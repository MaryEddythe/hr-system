<button type="button" class="sidebar-toggle" aria-label="Toggle sidebar" aria-expanded="true">
    <span class="sidebar-toggle-icon" aria-hidden="true">›</span>
</button>


<nav class="sidebar" id="appSidebar">
    <a href="{{ route('employees.index') }}" class="brand">
        <span class="brand-full">HR File System</span>
        <span class="brand-short">HR</span>
    </a>



    <div class="nav-links">
        <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">Employees</a>
        <a href="{{ route('credits.index') }}" class="{{ request()->routeIs('credits.*') ? 'active' : '' }}">Leave Credits</a>
    </div>

    <div class="nav-user">
        <div class="user-info">
            <div class="user-avatar">AD</div>
            <div class="user-details">
                <span class="user-name">Admin User</span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
        <a href="#logout">Logout</a>
    </div>
</nav>

