<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaAuditReport extends Model
{
    use SoftDeletes;

    protected $table = 'ba_audit_reports';

    protected $fillable = [
        'ba_certificate_id',
        'audit_year',
        'audit_type',
        'audit_date',
        'audit_report_file',
        'uploaded_by',
        'uploaded_by_id',
        'uploaded_at',
        'remarks',
    ];

    protected $dates = [
        'audit_date',
        'uploaded_at',
        'deleted_at',
    ];

    public function certificate()
    {
        return $this->belongsTo(BaCertificate::class, 'ba_certificate_id');
    }
}