<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sneezes', function (Blueprint $table) {
            $table->date('sneeze_date')->nullable()->after('user_id');
            $table->time('sneeze_time')->nullable()->after('sneeze_date');
        });

        // Migrate existing data from sneezed_at to separate date and time columns
        DB::table('sneezes')->whereNotNull('sneezed_at')->get()->each(function ($sneeze) {
            DB::table('sneezes')
                ->where('id', $sneeze->id)
                ->update([
                    'sneeze_date' => date('Y-m-d', strtotime($sneeze->sneezed_at)),
                    'sneeze_time' => date('H:i:s', strtotime($sneeze->sneezed_at)),
                ]);
        });

        // Make the columns non-nullable now that data is migrated
        Schema::table('sneezes', function (Blueprint $table) {
            $table->date('sneeze_date')->nullable(false)->change();
            $table->time('sneeze_time')->nullable(false)->change();
        });

        // Drop the old column
        Schema::table('sneezes', function (Blueprint $table) {
            $table->dropColumn('sneezed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sneezes', function (Blueprint $table) {
            $table->timestamp('sneezed_at')->nullable()->after('user_id');
        });

        // Restore data from date and time columns back to sneezed_at
        DB::table('sneezes')->whereNotNull('sneeze_date')->get()->each(function ($sneeze) {
            DB::table('sneezes')
                ->where('id', $sneeze->id)
                ->update([
                    'sneezed_at' => $sneeze->sneeze_date . ' ' . $sneeze->sneeze_time,
                ]);
        });

        Schema::table('sneezes', function (Blueprint $table) {
            $table->dropColumn(['sneeze_date', 'sneeze_time']);
        });
    }
};
