<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = "employees";

    protected $fillable = [
        'ipnumber',
        'employee_code',
        'age',
        'dob',
        'role',
        'employee_name',
        'speciality',
        'user_id',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'disabled_by',
        'disabled_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function disabledBy()
    {
        return $this->belongsTo(User::class, 'disabled_by');
    }


    //perform selection
    public static function selectEmployees($id, $employee_name){
        $employees_query = Employee::with([
            'createdBy:id,email',
            'updatedBy:id,email',
            'approvedBy:id,email',
            'disabledBy:id,email'
        ]);

        if($id != null){
            $employees_query->where('employees.id', $id);
        }
        elseif($employee_name != null){
            $employees_query->where('employees.employee_name', $employee_name);
        }

        return $employees_query->get()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'employee_name' => $employee->employee_name,
                'employee_code' => $employee->employee_code,
                'ipnumber' => $employee->ipnumber,
                'created_by' => $employee->createdBy ? $employee->createdBy->email : null,
                'created_at' => $employee->created_at,
                'updated_by' => $employee->updatedBy ? $employee->updatedBy->email : null,
                'updated_at' => $employee->updated_at,
                'approved_by' => $employee->approvedBy ? $employee->approvedBy->email : null,
                'approved_at' => $employee->approved_at,
                'disabled_by' => $employee->disabledBy ? $employee->disabledBy->email : null,
                'disabled_at' => $employee->disabled_at,
            ];
        });
    }
}
