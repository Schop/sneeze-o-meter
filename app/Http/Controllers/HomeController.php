<?php

namespace App\Http\Controllers;

use App\Models\Sneeze;
use App\Models\User;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get selected date from query parameter, default to yesterday
        $selectedDate = $request->query('date', now()->subDay()->toDateString());
        
        // Validate and parse the date
        try {
            $selectedDate = Carbon::parse($selectedDate)->toDateString();
        } catch (\Exception $e) {
            $selectedDate = now()->subDay()->toDateString();
        }
        
        // Get selected month from query parameter, default to current month
        $selectedMonth = $request->query('month', now()->format('Y-m'));
        
        // Validate and parse the month
        try {
            $selectedMonth = Carbon::parse($selectedMonth . '-01')->format('Y-m');
        } catch (\Exception $e) {
            $selectedMonth = now()->format('Y-m');
        }
        
        // General statistics
        $totalUsers = User::count();
        $totalSneezes = Sneeze::sum('count');
        $totalEvents = Sneeze::count();
        
        // Overall top 5 sneezers
        $topSneezers = User::select('users.id', 'users.name')
            ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
            ->where('users.show_in_leaderboard', true)
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('SUM(COALESCE(sneezes.count, 0)) DESC')
            ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
            ->limit(5)
            ->get();
        
        // Today's top sneezer (leader only)
        $todayTopSneezers = User::select('users.id', 'users.name', 'users.profile_picture')
            ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
            ->where('users.show_in_leaderboard', true)
            ->whereDate('sneezes.sneeze_date', now()->toDateString())
            ->groupBy('users.id', 'users.name', 'users.profile_picture')
            ->orderByRaw('SUM(COALESCE(sneezes.count, 0)) DESC')
            ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
            ->limit(1)
            ->get();
        
        // Yesterday's top 5 sneezers (or selected date)
        $yesterdayTopSneezers = User::select('users.id', 'users.name')
            ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
            ->where('users.show_in_leaderboard', true)
            ->whereDate('sneezes.sneeze_date', $selectedDate)
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('SUM(COALESCE(sneezes.count, 0)) DESC')
            ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
            ->limit(5)
            ->get();
        
        // This month's top 5 sneezers (or selected month)
        $selectedMonthCarbon = Carbon::parse($selectedMonth . '-01');
        $monthTopSneezers = User::select('users.id', 'users.name')
            ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
            ->where('users.show_in_leaderboard', true)
            ->whereYear('sneezes.sneeze_date', $selectedMonthCarbon->year)
            ->whereMonth('sneezes.sneeze_date', $selectedMonthCarbon->month)
            ->groupBy('users.id', 'users.name')
            ->orderByRaw('SUM(COALESCE(sneezes.count, 0)) DESC')
            ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
            ->limit(5)
            ->get();
        
        // Get current user's stats if logged in
        $currentUserOverall = null;
        $currentUserYesterday = null;
        $currentUserMonth = null;
        
        if (auth()->check()) {
            $userId = auth()->id();
            $userOptedIn = auth()->user()->show_in_leaderboard;
            
            // Check if user is not in top 5 overall and has opted in
            if ($userOptedIn && !$topSneezers->contains('id', $userId)) {
                $currentUserOverall = User::select('users.id', 'users.name')
                    ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                    ->where('users.id', $userId)
                    ->groupBy('users.id', 'users.name')
                    ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
                    ->first();
                
                // Calculate rank
                if ($currentUserOverall) {
                    $currentUserOverall->rank = User::select('users.id')
                        ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                        ->where('users.show_in_leaderboard', true)
                        ->groupBy('users.id')
                        ->havingRaw('SUM(COALESCE(sneezes.count, 0)) > ?', [$currentUserOverall->sneeze_count])
                        ->count() + 1;
                }
            }
            
            // Check if user is not in top 5 today and has opted in
            // Removed - no longer needed since we only show the top leader
            
            // Check if user is not in top 5 yesterday and has opted in
            if ($userOptedIn && !$yesterdayTopSneezers->contains('id', $userId)) {
                $currentUserYesterday = User::select('users.id', 'users.name')
                    ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                    ->whereDate('sneezes.sneeze_date', $selectedDate)
                    ->where('users.id', $userId)
                    ->groupBy('users.id', 'users.name')
                    ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
                    ->first();
                
                // Calculate rank
                if ($currentUserYesterday) {
                    $currentUserYesterday->rank = User::select('users.id')
                        ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                        ->where('users.show_in_leaderboard', true)
                        ->whereDate('sneezes.sneeze_date', $selectedDate)
                        ->groupBy('users.id')
                        ->havingRaw('SUM(COALESCE(sneezes.count, 0)) > ?', [$currentUserYesterday->sneeze_count])
                        ->count() + 1;
                }
            }
            
            // Check if user is not in top 5 this month and has opted in
            if ($userOptedIn && !$monthTopSneezers->contains('id', $userId)) {
                $currentUserMonth = User::select('users.id', 'users.name')
                    ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                    ->whereYear('sneezes.sneeze_date', $selectedMonthCarbon->year)
                    ->whereMonth('sneezes.sneeze_date', $selectedMonthCarbon->month)
                    ->where('users.id', $userId)
                    ->groupBy('users.id', 'users.name')
                    ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
                    ->first();
                
                // Calculate rank
                if ($currentUserMonth) {
                    $currentUserMonth->rank = User::select('users.id')
                        ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                        ->where('users.show_in_leaderboard', true)
                        ->whereYear('sneezes.sneeze_date', $selectedMonthCarbon->year)
                        ->whereMonth('sneezes.sneeze_date', $selectedMonthCarbon->month)
                        ->groupBy('users.id')
                        ->havingRaw('SUM(COALESCE(sneezes.count, 0)) > ?', [$currentUserMonth->sneeze_count])
                        ->count() + 1;
                }
            }
        }
        
        // Top sneeze day
        $topDay = Sneeze::selectRaw('sneeze_date, SUM(count) as total')
            ->groupBy('sneeze_date')
            ->orderBy('total', 'desc')
            ->first();
        
        // Top sneeze month
        $monthFormat = DB::getDriverName() === 'sqlite' 
            ? "strftime('%Y-%m', sneeze_date)" 
            : "DATE_FORMAT(sneeze_date, '%Y-%m')";
            
        $topMonth = Sneeze::selectRaw("{$monthFormat} as month, SUM(count) as total")
            ->groupBy('month')
            ->orderBy('total', 'desc')
            ->first();
        
        // Daily trend - for home page chart
        $getDailyStats = function($days) {
            $stats = Sneeze::where('sneeze_date', '>=', now()->subDays($days))
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
        
        // Monthly trend - SQLite compatible
        $monthlyData = Sneeze::selectRaw("{$monthFormat} as month, SUM(count) as total")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->reverse();
        
        // Get sneeze locations for heatmap (grouped for privacy)
        $sneezeLocations = Sneeze::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function($sneeze) {
                // Round to 2 decimal places (~1.1km precision) for privacy
                return [
                    'lat' => round($sneeze->latitude, 2),
                    'lng' => round($sneeze->longitude, 2),
                    'count' => $sneeze->count
                ];
            })
            ->groupBy(function($item) {
                return $item['lat'] . ',' . $item['lng'];
            })
            ->map(function($group) {
                $first = $group->first();
                return [
                    'lat' => $first['lat'],
                    'lng' => $first['lng'],
                    'intensity' => $group->sum('count')
                ];
            })
            ->values();

        return view('home', compact(
            'totalUsers',
            'totalSneezes',
            'totalEvents',
            'topSneezers',
            'todayTopSneezers',
            'yesterdayTopSneezers',
            'monthTopSneezers',
            'currentUserOverall',
            'currentUserYesterday',
            'currentUserMonth',
            'topDay',
            'topMonth',
            'dailyCounts7',
            'dailyCounts30',
            'dailyCounts90',
            'monthlyData',
            'sneezeLocations',
            'selectedDate',
            'selectedMonth'
        ));
    }
    
    public function getLeaderboardData(Request $request)
    {
        $type = $request->query('type'); // 'day' or 'month'
        $date = $request->query('date');
        $month = $request->query('month');
        
        if ($type === 'day' && $date) {
            // Validate and parse the date
            try {
                $selectedDate = Carbon::parse($date)->toDateString();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date'], 400);
            }
            
            // Get top 5 sneezers for the date
            $topSneezers = User::select('users.id', 'users.name')
                ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                ->where('users.show_in_leaderboard', true)
                ->whereDate('sneezes.sneeze_date', $selectedDate)
                ->groupBy('users.id', 'users.name')
                ->orderByRaw('SUM(COALESCE(sneezes.count, 0)) DESC')
                ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
                ->limit(5)
                ->get();
            
            // Get current user's rank if logged in and opted in
            $currentUser = null;
            if (auth()->check() && auth()->user()->show_in_leaderboard) {
                $userId = auth()->id();
                if (!$topSneezers->contains('id', $userId)) {
                    $currentUser = User::select('users.id', 'users.name')
                        ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                        ->whereDate('sneezes.sneeze_date', $selectedDate)
                        ->where('users.id', $userId)
                        ->groupBy('users.id', 'users.name')
                        ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
                        ->first();
                    
                    if ($currentUser) {
                        $currentUser->rank = User::select('users.id')
                            ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                            ->where('users.show_in_leaderboard', true)
                            ->whereDate('sneezes.sneeze_date', $selectedDate)
                            ->groupBy('users.id')
                            ->havingRaw('SUM(COALESCE(sneezes.count, 0)) > ?', [$currentUser->sneeze_count])
                            ->count() + 1;
                    }
                }
            }
            
            return response()->json([
                'date' => $selectedDate,
                'formatted_date' => app()->getLocale() === 'nl' 
                    ? DateHelper::formatLocalized(Carbon::parse($selectedDate), 'j M Y')
                    : DateHelper::formatLocalized(Carbon::parse($selectedDate), 'M j, Y'),
                'top_sneezers' => $topSneezers->map(function($sneezer) {
                    $sneezer->name = \Illuminate\Support\Str::limit($sneezer->name, 15);
                    return $sneezer;
                }),
                'current_user' => $currentUser ? (function($user) {
                    $user->name = \Illuminate\Support\Str::limit($user->name, 15);
                    return $user;
                })($currentUser) : null,
                'is_today' => Carbon::parse($selectedDate)->isToday(),
                'is_future' => Carbon::parse($selectedDate)->isFuture()
            ]);
        }
        
        if ($type === 'month' && $month) {
            // Validate and parse the month
            try {
                $selectedMonth = Carbon::parse($month . '-01')->format('Y-m');
                $selectedMonthCarbon = Carbon::parse($selectedMonth . '-01');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid month'], 400);
            }
            
            // Get top 5 sneezers for the month
            $topSneezers = User::select('users.id', 'users.name')
                ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                ->where('users.show_in_leaderboard', true)
                ->whereYear('sneezes.sneeze_date', $selectedMonthCarbon->year)
                ->whereMonth('sneezes.sneeze_date', $selectedMonthCarbon->month)
                ->groupBy('users.id', 'users.name')
                ->orderByRaw('SUM(COALESCE(sneezes.count, 0)) DESC')
                ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
                ->limit(5)
                ->get();
            
            // Get current user's rank if logged in and opted in
            $currentUser = null;
            if (auth()->check() && auth()->user()->show_in_leaderboard) {
                $userId = auth()->id();
                if (!$topSneezers->contains('id', $userId)) {
                    $currentUser = User::select('users.id', 'users.name')
                        ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                        ->whereYear('sneezes.sneeze_date', $selectedMonthCarbon->year)
                        ->whereMonth('sneezes.sneeze_date', $selectedMonthCarbon->month)
                        ->where('users.id', $userId)
                        ->groupBy('users.id', 'users.name')
                        ->selectRaw('SUM(COALESCE(sneezes.count, 0)) as sneeze_count')
                        ->first();
                    
                    if ($currentUser) {
                        $currentUser->rank = User::select('users.id')
                            ->leftJoin('sneezes', 'users.id', '=', 'sneezes.user_id')
                            ->where('users.show_in_leaderboard', true)
                            ->whereYear('sneezes.sneeze_date', $selectedMonthCarbon->year)
                            ->whereMonth('sneezes.sneeze_date', $selectedMonthCarbon->month)
                            ->groupBy('users.id')
                            ->havingRaw('SUM(COALESCE(sneezes.count, 0)) > ?', [$currentUser->sneeze_count])
                            ->count() + 1;
                    }
                }
            }
            
            return response()->json([
                'month' => $selectedMonth,
                'formatted_month' => DateHelper::formatLocalized($selectedMonthCarbon, 'F Y'),
                'top_sneezers' => $topSneezers->map(function($sneezer) {
                    $sneezer->name = \Illuminate\Support\Str::limit($sneezer->name, 15);
                    return $sneezer;
                }),
                'current_user' => $currentUser ? (function($user) {
                    $user->name = \Illuminate\Support\Str::limit($user->name, 15);
                    return $user;
                })($currentUser) : null,
                'is_current_month' => $selectedMonthCarbon->isCurrentMonth(),
                'is_future' => $selectedMonthCarbon->isFuture()
            ]);
        }
        
        return response()->json(['error' => 'Invalid request'], 400);
    }
}
