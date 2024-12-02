<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = "departments";

    protected $fillable = [
        'name',
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
    public static function selectDepartments($id, $name){
        $deparments_query = Department::with([
            'createdBy:id,email',
            'updatedBy:id,email',
            'approvedBy:id,email',
            'disabledBy:id,email'
        ]);

        if($id != null){
            $deparments_query->where('departments.id', $id);
        }
        elseif($name != null){
            $deparments_query->where('departments.name', $name);
        }

        return $deparments_query->get()->map(function ($department) {
            return [
                'id' => $department->id,
                'name' => $department->name,
                'created_by' => $department->createdBy ? $department->createdBy->email : null,
                'created_at' => $department->created_at,
                'updated_by' => $department->updatedBy ? $department->updatedBy->email : null,
                'updated_at' => $department->updated_at,
                'approved_by' => $department->approvedBy ? $department->approvedBy->email : null,
                'approved_at' => $department->approved_at,
                'disabled_by' => $department->disabledBy ? $department->disabledBy->email : null,
                'disabled_at' => $department->disabled_at,
            ];
        });
    }
}
