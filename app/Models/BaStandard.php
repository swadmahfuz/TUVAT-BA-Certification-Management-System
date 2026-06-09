<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaStandard extends Model
{
    protected $table = 'ba_standards';

    protected $fillable = [
        'standard_name',
        'standard_code',
        'status',
    ];

    public function certificates()
    {
        return $this->hasMany(BaCertificate::class, 'ba_standard_id');
    }
}