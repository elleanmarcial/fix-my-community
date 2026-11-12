<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::with('user')
            ->latest()
            ->get();

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report): View
    {
        $report->load([
            'user',
            'notes.author',
        ]);

        return view('admin.reports.show', compact('report'));
    }

    public function update(
        Request $request,
        Report $report
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:Submitted,Verified,In Progress,Resolved,Rejected',
            ],
            'assigned_to' => [
                'nullable',
                'string',
                'max:255',
            ],
            'new_note' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);

        $report->update([
            'status' => $validated['status'],
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        if ($validated['status'] === 'Resolved') {
            if (!$report->resolved_at) {
                $report->update([
                    'resolved_at' => now(),
                ]);
            }
        } elseif ($report->resolved_at) {
            $report->update([
                'resolved_at' => null,
            ]);
        }

        $newNote = trim($validated['new_note'] ?? '');

        if ($newNote !== '') {
            $report->notes()->create([
                'user_id' => $request->user()->id,
                'note' => $newNote,
            ]);

            // Keep the latest note in the existing field
            // for compatibility with older application views.
            $report->update([
                'admin_notes' => $newNote,
            ]);
        }

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('success', 'Report updated successfully.');
    }
}