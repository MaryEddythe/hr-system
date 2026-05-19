<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\EmployeeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([EmployeeObserver::class])]
class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'division_id',
        'position',
        'employment_type',
        'leave_type',
        'hired_at',
        'drive_folder_id',
        'drive_folder_url',
    ];

    protected $casts = [
        'hired_at' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public static function generateEmployeeId(): string
    {
        $latest = self::latest('id')->first();

        $number = $latest
            ? ((int) filter_var($latest->employee_id, FILTER_SANITIZE_NUMBER_INT)) + 1
            : 1;

        return 'EMP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function files()
    {
        return $this->hasMany(EmployeeFile::class);
    }

    public function leaveBenefits()
    {
        return $this->hasMany(EmployeeLeaveBenefit::class);
    }

}