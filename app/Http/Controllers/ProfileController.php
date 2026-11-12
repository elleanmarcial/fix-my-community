<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        $application = DB::table('admin_applications')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $barangays = DB::table('barangays')
            ->orderBy('name')
            ->get();

        return view('profile.edit', compact(
            'user',
            'application',
            'barangays'
        ));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    public function submitAdminApplication(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'resident', 403);

        $validated = $request->validate([
            'barangay_id' => ['required', 'integer', 'exists:barangays,id'],
            'position' => ['required', 'in:lead,member'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $existingApplication = DB::table('admin_applications')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending_review', 'approved'])
            ->exists();

        if ($existingApplication) {
            return back()->withErrors([
                'admin_application' => 'You already have a pending or approved application.',
            ]);
        }

        if (DB::table('barangay_members')
            ->where('user_id', $user->id)
            ->exists()) {
            return back()->withErrors([
                'admin_application' => 'You already belong to a barangay.',
            ]);
        }

        $proofPath = null;

        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')
                ->store('admin-applications', 'public');
        }

        DB::table('admin_applications')->insert([
            'user_id' => $user->id,
            'barangay_id' => $validated['barangay_id'],
            'position' => $validated['position'],
            'proof_path' => $proofPath,
            'status' => 'pending_review',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::route('profile.edit')
            ->with('admin_application_success', 'Your Barangay Admin application has been submitted successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
