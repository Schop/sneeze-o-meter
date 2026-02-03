<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Sneeze;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fictional usernames
        $usernames = [
            'SneezeMaster',
            'AchooChampion',
            'PollePieter',
            'SniffleQueen',
            'GesundheitGuru',
            'AllergyAnna',
            'TissueKing',
            'SneezyMcSneezeface',
            'BlessYouBob',
            'DustyDave',
            'PepperNose',
            'SpringSniffer',
            'HayfeverHank',
            'NasalNancy',
            'SnottyScott',
            'AchooAdrian',
            'SniffleSteve',
            'TissueThomas',
            'PollenPatrick',
            'BlessedBetty'
        ];

        // Create 20 users
        $users = [];
        foreach ($usernames as $index => $username) {
            $users[] = User::create([
                'name' => $username,
                'email' => strtolower(str_replace(' ', '', $username)) . '@sneeze-tracker.test',
                'password' => bcrypt('password'),
            ]);
        }

        // Generate sneezes for the past year
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();

        // Location bounds for Netherlands (for realistic seeding)
        $locations = [
            ['name' => 'Amsterdam', 'lat' => 52.3676, 'lng' => 4.9041],
            ['name' => 'Rotterdam', 'lat' => 51.9225, 'lng' => 4.4792],
            ['name' => 'Utrecht', 'lat' => 52.0907, 'lng' => 5.1214],
            ['name' => 'Groningen', 'lat' => 53.2194, 'lng' => 6.5665],
            ['name' => 'Eindhoven', 'lat' => 51.4416, 'lng' => 5.4697],
        ];

        foreach ($users as $user) {
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Random chance of sneezing on this day (70% chance)
                if (rand(1, 100) <= 70) {
                    // Random number of sneeze events for this day (1-5 events)
                    $eventsCount = rand(1, 5);
                    
                    for ($i = 0; $i < $eventsCount; $i++) {
                        // 60% chance of having location data
                        $hasLocation = rand(1, 100) <= 60;
                        $location = null;
                        $latitude = null;
                        $longitude = null;

                        if ($hasLocation) {
                            $selectedLocation = $locations[array_rand($locations)];
                            $location = $selectedLocation['name'];
                            // Add small random variation to coordinates
                            $latitude = $selectedLocation['lat'] + (rand(-100, 100) / 10000);
                            $longitude = $selectedLocation['lng'] + (rand(-100, 100) / 10000);
                        }

                        Sneeze::create([
                            'user_id' => $user->id,
                            'sneeze_date' => $currentDate->toDateString(),
                            'sneeze_time' => sprintf('%02d:%02d:00', rand(6, 23), rand(0, 59)),
                            'count' => rand(1, 4),
                            'location' => $location,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'notes' => null,
                        ]);
                    }
                }
                
                $currentDate->addDay();
            }
        }

        $this->command->info('Successfully seeded 20 users with a year of sneeze data!');
    }
}
