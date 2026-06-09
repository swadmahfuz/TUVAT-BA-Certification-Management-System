<?php

namespace App\Exports;

use App\Models\BaCertificate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Certificate Verification System (CVS) 
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Assistant Manager - Sales & Operations, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
|--------------------------------------------------------------------------
*/

class CertificateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Export BA certificate records with related client, standard and accreditation body.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return BaCertificate::with([
                'client',
                'standard',
                'accreditationBody'
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($certificate) {
                return [
                    'db_id'                                      => $certificate->id,

                    'client_name'                                => $certificate->client->client_name ?? null,
                    'client_address'                             => $certificate->client->client_address ?? null,
                    'contact_person'                             => $certificate->client->contact_person ?? null,
                    'email'                                      => $certificate->client->email ?? null,
                    'phone'                                      => $certificate->client->phone ?? null,

                    'standard_name'                              => $certificate->standard->standard_name ?? null,
                    'standard_code'                              => $certificate->standard->standard_code ?? null,

                    'accreditation_body'                         => $certificate->accreditationBody->accreditation_body_name ?? null,
                    'accreditation_body_short_name'              => $certificate->accreditationBody->short_name ?? null,

                    'certificate_number'                         => $certificate->certificate_number,
                    'certificate_scope'                          => $certificate->certificate_scope,
                    'certification_cycle'                        => $certificate->certification_cycle,

                    'certificate_issue_date'                     => $this->formatDate($certificate->certificate_issue_date),
                    'certificate_expiry_date'                    => $this->formatDate($certificate->certificate_expiry_date),
                    'initial_certification_audit_completion_date'=> $this->formatDate($certificate->initial_certification_audit_completion_date),

                    'surveillance_1_due_date'                    => $this->formatDate($certificate->surveillance_1_due_date),
                    'surveillance_2_due_date'                    => $this->formatDate($certificate->surveillance_2_due_date),
                    'recertification_due_date'                   => $this->formatDate($certificate->recertification_due_date),
                    'grace_period_end_date'                      => $this->formatDate($certificate->grace_period_end_date),

                    'audit_status'                               => $certificate->audit_status,
                    'certificate_status'                         => $certificate->certificate_status,
                    'workflow_status'                            => $certificate->status,

                    'lead_auditor'                               => $certificate->lead_auditor,
                    'auditor_1'                                  => $certificate->auditor_1,
                    'auditor_2'                                  => $certificate->auditor_2,
                    'auditor_3'                                  => $certificate->auditor_3,
                    'technical_expert'                           => $certificate->technical_expert,

                    'created_by'                                 => $certificate->created_by,
                    'created_by_id'                              => $certificate->created_by_id,
                    'created_at'                                 => $this->formatDateTime($certificate->created_at),

                    'review_by'                                  => $certificate->review_by,
                    'review_by_id'                               => $certificate->review_by_id,
                    'reviewed_at'                                => $this->formatDateTime($certificate->reviewed_at),

                    'approval_by'                                => $certificate->approval_by,
                    'approval_by_id'                             => $certificate->approval_by_id,
                    'approved_at'                                => $this->formatDateTime($certificate->approved_at),

                    'updated_by'                                 => $certificate->updated_by,
                    'updated_by_id'                              => $certificate->updated_by_id,
                    'updated_at'                                 => $this->formatDateTime($certificate->updated_at),

                    'deleted_by'                                 => $certificate->deleted_by,
                    'deleted_by_id'                              => $certificate->deleted_by_id,
                    'deleted_at'                                 => $this->formatDateTime($certificate->deleted_at),

                    'certificate_pdf'                            => $certificate->certificate_pdf,
                    'pdf_uploaded_by'                            => $certificate->pdf_uploaded_by,
                    'pdf_uploaded_by_id'                         => $certificate->pdf_uploaded_by_id,
                    'pdf_uploaded_at'                            => $this->formatDateTime($certificate->pdf_uploaded_at),

                    'remarks'                                    => $certificate->remarks,
                ];
            });
    }

    /**
     * Export heading row.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'DB ID',

            'Client Name',
            'Client Address',
            'Contact Person',
            'Email',
            'Phone',

            'Standard Name',
            'Standard Code',

            'Accreditation Body',
            'Accreditation Body Short Name',

            'Certificate Number',
            'Certificate Scope',
            'Certification Cycle',

            'Certificate Issue Date',
            'Certificate Expiry Date',
            'Initial Certification Audit Completion Date',

            'Surveillance 1 Due Date',
            'Surveillance 2 Due Date',
            'Recertification Due Date',
            'Grace Period End Date',

            'Audit Status',
            'Certificate Status',
            'Workflow Status',

            'Lead Auditor',
            'Auditor 1',
            'Auditor 2',
            'Auditor 3',
            'Technical Expert',

            'Created By',
            'Created By ID',
            'Created At',

            'Review By',
            'Review By ID',
            'Reviewed At',

            'Approval By',
            'Approval By ID',
            'Approved At',

            'Updated By',
            'Updated By ID',
            'Updated At',

            'Deleted By',
            'Deleted By ID',
            'Deleted At',

            'Certificate PDF',
            'PDF Uploaded By',
            'PDF Uploaded By ID',
            'PDF Uploaded At',

            'Remarks',
        ];
    }

    /**
     * Format date as DD-MM-YYYY.
     */
    private function formatDate($date)
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format('d-m-Y');
    }

    /**
     * Format datetime as DD-MM-YYYY HH:MM AM/PM.
     */
    private function formatDateTime($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        return Carbon::parse($dateTime)->format('d-m-Y h:i A');
    }
}