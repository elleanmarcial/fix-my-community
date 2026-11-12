<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('super-admin.users.index') }}"
                   class="text-sm text-indigo-600 hover:underline">
                    ← Back to User Accounts
                </a>

                <h1 class="text-2xl font-bold text-gray-800 mt-3">
                    User Account Details
                </h1>
            </div>

            {{-- USER INFORMATION --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    Personal Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm text-gray-500">Full Name</p>
                        <p class="font-semibold">{{ $user->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Email Address</p>
                        <p class="font-semibold">{{ $user->email }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Role</p>
                        <p class="font-semibold">
                            {{ ucwords(str_replace('_', ' ', $user->role)) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Account Status</p>
                        <p class="font-semibold">
                            {{ $user->is_active ? 'Active' : 'Disabled' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Email Verification</p>
                        <p class="font-semibold">
                            {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Registration Date</p>
                        <p class="font-semibold">
                            {{ $user->created_at?->format('F d, Y h:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- BARANGAY MEMBERSHIP --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    Barangay Membership
                </h2>

                @forelse($memberships as $membership)
                    <div class="border rounded-lg p-4 mb-3">
                        <p class="font-semibold">{{ $membership->barangay_name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $membership->city }}
                        </p>
                        <p class="text-sm mt-2">
                            Position: {{ ucfirst($membership->position) }}
                        </p>
                        <p class="text-sm">
                            Status: {{ ucfirst($membership->status) }}
                        </p>
                        <p class="text-sm">
                            Joined:
                            {{ $membership->joined_at
                                ? \Carbon\Carbon::parse($membership->joined_at)->format('M d, Y')
                                : 'Not available' }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">
                        No barangay membership found.
                    </p>
                @endforelse
            </div>

            {{-- SUBMITTED REPORTS --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    Submitted Reports
                </h2>

                @forelse($reports as $report)
                    <div class="border-b py-4">
                        <p class="font-semibold">{{ $report->title }}</p>
                        <p class="text-sm text-gray-500">
                            Category: {{ $report->category }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Status: {{ $report->status }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Submitted:
                            {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">
                        No submitted reports found.
                    </p>
                @endforelse
            </div>

            {{-- ADMIN APPLICATION HISTORY --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    Admin Application History
                </h2>

                @forelse($applications as $application)
                    <div class="border-b py-4">
                        <p class="font-semibold">
                            {{ $application->barangay_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Position: {{ ucfirst($application->position) }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Status: {{ ucwords(str_replace('_', ' ', $application->status)) }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Applied:
                            {{ \Carbon\Carbon::parse($application->created_at)->format('M d, Y') }}
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">
                        No admin applications found.
                    </p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
