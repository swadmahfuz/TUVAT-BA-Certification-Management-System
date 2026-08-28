<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificationCertificate extends Model
{
    use SoftDeletes;

    protected $table = 'certification_certificates';

    protected $fillable = [
        'certification_client_id',
        'certification_standard_id',
        'certification_accreditation_body_id',

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
        return $this->belongsTo(CertificationClient::class, 'certification_client_id');
    }

    public function standard()
    {
        return $this->belongsTo(CertificationStandard::class, 'certification_standard_id');
    }

    public function accreditationBody()
    {
        return $this->belongsTo(CertificationAccreditationBody::class, 'certification_accreditation_body_id');
    }

    public function auditReports()
    {
        return $this->hasMany(CertificationAuditReport::class, 'certification_certificate_id');
    }

    public function scopeApproved(Builder $query)
    {
        return $query->whereIn('status', ['Approved', 'approved', ' APPROVED']);
    }

    public function scopePendingReview(Builder $query)
    {
        return $query->whereIn('status', ['Pending Review', 'Pending']);
    }

    public function scopePendingApproval(Builder $query)
    {
        return $query->whereIn('status', ['Pending Approval', 'Reviewed']);
    }

    public function scopeAssignedForReview(Builder $query, int $userId)
    {
        return $query->pendingReview()->where('review_by_id', $userId);
    }

    public function scopeAssignedForApproval(Builder $query, int $userId)
    {
        return $query->pendingApproval()->where('approval_by_id', $userId);
    }

    public function scopeAssignedToUser(Builder $query, int $userId)
    {
        return $query->where(function ($builder) use ($userId) {
            $builder->where(function ($inner) use ($userId) {
                $inner->whereIn('status', ['Pending Review', 'Pending'])
                    ->where('review_by_id', $userId);
            })->orWhere(function ($inner) use ($userId) {
                $inner->whereIn('status', ['Pending Approval', 'Reviewed'])
                    ->where('approval_by_id', $userId);
            });
        });
    }
}
