<x-app-layout>
    <x-slot name="header">
        <div>
            <h1>Review Admin Application</h1>
            <p>Review and manage barangay admin applications.</p>
        </div>
    </x-slot>

    <div class="fmc-narrow application-review-page">

        <a href="{{ route('super-admin.applications.index') }}"
           class="application-back">
            ← Back to Applications
        </a>

        <div class="application-card">

            {{-- APPLICATION DETAILS --}}
            <h2>Application Details</h2>

            <div class="application-details">

                <div class="detail-item">
                    <span>Applicant Name</span>
                    <strong>{{ $application->applicant_name }}</strong>
                </div>

                <div class="detail-item">
                    <span>Email Address</span>
                    <strong>{{ $application->email }}</strong>
                </div>

                <div class="detail-item">
                    <span>Barangay</span>
                    <strong>{{ $application->barangay_name }}</strong>
                </div>

                <div class="detail-item">
                    <span>City / Municipality</span>
                    <strong>{{ $application->city ?: 'Not specified' }}</strong>
                </div>

                <div class="detail-item">
                    <span>Applied Position</span>
                    <strong>{{ ucfirst($application->position) }}</strong>
                </div>

                <div class="detail-item">
                    <span>Application Status</span>
                    <strong>
                        {{ ucwords(str_replace('_', ' ', $application->status)) }}
                    </strong>
                </div>

            </div>

            <div class="application-divider"></div>

            {{-- SUPPORTING DOCUMENT --}}
            <h3>Supporting Document</h3>

            @if ($application->proof_path)
                <a href="{{ asset('storage/' . $application->proof_path) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="proof-link">
                    <span>📄</span>
                    View Uploaded Proof
                </a>
            @else
                <p class="no-proof">
                    No supporting document uploaded.
                </p>
            @endif

            {{-- REVIEW NOTES --}}
            @if ($application->review_notes)
                <div class="existing-review-notes">
                    <h3>Review Notes</h3>
                    <p>{{ $application->review_notes }}</p>
                </div>
            @endif

            {{-- REVIEW DECISION --}}
            @if ($application->status === 'pending_review')

                <div class="application-divider"></div>

                <h3>Review Decision</h3>

                <div class="review-actions">

                    {{-- APPROVE FORM --}}
                    <form
                        method="POST"
                        action="{{ route('super-admin.applications.approve', $application->id) }}"
                        onsubmit="return confirm('Are you sure you want to approve this application?')"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="approve-button"
                            style="background:#087f5b !important;color:#ffffff !important;"
                        >
                            ✓ Approve Application
                        </button>
                    </form>

                    {{-- REJECT FORM --}}
                    <form
                        method="POST"
                        action="{{ route('super-admin.applications.reject', $application->id) }}"
                        class="reject-form"
                        onsubmit="return confirm('Are you sure you want to reject this application?')"
                    >
                        @csrf

                        <label for="review_notes">
                            Reason for Rejection
                        </label>

                        <textarea
                            id="review_notes"
                            name="review_notes"
                            rows="4"
                            required
                            maxlength="2000"
                            placeholder="Enter your reason for rejecting this application..."
                        >{{ old('review_notes') }}</textarea>

                        @error('review_notes')
                            <p class="review-error">{{ $message }}</p>
                        @enderror

                        <button
                            type="submit"
                            class="reject-button"
                            style="background:#dc2626 !important;color:#ffffff !important;"
                        >
                            ✕ Reject Application
                        </button>
                    </form>

                </div>

            @else

                <div class="review-completed">
                    This application has already been reviewed.
                </div>

            @endif

        </div>
    </div>

    <style>
        .application-review-page {
            max-width: 1000px;
            margin: 0 auto;
        }

        .application-back {
            display: inline-block;
            margin-bottom: 18px;
            color: #ffffff !important;
            font-weight: 600;
            text-decoration: none;
        }

        .application-back:hover {
            text-decoration: underline;
        }

        .application-card {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .application-card h2 {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 22px;
        }

        .application-card h3 {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .application-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-item span {
            color: rgba(255, 255, 255, 0.72);
            font-size: 13px;
        }

        .detail-item strong {
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .application-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.25);
            margin: 26px 0;
        }

        .proof-link {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 11px 16px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            color: #ffffff !important;
            font-weight: 600;
            text-decoration: none;
        }

        .proof-link:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .no-proof {
            color: rgba(255, 255, 255, 0.75);
        }

        .existing-review-notes {
            margin-top: 22px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 12px;
        }

        .existing-review-notes p {
            color: #ffffff;
            line-height: 1.6;
        }

        .review-actions {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* APPROVE BUTTON */
        .approve-button {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 22px !important;
            border: none !important;
            border-radius: 10px !important;
            background: #087f5b !important;
            color: #ffffff !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: 0.2s ease;
        }

        .approve-button:hover {
            background: #056747 !important;
            transform: translateY(-1px);
        }

        /* REJECT FORM */
        .reject-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .reject-form label {
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 600;
        }

        .reject-form textarea {
            width: 100%;
            min-height: 110px;
            padding: 13px 15px;
            background: #f7fffb !important;
            color: #164e3b !important;
            border: 1px solid #a7d9c1 !important;
            border-radius: 10px;
            resize: vertical;
        }

        .reject-form textarea:focus {
            outline: 2px solid rgba(255, 255, 255, 0.4);
            border-color: #ffffff !important;
        }

        /* REJECT BUTTON */
        .reject-button {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            align-self: flex-start;
            padding: 13px 22px !important;
            border: none !important;
            border-radius: 10px !important;
            background: #dc2626 !important;
            color: #ffffff !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .reject-button:hover {
            background: #b91c1c !important;
            transform: translateY(-1px);
        }

        .review-error {
            color: #fecaca !important;
            font-size: 13px;
        }

        .review-completed {
            padding: 14px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #ffffff;
        }

        @media (max-width: 640px) {
            .application-card {
                padding: 20px;
            }

            .application-details {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .approve-button,
            .reject-button {
                width: 100%;
            }
        }
    </style>

</x-app-layout>
