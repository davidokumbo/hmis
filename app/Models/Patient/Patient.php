<?php

namespace App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Patient extends Model
{
    use HasFactory;

    protected $table = "patients";

    protected $fillable = [
        'firstname',
        'lastname',
        'dob',
        'phonenumber1',
        'phonenumber2',
        'email',
        'address',
        'residence',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'disabled_by',
        'disabled_at',
        'deleted_by',
        'deleted_at',
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
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }


    //perform selection
    public static function selectPatients($id, $email){
        $patients_query = Patient::with([
            'createdBy:id,email',
            'updatedBy:id,email',
            'approvedBy:id,email',
            'disabledBy:id,email'
        ]);

        if($id != null){
            $patients_query->where('patients.id', $id);
        }
        elseif($email != null){
            $patients_query->where('patients.email', $email);
        }

        return $patients_query->get()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'patient_firstname' => $patient->firstname,
                'patient_lastname' => $patient->lastname,
                'dob' => $patient->dob,
                'phonenumber1' => $patient->phonenumber1,
                'phonenumber2' => $patient->phonenumber2,
                'email' => $patient->email,
                'address' => $patient->address,
                'residence' => $patient->residence,
                'created_by' => $patient->createdBy ? $patient->createdBy->email : null,
                'created_at' => $patient->created_at,
                'updated_by' => $patient->updatedBy ? $patient->updatedBy->email : null,
                'updated_at' => $patient->updated_at,
                'approved_by' => $patient->approvedBy ? $patient->approvedBy->email : null,
                'approved_at' => $patient->approved_at,
                'disabled_by' => $patient->disabledBy ? $patient->disabledBy->email : null,
                'disabled_at' => $patient->disabled_at,
                'deleted_by' => $patient->deleted_by ? $patient->deletedBy->email : null,
                'deleted_at' => $patient->deleted_at,        

            ];
        });
    }
}