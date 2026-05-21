<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeaveBenefit;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreditsController extends Controller
{
    public function index(): View
    {
        $allBenefits = EmployeeLeaveBenefit::with('employee')
            ->orderBy('start_date', 'desc')
            ->get();


        $leaveTypesPermanent = [
            'Special Emergency Leave',
            'Rehabilitation Leave',
            'Solo Parent Leave',
            'Paternity Leave',
            'Maternity Leave',
            'Special Privilege Leave',
            'Wellness Leave',
            'Vacation Leave',
        ];

        // Business rule: COS employees are only entitled to Wellness Leave and CTO
        $leaveTypesCos = [
            'Wellness Leave',
            'Credited Time-Off',
        ];

        return view('credits.leave-credits', compact('allBenefits', 'leaveTypesPermanent', 'leaveTypesCos'));
    }

    public function cto(): View
    {
        $allBenefits = EmployeeLeaveBenefit::with('employee')
            ->orderBy('start_date', 'desc')
            ->get();

        $ctoBenefits = $allBenefits->filter(function ($benefit) {
            $type = strtolower(trim((string) $benefit->credit_type));
            return $type === 'credited time-off' || str_contains($type, 'cto') || $type === 'credited time off';
        })->values();

        // Keep same leave-type arrays so the CTO page can reuse future UI if needed
        $leaveTypesPermanent = [
            'Special Emergency Leave',
            'Rehabilitation Leave',
            'Solo Parent Leave',
            'Paternity Leave',
            'Maternity Leave',
            'Special Privilege Leave',
            'Wellness Leave',
            'Vacation Leave',
        ];

        $leaveTypesCos = [
            'Wellness Leave',
            'Credited Time-Off',
        ];

        return view('credits.cto', [
            'ctoBenefits' => $ctoBenefits,
            'leaveTypesPermanent' => $leaveTypesPermanent,
            'leaveTypesCos' => $leaveTypesCos,
        ]);
    }


    public function edit(EmployeeLeaveBenefit $credit): View
    {
        $benefit = $credit->load('employee.division');

        $leaveTypesPermanent = [
            'Special Emergency Leave',
            'Rehabilitation Leave',
            'Solo Parent Leave',
            'Paternity Leave',
            'Maternity Leave',
            'Special Privilege Leave',
            'Wellness Leave',
            'Vacation Leave',
        ];

        $leaveTypesCos = [
            'Wellness Leave',
            'Credited Time-Off',
        ];

        return view('credits.edit', [
            'benefit' => $benefit,
            'leaveTypesPermanent' => $leaveTypesPermanent,
            'leaveTypesCos' => $leaveTypesCos,
        ]);
    }

    public function update(Request $request, EmployeeLeaveBenefit $credit)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'credit_type' => 'required|string',
            'date_applied' => 'required|date',
            'date_effective' => 'required|date',
            'credit_hours' => 'nullable|integer|min:0',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        // Update credit row
        $credit->credit_type = $validated['credit_type'];
        $credit->start_date = Carbon::parse($validated['start_date'])->toDateString();
        $credit->end_date = $validated['end_date'] ? Carbon::parse($validated['end_date'])->toDateString() : null;
        $credit->date_applied = Carbon::parse($validated['date_applied'])->toDateString();
        $credit->date_effective = Carbon::parse($validated['date_effective'])->toDateString();
        $credit->status = $validated['status'];

        $typeLower = strtolower(trim((string) $validated['credit_type']));

        // Canonicalize CTO input so employee profile filters work reliably.
        $isCtoInput = $typeLower === 'credited time-off' || $typeLower === 'credited time off' || str_contains($typeLower, 'cto');
        if ($isCtoInput) {
            $credit->credit_type = 'Credited Time-Off';
        } else {
            $credit->credit_type = $validated['credit_type'];
        }

        $isCto = $credit->credit_type === 'Credited Time-Off';


        if ($isCto) {
            $credit->credit_hours = isset($validated['credit_hours']) ? (int) $validated['credit_hours'] : 0;
        } else {
            // Day-based credits: 1 day = 10 hours (inclusive)
            $start = Carbon::parse($validated['start_date']);
            $end = $validated['end_date'] ? Carbon::parse($validated['end_date']) : $start;
            $dayCount = (int) $start->diffInDays($end) + 1;
            $credit->credit_hours = $dayCount * 10;
        }

        // Refresh stored employee fields from relationship (source of truth)
        $credit->load('employee.division');
        if ($credit->employee) {
            $credit->name = $credit->employee->full_name;
            $credit->division = $credit->employee->division?->code ?? 'N/A';
            $credit->position = $credit->employee->position;
            $credit->employment_type = $credit->employee->employment_type;
        }

        $credit->save();

        return redirect()->route('credits.index')->with('success', 'Leave credit updated successfully');
    }

    public function destroy(EmployeeLeaveBenefit $credit)
    {
        $credit->delete();
        return redirect()->route('credits.index')->with('success', 'Leave credit deleted successfully');
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $employees = Employee::with('division')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('employee_id', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'full_name' => $emp->full_name,
                    'employee_id' => $emp->employee_id,
                    'division_code' => optional($emp->division)->code ?? 'N/A',
                    'position' => $emp->position,
                    'employment_type' => $emp->employment_type,
                ];
            });

        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'credit_type' => 'required|string',
            'date_applied' => 'required|date',
            'date_effective' => 'required|date',
            'credit_hours' => 'nullable|integer|min:0',
        ]);

        $employee = Employee::find($validated['employee_id']);

        $creditType = (string) $validated['credit_type'];
        $typeLower = strtolower(trim($creditType));

        // Canonicalize CTO input so employee profile filters work reliably.
        $isCtoInput = $typeLower === 'credited time-off' || $typeLower === 'credited time off' || str_contains($typeLower, 'cto');
        if ($isCtoInput) {
            $creditType = 'Credited Time-Off';
        }

        $isDayBased = str_contains($typeLower, 'vacation')
            || str_contains($typeLower, 'sick')

            || str_contains($typeLower, 'wellness')
            || str_contains($typeLower, 'maternity')
            || str_contains($typeLower, 'paternity')
            || str_contains($typeLower, 'solo parent')
            || str_contains($typeLower, 'special privilege')
            || str_contains($typeLower, 'special emergency')
            || str_contains($typeLower, 'rehabilitation');

        $start = Carbon::parse($validated['start_date']);
        $end = $validated['end_date'] ? Carbon::parse($validated['end_date']) : $start;
        $dayCount = (int) $start->diffInDays($end) + 1;

        $creditHoursInput = isset($validated['credit_hours']) ? (int) $validated['credit_hours'] : null;
        $isCto = $creditType === 'Credited Time-Off';


        if ($isCto) {
            $creditHours = $creditHoursInput ?? 0;
        } else {
            $creditHours = $isDayBased ? ($dayCount * 10) : 0;
        }

        EmployeeLeaveBenefit::create([
            'employee_id' => $employee->id,
            'name' => $employee->full_name,
            'division' => optional($employee->division)->code ?? 'N/A',
            'position' => $employee->position,
            'employment_type' => $employee->employment_type,
            'credit_type' => $creditType,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'credit_hours' => $creditHours,
            'hours_used' => 0,
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('credits.index')->with('success', 'Leave credit created successfully');
    }
}

