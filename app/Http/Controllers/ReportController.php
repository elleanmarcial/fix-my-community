<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\BarangayLocator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('reports.index', compact('reports'));
    }

    public function create(): View
    {
        return view('reports.create');
    }

    public function store(
        Request $request,
        BarangayLocator $barangayLocator
    ): RedirectResponse {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'location_confirmed' => ['accepted'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        // Identify the barangay using the actual incident coordinates.
        $barangayId = $barangayLocator->findByCoordinates(
            (float) $validated['latitude'],
            (float) $validated['longitude']
        );

        if ($barangayId === null) {
            throw ValidationException::withMessages([
                'latitude' => 'The selected location is not within a recognized barangay. Please adjust your map pin.',
            ]);
        }

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request
                ->file('photo')
                ->store('reports', 'public');
        }

        unset(
            $validated['photo'],
            $validated['location_confirmed']
        );

        $validated['barangay_id'] = $barangayId;
        $validated['user_id'] = Auth::id();
        $validated['is_anonymous'] = $request->boolean('is_anonymous');
        $validated['status'] = 'Submitted';

        Report::create($validated);

        return redirect()
            ->route('reports.index')
            ->with('success', 'Your report has been submitted successfully.');
    }

    public function show(Report $report): View
    {
        abort_unless($report->user_id === Auth::id(), 403);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        abort_unless($report->user_id === Auth::id(), 403);

        return view('reports.edit', compact('report'));
    }

    public function update(
        Request $request,
        Report $report,
        BarangayLocator $barangayLocator
    ): RedirectResponse {
        abort_unless($report->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        // Recalculate the barangay whenever the report is updated.
        $barangayId = $barangayLocator->findByCoordinates(
            (float) $validated['latitude'],
            (float) $validated['longitude']
        );

        if ($barangayId === null) {
            throw ValidationException::withMessages([
                'latitude' => 'The selected location is not within a recognized barangay. Please adjust your map pin.',
            ]);
        }

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request
                ->file('photo')
                ->store('reports', 'public');
        }

        unset($validated['photo']);

        $validated['barangay_id'] = $barangayId;
        $validated['is_anonymous'] = $request->boolean('is_anonymous');

        $report->update($validated);

        return redirect()
            ->route('reports.index')
            ->with('success', 'Your report has been updated successfully.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        abort_unless($report->user_id === Auth::id(), 403);

        $report->delete();

        return redirect()
            ->route('reports.index')
            ->with('success', 'Your report has been deleted.');
    }
}