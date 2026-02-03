<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize sneeze_date to only contain dates (no time component)
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("UPDATE sneezes SET sneeze_date = DATE(sneeze_date)");
        } else {
            DB::statement("UPDATE sneezes SET sneeze_date = DATE(sneeze_date)");
        }
    }

    public function down(): void
    {
        // No need to reverse this - normalized data is fine
    }
};
