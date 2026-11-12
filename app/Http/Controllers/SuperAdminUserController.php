<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role') &&
            in_array($request->role, ['resident', 'admin', 'super_admin'])) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

public function show($id)
{
    $user = User::findOrFail($id);

    $memberships = DB::table('barangay_members')
        ->join('barangays', 'barangays.id', '=', 'barangay_members.barangay_id')
        ->where('barangay_members.user_id', $id)
        ->select(
            'barangays.name as barangay_name',
            'barangays.city',
            'barangay_members.position',
            'barangay_members.status',
            'barangay_members.joined_at'
        )
        ->get();

    $reports = DB::table('reports')
        ->where('user_id', $id)
        ->orderByDesc('created_at')
        ->get();

    $applications = DB::table('admin_applications')
        ->join('barangays', 'barangays.id', '=', 'admin_applications.barangay_id')
        ->where('admin_applications.user_id', $id)
        ->select(
            'admin_applications.*',
            'barangays.name as barangay_name'
        )
        ->orderByDesc('admin_applications.created_at')
        ->get();

    return view('super-admin.users.show', compact(
        'user',
        'memberships',
        'reports',
        'applications'
    ));
}

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        abort_if($user->id === auth()->id(), 403,
            'You cannot disable your own account.');

        abort_if($user->role === 'super_admin', 403,
            'Super Admin accounts cannot be disabled here.');

        $user->is_active = !$user->is_active;
        $user->save();

        if (!$user->is_active) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        return back()->with(
            'success',
            $user->is_active
                ? 'Account reactivated successfully.'
                : 'Account disabled and active sessions terminated.'
        );
    }
}
