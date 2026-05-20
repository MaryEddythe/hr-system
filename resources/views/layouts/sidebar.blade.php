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

        <div class="nav-dropdown">
            <button type="button" class="nav-dropdown-toggle {{ request()->routeIs('credits.*') ? 'active' : '' }}" onclick="toggleCreditsDropdown()">
                <span>LEAVE CREDITS</span>
                <span class="nav-dropdown-arrow" id="creditsDropdownArrow">▾</span>
            </button>
            <div class="nav-dropdown-menu" id="creditsDropdownMenu">
                <a href="{{ route('credits.cto') }}" class="{{ request()->routeIs('credits.cto') ? 'active' : '' }}">CTO</a>
                <a href="{{ route('credits.index') }}" class="{{ request()->routeIs('credits.index') ? 'active' : '' }}">Leave Credits</a>
            </div>
        </div>
    </div>

    <style>
        .nav-dropdown{ position: relative; }
.nav-dropdown-toggle{
            width: 100%;
            background: transparent;
            border: none;
            color: #ffffff;
            padding: 0.6rem 0.75rem;
            cursor: pointer;
            text-align: left;
            font: inherit;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 0.75rem;
        }
        .nav-dropdown-arrow{
            display:inline-block;
            transition: transform 0.15s ease;
        }
        .nav-dropdown-menu{
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            min-width: 220px;
            background: #0b3b87;
            border-radius: 6px;
            padding: 0.35rem;
            z-index: 50;
        }
        .nav-dropdown-menu a{
            display: block;
            padding: 0.35rem 0.75rem;
            text-decoration: none;
            color: #ffffff;
            border-radius: 4px;
            opacity: 1;
            font-size: 0.82rem;
        }
        .nav-dropdown-menu a:hover{
            background: transparent;
        }
        .nav-dropdown-menu a.active{
            background: transparent !important;
            color: #ffffff !important;
        }
        .nav-dropdown-menu a.active:hover{
            background: transparent !important;
        }

        /* JS-controlled open state */
        .nav-dropdown.open .nav-dropdown-menu{ display:block; }
        .nav-dropdown.open #creditsDropdownArrow{ transform: rotate(180deg); }
    </style>


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

<!-- remove any "Add CTO" button/link present anywhere on the page -->
<script>
function toggleCreditsDropdown(){
    const dropdown = document.querySelector('.nav-dropdown');
    if(!dropdown) return;
    dropdown.classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', function () {
    // Ensure dropdown toggle works on first render as well
    window.toggleCreditsDropdown = toggleCreditsDropdown;

    document.querySelectorAll('a,button,input[type="button"],input[type="submit"],span').forEach(function(el){
        if (el.textContent && el.textContent.trim() === 'Add CTO') {
            el.remove();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e){
        const dropdown = document.querySelector('.nav-dropdown');
        const btn = document.querySelector('.nav-dropdown-toggle');
        if(!dropdown || !btn) return;
        if(!dropdown.contains(e.target) && !btn.contains(e.target)){
            dropdown.classList.remove('open');
        }
    });
});
</script>


