<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificationAuditReport extends Model
{
    use SoftDeletes;

    protected $table = 'certification_audit_reports';

    protected $fillable = [
        'certification_certificate_id',
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
        return $this->belongsTo(CertificationCertificate::class, 'certification_certificate_id');
    }
}
