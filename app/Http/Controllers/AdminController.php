<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sneeze;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $users = User::withCount(['sneezes'])
            ->withSum('sneezes', 'count')
            ->orderBy('sneezes_sum_count', 'desc')
            ->get();

        $totalUsers = $users->count();
        $totalSneezes = DB::table('sneezes')->sum('count');
        $totalEvents = DB::table('sneezes')->count();
        $avgSneezesPerUser = $totalUsers > 0 ? round($totalSneezes / $totalUsers, 1) : 0;

        // Get current database
        $currentDb = $this->getCurrentDatabase();

        return view('admin.index', compact('users', 'totalUsers', 'totalSneezes', 'totalEvents', 'avgSneezesPerUser', 'currentDb'));
    }

    /**
     * Display all sneezes in the database.
     */
    public function sneezes()
    {
        $sneezes = Sneeze::with('user')
            ->orderBy('sneeze_date', 'desc')
            ->orderBy('sneeze_time', 'desc')
            ->paginate(50);

        return view('admin.sneezes', compact('sneezes'));
    }

    /**
     * Toggle admin status for a user.
     */
    public function toggleAdmin(User $user)
    {
        // Prevent removing admin status from yourself if you're the only admin
        if ($user->is_admin && $user->id === auth()->id()) {
            $adminCount = User::where('is_admin', true)->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.index')
                    ->with('error', __('messages.admin.cannot_remove_admin'));
            }
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return redirect()->route('admin.index')
            ->with('success', __('messages.admin.admin_status_updated', ['name' => $user->name]));
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.index')
                ->with('error', __('messages.admin.cannot_delete_self'));
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.index')
            ->with('success', __('messages.admin.user_deleted', ['name' => $userName]));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function editUser(User $user)
    {
        return view('admin.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        // Prevent removing admin status from the last admin
        if ($user->is_admin && !$request->has('is_admin')) {
            $adminCount = User::where('is_admin', true)->count();
            if ($adminCount <= 1) {
                return redirect()->back()
                    ->with('error', __('messages.admin.cannot_remove_last_admin'))
                    ->withInput();
            }
        }

        // Prevent users from removing their own admin status
        if ($user->id === auth()->id() && $user->is_admin && !$request->has('is_admin')) {
            return redirect()->back()
                ->with('error', __('messages.admin.cannot_remove_own_admin'))
                ->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = $request->has('is_admin') && $request->is_admin;
        $user->save();

        return redirect()->route('admin.index')
            ->with('success', __('messages.admin.user_updated', ['name' => $user->name]));
    }

    /**
     * Switch between production and test database.
     */
    public function switchDatabase(Request $request)
    {
        $database = $request->input('database');
        
        // Validate the database name
        if (!in_array($database, ['database.sqlite', 'testdatabase.sqlite'])) {
            return redirect()->route('admin.index')
                ->with('error', 'Invalid database selected.');
        }

        // Update .env file
        $envPath = base_path('.env');
        
        if (file_exists($envPath)) {
            $env = file_get_contents($envPath);
            
            // Update or add DB_DATABASE line
            $dbPath = database_path($database);
            
            if (preg_match('/^DB_DATABASE=.*$/m', $env)) {
                $env = preg_replace(
                    '/^DB_DATABASE=.*$/m',
                    'DB_DATABASE=' . $dbPath,
                    $env
                );
            } else {
                $env .= "\nDB_DATABASE=" . $dbPath;
            }
            
            file_put_contents($envPath, $env);
            
            // Clear config cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            
            return redirect()->route('admin.index')
                ->with('success', 'Database switched to ' . $database . '. Page will reload.');
        }
        
        return redirect()->route('admin.index')
            ->with('error', 'Could not update .env file.');
    }

    /**
     * Get the current database filename.
     */
    private function getCurrentDatabase()
    {
        $dbPath = config('database.connections.sqlite.database');
        return basename($dbPath);
    }
}
