@extends('layouts.app')
@section('title', 'Add CTO')

@section('content')
<div class="page-header">
    <div class="credits-info">
        <div class="page-title">Add CTO</div>
        <div class="page-subtitle">Create a Credited Time-Off credit</div>
    </div>
    <a href="{{ route('credits.index') }}" class="btn btn-outline">Back</a>
</div>

<div class="form-card" style="max-width: 800px;">
    <form method="POST" action="{{ route('credits.store') }}">
        @csrf

        {{-- Using the same leave-credit store route; we pre-set credit_type to CTO --}}
        <input type="hidden" name="credit_type" value="Credited Time-Off">

        <div class="modal-body">
            <div class="search-container">
                <label class="form-group-label">Employee *</label>
                <input type="text" id="employeeSearch" class="search-input" placeholder="Search employees..." autocomplete="off" required>
                <div class="search-results" id="searchResults"></div>
                <input type="hidden" id="employeeId" name="employee_id" required>
            </div>

            <div class="form-grid">
                <div>
                    <label class="form-group-label">Division</label>
                    <input type="text" id="division" class="form-control" disabled>
                </div>
                <div>
                    <label class="form-group-label">Position</label>
                    <input type="text" id="position" class="form-control" disabled>
                </div>
            </div>

            <div>
                <label class="form-group-label">Employment Type</label>
                <input type="text" id="employmentType" class="form-control" disabled>
            </div>

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

            <div id="creditHoursWrapper" style="display:block;">
                <div class="form-grid full">
                    <div>
                        <label class="form-group-label">Credit Hours *</label>
                        <input type="number" name="credit_hours" class="form-control" min="0" step="1" placeholder="Enter hours" required>
                    </div>
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

            <div>
                <label class="form-group-label">Remarks</label>
                <input type="text" name="remarks" class="form-control" placeholder="Enter remarks (optional)">
            </div>
        </div>

        <div class="modal-footer">
            <a href="{{ route('credits.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Leave Credit</button>
        </div>
    </form>
</div>

<script>
    const employeeSearch = document.getElementById('employeeSearch');
    const searchResults = document.getElementById('searchResults');
    const employeeIdInput = document.getElementById('employeeId');
    const divisionField = document.getElementById('division');
    const positionField = document.getElementById('position');
    const employmentTypeField = document.getElementById('employmentType');

    employeeSearch.addEventListener('input', async (e) => {
        const query = e.target.value.trim();
        if (query.length < 1) {
            searchResults.classList.remove('active');
            return;
        }

        try {
            const response = await fetch(`{{ route('api.employees.search') }}?q=${encodeURIComponent(query)}`);
            const employees = await response.json();

            if (employees.length === 0) {
                searchResults.innerHTML = '<div style="padding: 0.75rem 1rem; color: #94a3b8;">No employees found</div>';
                searchResults.classList.add('active');
                return;
            }

            searchResults.innerHTML = employees.map(emp => `
                <div class="search-result-item" onclick="selectEmployee(${emp.id}, '${emp.full_name}', '${emp.division_code}', '${emp.position}', '${emp.employment_type}')">
                    <div class="search-result-name">${emp.full_name}</div>
                    <div class="search-result-info">${emp.employee_id} · ${emp.division_code} · ${emp.position}</div>
                </div>
            `).join('');
            searchResults.classList.add('active');
        } catch (error) {
            console.error('Search error:', error);
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-container')) {
            searchResults.classList.remove('active');
        }
    });

    function selectEmployee(id, name, division, position, employmentType) {
        employeeSearch.value = name;
        employeeIdInput.value = id;
        divisionField.value = division;
        positionField.value = position;
        employmentTypeField.value = employmentType;
        searchResults.classList.remove('active');
    }
</script>
@endsection

