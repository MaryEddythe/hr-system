@extends('layouts.app')
@section('title', 'Employees')

@section('content')
<style>
    .employees-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .employees-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .table-wrapper {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        margin-bottom: 2rem;
    }
    .actions-cell {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
    }
    .employee-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
    }
    .employee-email {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
    }
    .empty-state-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, #e2e8f0 0%, #f1f5f9 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #94a3b8;
    }
    .empty-state-text {
        font-size: 0.95rem;
        margin-bottom: 0.85rem;
        font-weight: 600;
        color: #475569;
    }
    .empty-state-link {
        color: #0066cc;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.2s ease;
        font-size: 0.9rem;
    }
    .empty-state-link:hover {
        color: #0052a3;
    }
</style>

<div class="page-header">
    <div class="employees-info">
        <div class="page-title">Employees</div>
        <div class="page-subtitle">Total Records: <strong>{{ $employees->total() }}</strong></div>
    </div>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">+ Add Employee</a>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $emp)
            <tr>
                <td>
                    <span class="badge badge-blue">{{ $emp->employee_id }}</span>
                </td>
                <td>
                    <div class="employee-name">{{ $emp->full_name }}</div>
                    <div class="employee-email">{{ $emp->email }}</div>
                </td>
                <td>{{ optional($emp->division)->code ?? 'N/A' }}</td>
                <td>{{ $emp->position }}</td>
                <td>{{ $emp->employment_type === 'PERMANENT' ? 'Permanent' : 'COS' }}</td>
                <td>
                    <div class="actions-cell">
                        <a href="{{ route('employees.show', $emp) }}" class="btn btn-outline btn-sm">View</a>
                        <a href="{{ route('employees.edit', $emp) }}" class="btn btn-outline btn-sm">Edit</a>
                        <form method="POST" action="{{ route('employees.destroy', $emp) }}"
                              onsubmit="return confirm('Delete {{ $emp->full_name }}? This cannot be undone.')">

                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <div class="empty-state-icon">–</div>
                        <div class="empty-state-text">No employees found</div>
                        <a href="{{ route('employees.create') }}" class="empty-state-link">Add your first employee</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div>{{ $employees->links() }}</div>
@endsection
