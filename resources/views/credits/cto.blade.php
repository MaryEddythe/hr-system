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
</style>

<div class="page-header">
    <div class="credits-info">
        <div class="page-title">CTO</div>
        <div class="page-subtitle">Credited Time-Off credits and credit status</div>
    </div>
    <a href="{{ route('credits.index') }}" class="btn btn-outline">Back to Leave Credits</a>
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
@endsection

