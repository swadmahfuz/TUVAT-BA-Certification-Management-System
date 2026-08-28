<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificationStandard extends Model
{
    protected $table = 'certification_standards';

    protected $fillable = [
        'standard_name',
        'standard_code',
        'status',
    ];

    public function certificates()
    {
        return $this->hasMany(CertificationCertificate::class, 'certification_standard_id');
    }
}
