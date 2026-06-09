<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaCertificate extends Model
{
    use SoftDeletes;

    protected $table = 'ba_certificates';

    protected $fillable = [
        'ba_client_id',
        'ba_standard_id',
        'ba_accreditation_body_id',

        'certificate_number',
        'certificate_scope',
        'certificate_issue_date',
        'certificate_expiry_date',
        'certification_cycle',
        'initial_certification_audit_completion_date',

        'surveillance_1_due_date',
        'surveillance_2_due_date',
        'recertification_due_date',
        'grace_period_end_date',

        'audit_status',
        'certificate_status',

        'lead_auditor',
        'auditor_1',
        'auditor_2',
        'auditor_3',
        'technical_expert',

        'status',
        'created_by',
        'created_by_id',
        'review_by',
        'review_by_id',
        'reviewed_at',
        'approval_by',
        'approval_by_id',
        'approved_at',
        'updated_by',
        'updated_by_id',
        'deleted_by',
        'deleted_by_id',

        'certificate_pdf',
        'pdf_uploaded_by',
        'pdf_uploaded_by_id',
        'pdf_uploaded_at',

        'remarks',
    ];

    protected $dates = [
        'certificate_issue_date',
        'certificate_expiry_date',
        'initial_certification_audit_completion_date',
        'surveillance_1_due_date',
        'surveillance_2_due_date',
        'recertification_due_date',
        'grace_period_end_date',
        'reviewed_at',
        'approved_at',
        'pdf_uploaded_at',
        'deleted_at',
    ];

    public function client()
    {
        return $this->belongsTo(BaClient::class, 'ba_client_id');
    }

    public function standard()
    {
        return $this->belongsTo(BaStandard::class, 'ba_standard_id');
    }

    public function accreditationBody()
    {
        return $this->belongsTo(BaAccreditationBody::class, 'ba_accreditation_body_id');
    }

    public function auditReports()
    {
        return $this->hasMany(BaAuditReport::class, 'ba_certificate_id');
    }
}