<?php

namespace App\Http\Controllers;

use App\Models\Sneeze;
use App\Models\User;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SneezeController extends Controller
{
    /**
     * Display the public leaderboard.
     */
    public function leaderboard(Request $request)
    {
        $type = $request->input('type', 'all');
        $period = $request->input('period');
        $navigate = $request->input('navigate');
        
        // Handle navigation
        if ($navigate && $period) {
            if ($type === 'monthly') {
                $currentPeriod = \Carbon\Carbon::parse($period);
                $newPeriod = $navigate == 1 ? $currentPeriod->addMonth() : $currentPeriod->subMonth();
                $period = $newPeriod->format('Y-m-01');
            } else if ($type === 'daily') {
                $currentPeriod = \Carbon\Carbon::parse($period);
                $newPeriod = $navigate == 1 ? $currentPeriod->addDay() : $currentPeriod->subDay();
                $period = $newPeriod->format('Y-m-d');
            }
        }
        
        // Set default period if not provided
        if (!$period) {
            if ($type === 'monthly') {
                $period = now()->format('Y-m-01');
            } else if ($type === 'daily') {
                $period = now()->format('Y-m-d');
            }
        }
        
        // Build the query based on type
        $query = User::select('users.id', 'users.name')
            ->where('users.show_in_leaderboard', true)
            ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id');
        
        if ($type === 'monthly') {
            $query->whereYear('sneezes.sneeze_date', '=', \Carbon\Carbon::parse($period)->year)
                  ->whereMonth('sneezes.sneeze_date', '=', \Carbon\Carbon::parse($period)->month);
        } else if ($type === 'daily') {
            $query->whereDate('sneezes.sneeze_date', '=', $period);
        }
        
        $leaderboard = $query->groupBy('users.id', 'users.name')
            ->orderByRaw('SUM(COALESCE(sneezes.count, 0)) DESC')
            ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
            ->having('sneeze_count', '>', 0)
            ->get();
        
        // Handle AJAX requests for navigation
        if ($request->ajax()) {
            $periodLabel = '';
            $disableNext = false;
            
            if ($type === 'monthly') {
                $periodLabel = \Carbon\Carbon::parse($period)->format('F Y');
                $disableNext = \Carbon\Carbon::parse($period)->isCurrentMonth() || \Carbon\Carbon::parse($period)->isFuture();
            } else if ($type === 'daily') {
                $periodLabel = \Carbon\Carbon::parse($period)->format('F j, Y');
                $disableNext = \Carbon\Carbon::parse($period)->isToday() || \Carbon\Carbon::parse($period)->isFuture();
            }
            
            return response()->json([
                'leaderboard' => $leaderboard->map(function($user) {
                    return [
                        'name' => \Illuminate\Support\Str::limit($user->name, 15),
                        'sneeze_count' => $user->sneeze_count
                    ];
                }),
                'period' => $period,
                'periodLabel' => $periodLabel,
                'disableNext' => $disableNext
            ]);
        }
        
        return view('leaderboard', compact('leaderboard', 'type', 'period'));
    }

    /**
     * Display the user's sneeze history.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get all sneezes for calculations and display
        $allSneezes = $user->sneezes()->orderBy('sneeze_date', 'desc')->orderBy('sneeze_time', 'desc')->get();
        
        // Use all sneezes for display (DataTables will handle pagination)
        $sneezes = $allSneezes;

        // Summary Statistics
        $totalSneezes = $allSneezes->sum('count');
        $totalEvents = $allSneezes->count();
        
        // Calculate daily statistics
        $dailyData = $allSneezes->groupBy(function($sneeze) {
            return $sneeze->sneeze_date->format('Y-m-d');
        })->map(function($group) {
            return $group->sum('count');
        });
        
        $avgPerDay = $dailyData->count() > 0 ? round($dailyData->sum() / $dailyData->count(), 1) : 0;
        $peakDay = $dailyData->max() ?? 0;
        $peakDayDate = $dailyData->count() > 0 ? $dailyData->search($peakDay) : null;
        $peakDayFormatted = $peakDayDate ? DateHelper::formatLocalized(\Carbon\Carbon::parse($peakDayDate)) : 'N/A';
        
        // Monthly Statistics - All Time
        $monthlyDataAllTime = $allSneezes->groupBy(function($sneeze) {
            return $sneeze->sneeze_date->format('F Y');
        })->map(function($group) {
            return $group->sum('count');
        });
        
        // Monthly Statistics - This Year
        $currentYear = now()->year;
        $monthlyDataThisYear = $allSneezes
            ->filter(function($sneeze) use ($currentYear) {
                return $sneeze->sneeze_date->year === $currentYear;
            })
            ->groupBy(function($sneeze) {
                return $sneeze->sneeze_date->format('F');
            })
            ->map(function($group) {
                return $group->sum('count');
            });
        
        // For backward compatibility, use all time data
        $monthlyData = $monthlyDataAllTime;
        
        // Exclude current month from average if it's not complete
        $currentMonth = now()->format('F Y');
        $completeMonths = $monthlyData->except($currentMonth);
        
        $totalMonths = $completeMonths->count();
        $avgPerMonth = $totalMonths > 0 ? round($completeMonths->sum() / $totalMonths, 1) : 0;
        $peakMonthCount = $monthlyData->max() ?? 0;
        $peakMonth = $monthlyData->count() > 0 ? $monthlyData->search($peakMonthCount) : 'N/A';
        // Format the peak month with translated names
        $peakMonthFormatted = $peakMonth !== 'N/A' ? DateHelper::formatLocalized(\Carbon\Carbon::parse($peakMonth), 'F Y') : 'N/A';
        
        // Hourly statistics - All Time
        $hourExpression = DB::getDriverName() === 'sqlite' 
            ? 'CAST(strftime("%H", sneeze_time) AS INTEGER)' 
            : 'HOUR(sneeze_time)';
            
        $hourlyStatsAllTime = $user->sneezes()
            ->selectRaw("$hourExpression as hour, SUM(count) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('total', 'hour');

        // Fill in missing hours with 0
        $hourlyCountsAllTime = collect(range(0, 23))->mapWithKeys(function ($hour) use ($hourlyStatsAllTime) {
            return [$hour => $hourlyStatsAllTime->get($hour, 0)];
        });
        
        // Hourly statistics - This Year
        $hourlyStatsThisYear = $user->sneezes()
            ->whereYear('sneeze_date', $currentYear)
            ->selectRaw("$hourExpression as hour, SUM(count) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('total', 'hour');

        // Fill in missing hours with 0
        $hourlyCountsThisYear = collect(range(0, 23))->mapWithKeys(function ($hour) use ($hourlyStatsThisYear) {
            return [$hour => $hourlyStatsThisYear->get($hour, 0)];
        });
        
        // For backward compatibility
        $hourlyCounts = $hourlyCountsAllTime;

        // Daily statistics for multiple time periods (7, 30, 90 days)
        $getDailyStats = function($days) use ($user) {
            $stats = $user->sneezes()
                ->where('sneeze_date', '>=', now()->subDays($days))
                ->selectRaw('DATE(sneeze_date) as date, SUM(count) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->date => $item->total];
                });

            return collect(range(0, $days - 1))->mapWithKeys(function ($daysAgo) use ($stats, $days) {
                $date = now()->subDays($days - 1 - $daysAgo)->format('Y-m-d');
                return [$date => $stats->get($date, 0)];
            });
        };

        $dailyCounts7 = $getDailyStats(7);
        $dailyCounts30 = $getDailyStats(30);
        $dailyCounts90 = $getDailyStats(90);
        
        // Default to 30 days for backward compatibility
        $dailyCounts = $dailyCounts30;
        
        // Day of week statistics - All Time
        $dayOfWeekStatsAllTime = $allSneezes->groupBy(function($sneeze) {
            return $sneeze->sneeze_date->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        })->map(function($group) {
            return $group->sum('count');
        });
        
        $dayOfWeekCountsAllTime = collect(range(0, 6))->mapWithKeys(function ($day) use ($dayOfWeekStatsAllTime) {
            return [$day => $dayOfWeekStatsAllTime->get($day, 0)];
        });
        
        // Day of week statistics - This Year
        $dayOfWeekStatsThisYear = $allSneezes
            ->filter(function($sneeze) use ($currentYear) {
                return $sneeze->sneeze_date->year === $currentYear;
            })
            ->groupBy(function($sneeze) {
                return $sneeze->sneeze_date->dayOfWeek;
            })
            ->map(function($group) {
                return $group->sum('count');
            });
        
        $dayOfWeekCountsThisYear = collect(range(0, 6))->mapWithKeys(function ($day) use ($dayOfWeekStatsThisYear) {
            return [$day => $dayOfWeekStatsThisYear->get($day, 0)];
        });
        
        // For backward compatibility
        $dayOfWeekCounts = $dayOfWeekCountsAllTime;
        
        // Helper function to process location statistics
        $getLocationStats = function($sneezes) {
            return $sneezes
                ->groupBy(function($sneeze) {
                    return $sneeze->location ? strtolower(trim($sneeze->location)) : null;
                })
                ->filter(function($group, $key) {
                    return $key !== null && $key !== '';
                })
                ->map(function($group, $key) {
                    // Use the most common casing for display
                    $locationNames = $group->pluck('location')->filter()->countBy();
                    $displayName = $locationNames->sortDesc()->keys()->first();
                    return [
                        'name' => $displayName,
                        'count' => $group->sum('count')
                    ];
                })
                ->sortByDesc('count')
                ->take(10)
                ->mapWithKeys(function($item) {
                    return [$item['name'] => $item['count']];
                });
        };
        
        // Location statistics (top 10) - All Time
        $locationStatsAllTime = $getLocationStats($allSneezes);
        
        // Location statistics (top 10) - This Year
        $sneezesThisYear = $allSneezes->filter(function($sneeze) use ($currentYear) {
            return $sneeze->sneeze_date->year === $currentYear;
        });
        $locationStatsThisYear = $getLocationStats($sneezesThisYear);
        
        // For backward compatibility
        $locationStats = $locationStatsAllTime;
        
        // Helper function to process heatmap data
        $getHeatmapData = function($sneezes) {
            return $sneezes
                ->filter(function($sneeze) {
                    return $sneeze->latitude !== null && $sneeze->longitude !== null;
                })
                ->groupBy(function($sneeze) {
                    // Group by coordinates rounded to 4 decimal places (about 11m precision)
                    return round($sneeze->latitude, 4) . ',' . round($sneeze->longitude, 4);
                })
                ->map(function($group) {
                    $first = $group->first();
                    return [
                        'lat' => round($first->latitude, 4),
                        'lng' => round($first->longitude, 4),
                        'count' => $group->sum('count'), // Sum all sneeze counts at this location
                        'events' => $group->count(), // Number of separate sneeze events
                        'location' => $first->location
                    ];
                })
                ->values();
        };
        
        // Heatmap data (sneezes with coordinates) - All Time
        $heatmapDataAllTime = $getHeatmapData($allSneezes);
        
        // Heatmap data - This Year
        $heatmapDataThisYear = $getHeatmapData($sneezesThisYear);
        
        // For backward compatibility
        $heatmapData = $heatmapDataAllTime;
        
        // Most recent sneeze
        $lastSneeze = $allSneezes->first();
        $lastSneezeFormatted = $lastSneeze 
            ? $lastSneeze->sneeze_date->format('d/m/Y') . ', ' . substr($lastSneeze->sneeze_time, 0, 5)
            : 'No sneezes yet';

        return view('dashboard', compact(
            'sneezes', 
            'totalSneezes',
            'totalEvents',
            'avgPerDay',
            'peakDay',
            'peakDayFormatted',
            'totalMonths',
            'avgPerMonth',
            'peakMonth',
            'peakMonthFormatted',
            'peakMonthCount',
            'hourlyCounts',
            'hourlyCountsThisYear',
            'hourlyCountsAllTime',
            'dailyCounts',
            'dailyCounts7',
            'dailyCounts30',
            'dailyCounts90',
            'monthlyData',
            'monthlyDataThisYear',
            'monthlyDataAllTime',
            'dayOfWeekCounts',
            'dayOfWeekCountsThisYear',
            'dayOfWeekCountsAllTime',
            'locationStats',
            'locationStatsThisYear',
            'locationStatsAllTime',
            'heatmapData',
            'heatmapDataThisYear',
            'heatmapDataAllTime',
            'lastSneezeFormatted'
        ));
    }

    /**
     * Store a new sneeze record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sneeze_date' => 'nullable|date',
            'sneeze_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'count' => 'nullable|integer|min:1|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Use separate date and time fields
        $date = $validated['sneeze_date'] ?? now()->format('Y-m-d');
        $time = $validated['sneeze_time'] ?? now()->format('H:i');

        $sneeze = auth()->user()->sneezes()->create([
            'sneeze_date' => $date,
            'sneeze_time' => $time,
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'count' => $validated['count'] ?? 1,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Check if this is from quick-sneeze page
        if ($request->input('redirect_to') === 'dashboard') {
            return redirect()->route('dashboard')
                ->with('success', __('messages.dashboard.sneeze_recorded'));
        }

        return redirect()->back()
            ->with('success', __('messages.dashboard.sneeze_recorded'));
    }

    /**
     * Update an existing sneeze record.
     */
    public function update(Request $request, Sneeze $sneeze)
    {
        // Allow if user owns this sneeze OR user is admin
        if ($sneeze->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'sneeze_date' => 'nullable|date',
            'sneeze_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'count' => 'nullable|integer|min:1|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $sneeze->update([
            'sneeze_date' => $validated['sneeze_date'] ?? $sneeze->sneeze_date,
            'sneeze_time' => $validated['sneeze_time'] ?? $sneeze->sneeze_time,
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'count' => $validated['count'] ?? $sneeze->count,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Redirect based on referrer
        $redirectRoute = request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'admin') 
            ? 'admin.sneezes' 
            : 'dashboard';

        return redirect()->route($redirectRoute)
            ->with('success', __('messages.dashboard.sneeze_updated'));
    }

    /**
     * Remove a sneeze record.
     */
    public function destroy(Sneeze $sneeze)
    {
        // Allow if user owns this sneeze OR user is admin
        if ($sneeze->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $sneeze->delete();

        // Redirect based on referrer
        $redirectRoute = request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'admin') 
            ? 'admin.sneezes' 
            : 'dashboard';

        return redirect()->route($redirectRoute)
            ->with('success', __('messages.dashboard.sneeze_deleted'));
    }

    /**
     * Export user's sneezes to CSV.
     */
    public function export()
    {
        $user = auth()->user();
        $sneezes = $user->sneezes()
            ->orderBy('sneeze_date', 'desc')
            ->orderBy('sneeze_time', 'desc')
            ->get();

        $filename = 'sneezes_' . $user->name . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($sneezes) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, ['Date', 'Time', 'Count', 'Location', 'Latitude', 'Longitude', 'Notes']);
            
            // Data rows
            foreach ($sneezes as $sneeze) {
                fputcsv($file, [
                    $sneeze->sneeze_date->format('Y-m-d'),
                    $sneeze->sneeze_time,
                    $sneeze->count,
                    $sneeze->location ?? '',
                    $sneeze->latitude ?? '',
                    $sneeze->longitude ?? '',
                    $sneeze->notes ?? '',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display details for a specific day.
     */
    public function dailyDetails($date)
    {
        try {
            $date = \Carbon\Carbon::parse($date);
        } catch (\Exception $e) {
            abort(404);
        }

        // General stats for the day
        $totalSneezes = Sneeze::whereDate('sneeze_date', $date)->sum('count');
        $totalUsers = Sneeze::whereDate('sneeze_date', $date)->distinct('user_id')->count('user_id');
        $totalEvents = Sneeze::whereDate('sneeze_date', $date)->count();
        $generalStats = [
            'active_users' => $totalUsers,
            'total_sneezes' => $totalSneezes,
            'total_events' => $totalEvents,
        ];

        // Hourly distribution
        $hourlyData = Sneeze::whereDate('sneeze_date', $date)
            ->selectRaw('CAST(substr(sneeze_time, 1, 2) AS INTEGER) as hour, SUM(count) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        // Fill in missing hours with 0
        $hourlyDataComplete = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyDataComplete[$i] = $hourlyData[$i] ?? 0;
        }

        // User hourly distribution (if authenticated)
        $userHourlyDataComplete = [];
        if (auth()->check()) {
            $userHourlyData = auth()->user()->sneezes()
                ->whereDate('sneeze_date', $date)
                ->selectRaw('CAST(substr(sneeze_time, 1, 2) AS INTEGER) as hour, SUM(count) as total')
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('total', 'hour')
                ->toArray();

            // Fill in missing hours with 0
            for ($i = 0; $i < 24; $i++) {
                $userHourlyDataComplete[$i] = $userHourlyData[$i] ?? 0;
            }
        }

        // Top sneezers for the day
        $topSneezers = User::select('users.id', 'users.name')
            ->where('users.show_in_leaderboard', true)
            ->join('sneezes', 'users.id', '=', 'sneezes.user_id')
            ->whereDate('sneezes.sneeze_date', $date)
            ->groupBy('users.id', 'users.name')
            ->selectRaw('SUM(sneezes.count) as sneeze_count')
            ->orderByDesc('sneeze_count')
            ->limit(10)
            ->get();

        // User-specific stats (if authenticated)
        $userStats = null;
        $userSneezes = collect();
        $userHeatmapData = [];
        if (auth()->check()) {
            $userSneezes = auth()->user()->sneezes()
                ->whereDate('sneeze_date', $date)
                ->orderBy('sneeze_time')
                ->get();

            $userStats = [
                'total_count' => $userSneezes->sum('count'),
                'total_events' => $userSneezes->count(),
                'first_sneeze' => $userSneezes->first()?->sneeze_time,
                'last_sneeze' => $userSneezes->last()?->sneeze_time,
            ];

            // Get user's location data for heatmap
            $userHeatmapData = auth()->user()->sneezes()
                ->whereDate('sneeze_date', $date)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->map(function ($sneeze) {
                    return [
                        'lat' => $sneeze->latitude,
                        'lng' => $sneeze->longitude,
                        'count' => $sneeze->count
                    ];
                })
                ->toArray();
        }

        // Get all users' location data for heatmap
        $allHeatmapData = Sneeze::whereDate('sneeze_date', $date)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($sneeze) {
                return [
                    'lat' => $sneeze->latitude,
                    'lng' => $sneeze->longitude,
                    'count' => $sneeze->count
                ];
            })
            ->toArray();

        return view('daily-details', compact(
            'date',
            'totalSneezes',
            'totalUsers',
            'totalEvents',
            'hourlyDataComplete',
            'userHourlyDataComplete',
            'topSneezers',
            'userStats',
            'userSneezes',
            'generalStats',
            'userHeatmapData',
            'allHeatmapData'
        ));
    }
}
