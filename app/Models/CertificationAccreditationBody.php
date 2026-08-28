<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificationAccreditationBody extends Model
{
    protected $table = 'certification_accreditation_bodies';

    protected $fillable = [
        'accreditation_body_name',
        'short_name',
        'status',
    ];

    public function certificates()
    {
        return $this->hasMany(CertificationCertificate::class, 'certification_accreditation_body_id');
    }
}
