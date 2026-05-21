@extends('layouts.app')
@section('title', 'CTO')

@section('content')
<style>
    .credits-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .credits-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    /* Popup modal for Add CTO */
    .modal-overlay{
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .modal-content{
        background: #fff;
        border-radius: 10px;
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    }
    .modal-header{
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display:flex;
        align-items:center;
        justify-content: space-between;
        gap:1rem;
    }
    .modal-title{ font-size: 1.2rem; font-weight: 800; color:#0f172a; }
    .modal-close{
        background:none;
        border:none;
        font-size: 1.5rem;
        cursor:pointer;
        color:#64748b;
        line-height:1;
    }
    .modal-body{ padding: 1.25rem 1.5rem; }
    .modal-footer{
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        display:flex;
        justify-content:flex-end;
        gap:0.75rem;
    }

    .form-group-label{
        font-size: 0.8rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display:block;
    }
</style>


<div class="page-header">
    <div class="credits-info">
        <div class="page-title">CTO</div>
        <div class="page-subtitle">Credited Time-Off credits and credit status</div>
    </div>

    <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
        <button type="button" class="btn btn-primary" onclick="openCreateCtoModal()">+ Add CTO</button>
        <a href="{{ route('credits.index') }}" class="btn btn-outline">Back to Leave Credits</a>
    </div>
</div>

<div class="modal-overlay" id="createCtoModal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add CTO</h2>
            <button class="modal-close" onclick="closeCreateCtoModal()">×</button>
        </div>

        <form method="POST" action="{{ route('credits.store') }}" onsubmit="handleSubmit(event)">
            @csrf
            <div class="modal-body">

                <div class="search-container">
                    <label class="form-group-label">Employee *</label>
                    <input type="text" id="ctoEmployeeSearch" class="search-input" placeholder="Search employees..." autocomplete="off" required>
                    <div class="search-results" id="ctoSearchResults"></div>
                    <input type="hidden" id="ctoEmployeeId" name="employee_id" required>
                </div>

                <div class="form-grid">
                    <div>
                        <label class="form-group-label">Division</label>
                        <input type="text" id="ctoDivision" class="form-control" disabled>
                    </div>
                    <div>
                        <label class="form-group-label">Position</label>
                        <input type="text" id="ctoPosition" class="form-control" disabled>
                    </div>
                </div>

                <div>
                    <label class="form-group-label">Employment Type</label>
                    <input type="text" id="ctoEmploymentType" class="form-control" disabled>
                </div>

                <input type="hidden" name="credit_type" value="Credited Time-Off" />

                <div class="form-grid">
                    <div>
                        <label class="form-group-label">Start Date *</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-group-label">End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <label class="form-group-label">Credit Hours *</label>
                        <input type="number" name="credit_hours" id="ctoCreditHours" class="form-control" min="0" step="1" placeholder="Enter hours" required />
                    </div>
                    <div>
                        <label class="form-group-label">Status</label>
                        <input type="text" class="form-control" value="ACTIVE" disabled>
                    </div>
                </div>

                <div class="form-grid">
                    <div>
                        <label class="form-group-label">Date Applied *</label>
                        <input type="date" name="date_applied" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-group-label">Date Effective *</label>
                        <input type="date" name="date_effective" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeCreateCtoModal()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">Create CTO</button>
            </div>
        </form>
    </div>
</div>


<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Division</th>
                <th>Position</th>
                <th>Employment Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Leave Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ctoBenefits as $benefit)
                <tr>
                    <td><span class="badge badge-blue">{{ $benefit->employee->employee_id ?? 'N/A' }}</span></td>
                    <td><div class="leave-type-cell">{{ $benefit->name }}</div></td>
                    <td>{{ $benefit->division ?? 'N/A' }}</td>
                    <td>{{ $benefit->position ?? 'N/A' }}</td>
                    <td>{{ $benefit->employment_type === 'PERMANENT' ? 'Permanent' : 'COS' }}</td>
                    <td><div class="leave-date">{{ $benefit->start_date->format('M d, Y') }}</div></td>
                    <td>
                        <div class="leave-date">
                            @if($benefit->end_date)
                                {{ $benefit->end_date->format('M d, Y') }}
                            @else
                                <span style="color: #94a3b8;">—</span>
                            @endif
                        </div>
                    </td>
                    <td><div class="leave-type-cell">{{ $benefit->credit_type }}</div></td>
                    <td>
                        <span class="status-badge status-{{ strtolower($benefit->status) }}">
                            {{ $benefit->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-state-icon">–</div>
                            <div class="empty-state-text">No CTO credits found</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // CTO Employee live search (uses GET route: api/employees/search)
    function openCreateCtoModal(){
        const el = document.getElementById('createCtoModal');
        if(!el) return;
        el.style.display = 'flex';
    }
    function closeCreateCtoModal(){
        const el = document.getElementById('createCtoModal');
        if(!el) return;
        el.style.display = 'none';
    }

    function handleSubmit(event){
        // keep modal UX; submission proceeds normally
    }

    async function searchEmployees(query) {
        if (!query || query.trim().length < 1) return [];

        const res = await fetch(`{{ route('api.employees.search') }}?q=${encodeURIComponent(query)}`);
        if (!res.ok) return [];
        return await res.json();
    }

    function renderSearchResults(items) {
        const resultsEl = document.getElementById('ctoSearchResults');
        if (!resultsEl) return;

        if (!items || items.length === 0) {
            resultsEl.innerHTML = '<div class="search-result-empty">No employees found</div>';
            return;
        }

        resultsEl.innerHTML = items.map(item => {
            return `
                <button type="button" class="search-result-item" data-employee-id="${item.id}">
                    <div style="font-weight:700">${item.full_name}</div>
                    <div style="font-size:0.85em; opacity:0.85">${item.employee_id} · ${item.division_code}</div>
                </button>
            `;
        }).join('');
    }

    function attachResultClickHandlers() {
        const resultsEl = document.getElementById('ctoSearchResults');
        if (!resultsEl) return;

        resultsEl.querySelectorAll('.search-result-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const employeeId = btn.getAttribute('data-employee-id');
                const employee = btn;

                // Find matching item from last rendered list (by id)
                const searchInput = document.getElementById('ctoEmployeeSearch');
                const inputValue = searchInput?.value ?? '';

                // Use DOM text parsing is brittle; instead re-search quickly for selected name fragment.
                // We'll just set hidden employee id and fill remaining fields from button content when possible.
                // For robust fields, we re-call searchEmployees using current input.
                const finalize = async () => {
                    const items = await searchEmployees(inputValue);
                    const selected = items.find(x => String(x.id) === String(employeeId));

                    if (!selected) return;

                    // Populate visible + hidden fields
                    document.getElementById('ctoEmployeeId').value = selected.id;
                    document.getElementById('ctoEmployeeSearch').value = selected.full_name;
                    document.getElementById('ctoDivision').value = selected.division_code ?? 'N/A';
                    document.getElementById('ctoPosition').value = selected.position ?? 'N/A';
                    document.getElementById('ctoEmploymentType').value = selected.employment_type ?? 'N/A';


                    const resultsEl2 = document.getElementById('ctoSearchResults');
                    if (resultsEl2) resultsEl2.innerHTML = '';
                };

                finalize();
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('ctoEmployeeSearch');
        const resultsEl = document.getElementById('ctoSearchResults');

        if (!searchInput || !resultsEl) return;

        let debounceTimer = null;

        searchInput.addEventListener('input', () => {
            const q = searchInput.value;

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(async () => {
                const items = await searchEmployees(q);
                renderSearchResults(items);
                attachResultClickHandlers();
            }, 250);
        });

        document.addEventListener('click', (e) => {
            const within = e.target.closest?.('#createCtoModal');
            if (!within) return;

            // Click outside the results area clears results
            if (!e.target.closest?.('#ctoSearchResults') && !e.target.closest?.('#ctoEmployeeSearch')) {
                resultsEl.innerHTML = '';
            }
        });

        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape'){
                closeCreateCtoModal();
            }
        });
    });
</script>



@endsection


