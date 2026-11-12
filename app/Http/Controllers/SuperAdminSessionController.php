<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminSessionController extends Controller
{
    public function index($role = 'resident')
    {
        abort_unless(
            in_array($role, ['resident', 'admin', 'super_admin']),
            404
        );

        $lifetime = config('session.lifetime', 120);
        $cutoff = now()->subMinutes($lifetime)->timestamp;

        $sessions = DB::table('sessions')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->where('users.role', $role)
            ->where('sessions.last_activity', '>=', $cutoff)
            ->select(
                'sessions.id as session_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.id as user_id',
                'users.name',
                'users.email',
                'users.role'
            )
            ->orderByDesc('sessions.last_activity')
            ->paginate(20);

        return view('super-admin.sessions.index', compact(
            'sessions',
            'role'
        ));
    }

    public function destroy(Request $request, $sessionId)
    {
        $session = DB::table('sessions')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->where('sessions.id', $sessionId)
            ->whereIn('users.role', [
                'resident',
                'admin',
                'super_admin'
            ])
            ->select('sessions.id', 'sessions.user_id')
            ->first();

        abort_if(!$session, 404);

        // Never terminate your own current session here.
        if ($session->id === $request->session()->getId()) {
            return back()->with(
                'error',
                'You cannot terminate your current session.'
            );
        }

        DB::table('sessions')
            ->where('id', $session->id)
            ->delete();

        return back()->with(
            'success',
            'User session terminated successfully.'
        );
    }
}
