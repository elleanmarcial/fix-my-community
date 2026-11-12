
@php($u = Auth::user())

<header class="topbar">
    <div class="topbar-in">

        <a class="brand" href="{{ route('dashboard') }}" style="text-decoration:none;color:inherit">
            <span class="mark" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="#fff" stroke-width="1.6">
                    <path d="M9 16s5-4.4 5-8.2A5 5 0 0 0 4 7.8C4 11.6 9 16 9 16z"/>
                    <circle cx="9" cy="7.8" r="1.8"/>
                </svg>
            </span>
            Fix My Community
        </a>

        <nav class="fmc-nav" aria-label="Main navigation">

            {{-- SUPER ADMIN NAVIGATION --}}
            @if ($u->role === 'super_admin')

                <a href="{{ route('super-admin.applications.index') }}"
                   @if(request()->routeIs('super-admin.applications.*')) aria-current="page" @endif>
                    Admin Applications
                </a>

                <a href="{{ route('super-admin.sessions.index') }}"
                   @if(request()->routeIs('super-admin.sessions.*')) aria-current="page" @endif>
                    Sessions
                </a>

                <a href="{{ route('super-admin.users.index') }}"
                   @if(request()->routeIs('super-admin.users.*')) aria-current="page" @endif>
                    User Accounts
                </a>

            {{-- ADMIN NAVIGATION --}}
            @elseif ($u->role === 'admin')

                <a href="{{ route('admin.dashboard') }}"
                   @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                    Overview
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   @if(request()->routeIs('admin.reports.*')) aria-current="page" @endif>
                    Community reports
                </a>

            {{-- RESIDENT NAVIGATION --}}
            @else

                <a href="{{ route('dashboard') }}"
                   @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                    Home
                </a>

                <a href="{{ route('reports.create') }}"
                   @if(request()->routeIs('reports.create')) aria-current="page" @endif>
                    Submit a report
                </a>

                <a href="{{ route('reports.index') }}"
                   @if(request()->routeIs('reports.index','reports.show','reports.edit')) aria-current="page" @endif>
                    My reports
                </a>

            @endif

            {{-- SHARED NAVIGATION --}}
            <a href="{{ route('profile.edit') }}"
               @if(request()->routeIs('profile.*')) aria-current="page" @endif>
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="navlink">Sign out</button>
            </form>

        </nav>
    </div>
</header>
