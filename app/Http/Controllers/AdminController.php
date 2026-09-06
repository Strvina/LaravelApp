<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Notification;
use App\Models\Products;
use App\Models\ToDo;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalProducts = Products::count();
        $monthlyRevenue = Expense::where('type', 'income')->whereYear('date', now()->year)->whereMonth('date', now()->month)->sum('amount');
        $monthlyExpense = Expense::where('type', 'expense')->whereYear('date', now()->year)->whereMonth('date', now()->month)->sum('amount');
        $activeTasks = ToDo::where('status', 'pending')->count();
        $completedTasksCount = ToDo::where('status', 'completed')->count();
        $lowStockCount = Products::lowStock()->count();
        $activityCount = ActivityLog::count();
        $netBalance = Expense::where('type', 'income')->sum('amount') - Expense::where('type', 'expense')->sum('amount');

        $userSignups = [];
        $expensesPerMonth = [];
        $completedTasks = [];

        $currentYear = now()->year;

        for ($i = 1; $i <= 12; $i++) {
            $userSignups[] = User::whereYear('created_at', $currentYear)->whereMonth('created_at', $i)->count();
            $expensesPerMonth[] = Expense::where('type', 'expense')->whereYear('date', $currentYear)->whereMonth('date', $i)->sum('amount');
            $completedTasks[] = ToDo::where('status', 'completed')->whereYear('updated_at', $currentYear)->whereMonth('updated_at', $i)->count();
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $notifications = Notification::where('user_id', auth()->id())->unread()->latest()->get();
        $recentActivities = ActivityLog::with('user')->latest()->take(8)->get();
        $recentUsers = User::latest()->take(5)->get();
        $recentProducts = Products::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'monthlyRevenue',
            'monthlyExpense',
            'activeTasks',
            'completedTasksCount',
            'lowStockCount',
            'activityCount',
            'netBalance',
            'userSignups',
            'expensesPerMonth',
            'completedTasks',
            'months',
            'notifications',
            'recentActivities',
            'recentUsers',
            'recentProducts'
        ));
    }

    public function activityLogs(Request $request)
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:50'],
            'model_type' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = ActivityLog::with('user')->latest();

        if ($filters['user_id'] ?? null) {
            $query->where('user_id', $filters['user_id']);
        }

        if ($filters['action'] ?? null) {
            $query->where('action', $filters['action']);
        }

        if ($filters['model_type'] ?? null) {
            $query->where('model_type', $filters['model_type']);
        }

        if ($filters['from'] ?? null) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if ($filters['to'] ?? null) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::distinct()->orderBy('action')->pluck('action')->filter();
        $modelTypes = ActivityLog::distinct()->orderBy('model_type')->pluck('model_type')->filter();

        return view('admin.activity-logs', compact('logs', 'users', 'actions', 'modelTypes', 'filters'));
    }
}
