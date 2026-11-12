<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- PAGE HEADER --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    User Accounts
                </h1>
                <p class="text-sm text-gray-500">
                    Manage registered residents, administrators, and Super Admins.
                </p>
            </div>

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- SEARCH AND FILTER --}}
            <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
                <form method="GET"
                    action="{{ route('super-admin.users.index') }}"
                    class="flex flex-col md:flex-row gap-3">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name or email..."
                        class="flex-1 rounded-lg border-gray-300"
                    >

                    <select name="role" class="rounded-lg border-gray-300">
                        <option value="">All Roles</option>

                        <option value="resident" @selected(request('role') === 'resident')>
                            Residents
                        </option>

                        <option value="admin" @selected(request('role') === 'admin')>
                            Admins
                        </option>

                        <option value="super_admin" @selected(request('role') === 'super_admin')>
                            Super Admins
                        </option>
                    </select>

                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Search
                    </button>

                    <a href="{{ route('super-admin.users.index') }}"
                        class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-center hover:bg-gray-200">
                        Reset
                    </a>
                </form>
            </div>

            {{-- USER TABLE --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">

                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Registered</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($users as $user)

                                <tr class="hover:bg-gray-50">

                                    {{-- USER INFORMATION --}}
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-800">
                                            {{ $user->name }}
                                        </div>

                                        <div class="text-gray-500">
                                            {{ $user->email }}
                                        </div>
                                    </td>

                                    {{-- ROLE --}}
                                    <td class="px-6 py-4">
                                        {{ ucwords(str_replace('_', ' ', $user->role)) }}
                                    </td>

                                    {{-- ACCOUNT STATUS --}}
                                    <td class="px-6 py-4">

                                        @if($user->is_active)
                                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs">
                                                Disabled
                                            </span>
                                        @endif

                                    </td>

                                    {{-- REGISTRATION DATE --}}
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $user->created_at?->format('M d, Y') }}
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="px-6 py-4">

                                        <div class="flex flex-wrap items-center gap-2 justify-end">

                                            {{-- VIEW DETAILS --}}
                                            <a href="{{ route('super-admin.users.show', $user->id) }}"
                                                class="px-4 py-2 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                                View Details
                                            </a>

                                            {{-- DISABLE / REACTIVATE --}}
                                            @if($user->id !== auth()->id() && $user->role !== 'super_admin')

                                                <form method="POST"
                                                    action="{{ route('super-admin.users.toggle-status', $user->id) }}"
                                                    onsubmit="return confirm('Are you sure you want to {{ $user->is_active ? 'disable' : 'reactivate' }} this account?');">

                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        class="px-4 py-2 rounded-lg text-xs font-semibold
                                                        {{ $user->is_active
                                                            ? 'bg-red-100 text-red-700 hover:bg-red-200'
                                                            : 'bg-green-100 text-green-700 hover:bg-green-200' }}">

                                                        {{ $user->is_active ? 'Disable' : 'Reactivate' }}

                                                    </button>

                                                </form>

                                            @else

                                                <span class="text-gray-400 text-xs">
                                                    Protected
                                                </span>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        No user accounts found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="p-5 border-t">
                    {{ $users->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
