<?php

namespace App\Console\Commands;

use App\Models\Sneeze;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ImportSneezeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sneeze:import {file} {--user= : User ID to assign sneezes to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import sneeze data from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $userId = $this->option('user');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        // Get user
        if (!$userId) {
            $users = User::all();
            if ($users->count() === 1) {
                $user = $users->first();
                $this->info("Using user: {$user->name} (ID: {$user->id})");
            } else {
                $this->error("Multiple users found. Please specify --user=ID");
                $this->table(['ID', 'Name', 'Email'], $users->map(fn($u) => [$u->id, $u->name, $u->email]));
                return 1;
            }
        } else {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User not found with ID: {$userId}");
                return 1;
            }
        }

        // Dutch month names mapping
        $dutchMonths = [
            'januari' => 1,
            'februari' => 2,
            'maart' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'augustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'december' => 12,
        ];

        $imported = 0;
        $skipped = 0;
        $errors = 0;

        // Open and read CSV
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Skip header row
            $header = fgetcsv($handle);
            
            $this->info("Importing sneezes for user: {$user->name}");
            $progressBar = $this->output->createProgressBar();

            while (($data = fgetcsv($handle)) !== false) {
                try {
                    // Parse CSV columns: ID, Date, Time, Sneeze Count, Location, Latitude, Longitude, Remarks, Created At
                    $dateStr = $data[1]; // e.g., "2 januari"
                    $timeStr = $data[2]; // e.g., "02:00"
                    $count = (int)$data[3];
                    $location = $data[4] ?: null;
                    $latitude = !empty($data[5]) ? (float)$data[5] : null;
                    $longitude = !empty($data[6]) ? (float)$data[6] : null;
                    $notes = $data[7] ?: null;

                    // Parse Dutch date format "2 januari" - assuming current year (2026)
                    preg_match('/(\d+)\s+(\w+)/', $dateStr, $matches);
                    if (count($matches) === 3) {
                        $day = (int)$matches[1];
                        $monthName = strtolower($matches[2]);
                        $month = $dutchMonths[$monthName] ?? null;
                        
                        if ($month) {
                            // Create datetime - using 2026 as the year
                            $year = 2026;
                            $sneezedAt = Carbon::create($year, $month, $day)->setTimeFromTimeString($timeStr);
                            
                            // Create sneeze record
                            Sneeze::create([
                                'user_id' => $user->id,
                                'sneezed_at' => $sneezedAt,
                                'count' => $count,
                                'location' => $location,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'notes' => $notes,
                            ]);
                            
                            $imported++;
                        } else {
                            $this->warn("Could not parse month: {$monthName}");
                            $skipped++;
                        }
                    } else {
                        $this->warn("Could not parse date: {$dateStr}");
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $this->error("Error importing row: " . $e->getMessage());
                    $errors++;
                }
                
                $progressBar->advance();
            }

            fclose($handle);
            $progressBar->finish();
            $this->newLine();
        }

        $this->info("Import completed!");
        $this->table(['Status', 'Count'], [
            ['Imported', $imported],
            ['Skipped', $skipped],
            ['Errors', $errors],
        ]);

        return 0;
    }
}
