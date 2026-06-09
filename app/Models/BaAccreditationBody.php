<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaAccreditationBody extends Model
{
    protected $table = 'ba_accreditation_bodies';

    protected $fillable = [
        'accreditation_body_name',
        'short_name',
        'status',
    ];

    public function certificates()
    {
        return $this->hasMany(BaCertificate::class, 'ba_accreditation_body_id');
    }
}