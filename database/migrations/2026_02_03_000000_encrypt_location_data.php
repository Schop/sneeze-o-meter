<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all sneezes with latitude/longitude data
        $sneezes = DB::table('sneezes')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Encrypt each existing record
        foreach ($sneezes as $sneeze) {
            DB::table('sneezes')
                ->where('id', $sneeze->id)
                ->update([
                    'latitude' => encrypt($sneeze->latitude),
                    'longitude' => encrypt($sneeze->longitude),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all sneezes with latitude/longitude data
        $sneezes = DB::table('sneezes')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Decrypt each record
        foreach ($sneezes as $sneeze) {
            try {
                DB::table('sneezes')
                    ->where('id', $sneeze->id)
                    ->update([
                        'latitude' => decrypt($sneeze->latitude),
                        'longitude' => decrypt($sneeze->longitude),
                    ]);
            } catch (\Exception $e) {
                // If decryption fails, keep the encrypted values
                // This is a safety measure in case the key has changed
            }
        }
    }
};
