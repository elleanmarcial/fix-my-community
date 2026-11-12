
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Session Management
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">

                <h3 class="text-2xl font-bold text-gray-800">
                    Active User Sessions
                </h3>

                <p class="text-sm text-gray-500 mt-1 mb-6">
                    Monitor and manage currently active user login sessions.
                </p>

                @php
                    $roles = [
                        'resident' => 'Residents',
                        'admin' => 'Administrators',
                        'super_admin' => 'Super Administrators',
                    ];
                @endphp

                <div class="flex flex-wrap gap-2 border-b pb-4 mb-6">
                    @foreach($roles as $key => $label)
                        <a href="{{ route('super-admin.sessions.index', $key) }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium
                           {{ $role === $key
                                ? 'bg-indigo-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-gray-800">
                        {{ $roles[$role] }} Sessions
                    </h4>

                    <span class="text-sm text-gray-500">
                        {{ $sessions->total() }} active session(s)
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Device / Browser</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP Address</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Last Activity</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($sessions as $session)
                                <tr>
                                    <td class="px-4 py-4">
                                        <p class="font-medium text-gray-900">
                                            {{ $session->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $session->email }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <div class="max-w-xs break-words">
                                            {{ $session->user_agent ?: 'Unknown device' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{ $session->ip_address ?: 'Unknown' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                    </td>

                                    <td class="px-4 py-4 text-right">
                                        @if($session->session_id !== session()->getId())
                                            <form method="POST"
                                                  action="{{ route('super-admin.sessions.destroy', $session->session_id) }}"
                                                  onsubmit="return confirm('Terminate this user session?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                    Force Logout
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                Current Session
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                        No active sessions found for this account type.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $sessions->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
