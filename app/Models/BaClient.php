<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaClient extends Model
{
    use SoftDeletes;

    protected $table = 'ba_clients';

    protected $fillable = [
        'client_name',
        'client_address',
        'contact_person',
        'email',
        'phone',
        'remarks',
        'created_by',
        'created_by_id',
        'updated_by',
        'updated_by_id',
        'deleted_by',
        'deleted_by_id',
    ];

    public function certificates()
    {
        return $this->hasMany(BaCertificate::class, 'ba_client_id');
    }
}