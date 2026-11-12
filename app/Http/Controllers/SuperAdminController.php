<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function index()
    {
        $applications = DB::table('admin_applications')
            ->join('users', 'users.id', '=', 'admin_applications.user_id')
            ->join('barangays', 'barangays.id', '=', 'admin_applications.barangay_id')
            ->select(
                'admin_applications.*',
                'users.name as applicant_name',
                'users.email',
                'barangays.name as barangay_name',
                'barangays.city as city'
            )
            ->orderByDesc('admin_applications.created_at')
            ->paginate(15);

        return view('super-admin.applications.index', compact('applications'));
    }

    public function show($id)
    {
        $application = DB::table('admin_applications')
            ->join('users', 'users.id', '=', 'admin_applications.user_id')
            ->join('barangays', 'barangays.id', '=', 'admin_applications.barangay_id')
            ->select(
                'admin_applications.*',
                'users.name as applicant_name',
                'users.email',
                'barangays.name as barangay_name',
                'barangays.city as city'
            )
            ->where('admin_applications.id', $id)
            ->first();

        abort_if(!$application, 404);

        return view('super-admin.applications.show', compact('application'));
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            $application = DB::table('admin_applications')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            abort_if(!$application, 404);

            abort_if(
                $application->status !== 'pending_review',
                403,
                'This application has already been reviewed.'
            );

            $user = DB::table('users')
                ->where('id', $application->user_id)
                ->lockForUpdate()
                ->first();

            abort_if(!$user || $user->role !== 'resident', 403);

            $existingMembership = DB::table('barangay_members')
                ->where('user_id', $user->id)
                ->exists();

            abort_if(
                $existingMembership,
                403,
                'This user already belongs to a barangay.'
            );

            if ($application->position === 'lead') {
                $barangay = DB::table('barangays')
                    ->where('id', $application->barangay_id)
                    ->lockForUpdate()
                    ->first();

                abort_if(
                    !$barangay || $barangay->lead_user_id,
                    403,
                    'This barangay already has a lead.'
                );
            }

            DB::table('barangay_members')->insert([
                'barangay_id' => $application->barangay_id,
                'user_id' => $user->id,
                'position' => $application->position,
                'status' => 'active',
                'joined_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($application->position === 'lead') {
                DB::table('barangays')
                    ->where('id', $application->barangay_id)
                    ->update([
                        'lead_user_id' => $user->id,
                        'updated_at' => now(),
                    ]);
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role' => 'admin',
                    'updated_at' => now(),
                ]);

            DB::table('admin_applications')
                ->where('id', $id)
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('super-admin.applications.index')
            ->with('success', 'Application approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:2000'],
        ]);

        $application = DB::table('admin_applications')
            ->where('id', $id)
            ->first();

        abort_if(!$application, 404);

        abort_if(
            $application->status !== 'pending_review',
            403,
            'This application has already been reviewed.'
        );

        DB::table('admin_applications')
            ->where('id', $id)
            ->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('super-admin.applications.index')
            ->with('success', 'Application rejected successfully.');
    }
}
