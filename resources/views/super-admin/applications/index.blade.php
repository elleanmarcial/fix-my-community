
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Super Admin — Application Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">
                                Admin Applications
                            </h3>
                            <p class="text-gray-500 text-sm mt-1">
                                Review and manage barangay administrator applications.
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Applicant</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barangay</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse($applications as $application)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-900">
                                                {{ $application->applicant_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $application->email }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            {{ $application->barangay_name }},
                                            {{ $application->city }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-700 capitalize">
                                            {{ $application->position }}
                                        </td>

                                        <td class="px-4 py-4">
                                            @if($application->status === 'pending_review')
                                                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($application->status === 'approved')
                                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">Approved</span>
                                            @else
                                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-right">
                                            <a href="{{ route('super-admin.applications.show', $application->id) }}"
                                               class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                                View Details →
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                            No applications found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
