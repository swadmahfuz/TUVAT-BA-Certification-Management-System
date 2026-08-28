<?php

namespace App\Imports;

use App\Models\CertificationCertificate;
use App\Models\CertificationClient;
use App\Models\CertificationStandard;
use App\Models\CertificationAccreditationBody;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/*
|--------------------------------------------------------------------------
| TUVAT BA Certification Management System
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Head of Divison - Business Assurance & Training, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
| Latest Stable Release: v5.1.0 -  29 August 2026
|--------------------------------------------------------------------------
*/

class CertificateImport implements ToModel, WithHeadingRow
{
    /**
     * Import one BA certificate record from each Excel row.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $loggedInUser = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | 1. Basic required check
        |--------------------------------------------------------------------------
        */

        if (empty($row['client_name'])) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Client create / find
        |--------------------------------------------------------------------------
        */

        $client = CertificationClient::firstOrCreate(
            [
                'client_name' => trim($row['client_name']),
            ],
            [
                'client_address' => $row['client_address'] ?? null,
                'contact_person' => $row['contact_person'] ?? null,
                'email'          => $row['email'] ?? null,
                'phone'          => $row['phone'] ?? null,
                'remarks'        => $row['client_remarks'] ?? null,
                'created_by'     => $loggedInUser ? $loggedInUser->name : null,
                'created_by_id'  => $loggedInUser ? $loggedInUser->id : null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 3. Standard create / find
        |--------------------------------------------------------------------------
        */

        $standard = null;

        if (!empty($row['standard_name'])) {
            $standard = CertificationStandard::firstOrCreate(
                [
                    'standard_name' => trim($row['standard_name']),
                ],
                [
                    'standard_code' => $row['standard_code'] ?? null,
                    'status'        => 'Active',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Accreditation body create / find
        |--------------------------------------------------------------------------
        */

        $accreditationBody = null;

        if (!empty($row['accreditation_body'])) {
            $accreditationBody = CertificationAccreditationBody::firstOrCreate(
                [
                    'accreditation_body_name' => trim($row['accreditation_body']),
                ],
                [
                    'short_name' => $row['accreditation_body_short_name'] ?? null,
                    'status'     => 'Active',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Reviewer and approver
        |--------------------------------------------------------------------------
        | Supports both:
        | - review_by_email / approval_by_email
        | - review_by / approval_by
        |--------------------------------------------------------------------------
        */

        $reviewUser = null;
        $approvalUser = null;

        if (!empty($row['review_by_email'])) {
            $reviewUser = User::where('email', $row['review_by_email'])->first();
        }

        if (!$reviewUser && !empty($row['review_by'])) {
            $reviewUser = User::where('name', $row['review_by'])->first();
        }

        if (!empty($row['approval_by_email'])) {
            $approvalUser = User::where('email', $row['approval_by_email'])->first();
        }

        if (!$approvalUser && !empty($row['approval_by'])) {
            $approvalUser = User::where('name', $row['approval_by'])->first();
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Date handling
        |--------------------------------------------------------------------------
        */

        $certificateIssueDate = $this->parseDate($row['certificate_issue_date'] ?? null);
        $certificateExpiryDate = $this->parseDate($row['certificate_expiry_date'] ?? null);
        $initialAuditCompletionDate = $this->parseDate($row['initial_certification_audit_completion_date'] ?? null);

        $cycleDates = $this->calculateCycleDates($initialAuditCompletionDate, $certificateExpiryDate);

        /*
        |--------------------------------------------------------------------------
        | 7. Certificate number handling
        |--------------------------------------------------------------------------
        | If certificate_number exists, update existing record.
        | If blank, create new record.
        |--------------------------------------------------------------------------
        */

        $certificateNumber = !empty($row['certificate_number']) ? trim($row['certificate_number']) : null;

        $data = [
            'certification_client_id'                       => $client->id,
            'certification_standard_id'                     => $standard ? $standard->id : null,
            'certification_accreditation_body_id'           => $accreditationBody ? $accreditationBody->id : null,

            'certificate_number'                            => $certificateNumber,
            'certificate_scope'                             => $row['certificate_scope'] ?? null,
            'certification_cycle'                           => $row['certification_cycle'] ?? null,

            'certificate_issue_date'                        => $certificateIssueDate,
            'certificate_expiry_date'                       => $certificateExpiryDate,
            'initial_certification_audit_completion_date'   => $initialAuditCompletionDate,

            'surveillance_1_due_date'                       => $cycleDates['surveillance_1_due_date'],
            'surveillance_2_due_date'                       => $cycleDates['surveillance_2_due_date'],
            'recertification_due_date'                      => $cycleDates['recertification_due_date'],
            'grace_period_end_date'                         => $cycleDates['grace_period_end_date'],

            'audit_status'                                  => $row['audit_status'] ?? 'Not Scheduled',
            'certificate_status'                            => $row['certificate_status'] ?? 'Active',
            'status'                                        => 'Pending Review',

            'lead_auditor'                                  => $row['lead_auditor'] ?? null,
            'auditor_1'                                     => $row['auditor_1'] ?? null,
            'auditor_2'                                     => $row['auditor_2'] ?? null,
            'auditor_3'                                     => $row['auditor_3'] ?? null,
            'technical_expert'                              => $row['technical_expert'] ?? null,

            'created_by'                                    => $loggedInUser ? $loggedInUser->name : null,
            'created_by_id'                                 => $loggedInUser ? $loggedInUser->id : null,

            'review_by'                                     => $reviewUser ? $reviewUser->name : ($row['review_by'] ?? null),
            'review_by_id'                                  => $reviewUser ? $reviewUser->id : null,

            'approval_by'                                   => $approvalUser ? $approvalUser->name : ($row['approval_by'] ?? null),
            'approval_by_id'                                => $approvalUser ? $approvalUser->id : null,

            'updated_by'                                    => $loggedInUser ? $loggedInUser->name : null,
            'updated_by_id'                                 => $loggedInUser ? $loggedInUser->id : null,

            'remarks'                                       => $row['remarks'] ?? null,
        ];

        if ($certificateNumber) {
            return CertificationCertificate::updateOrCreate(
                [
                    'certificate_number' => $certificateNumber,
                ],
                $data
            );
        }

        return new CertificationCertificate($data);
    }

    /**
     * Convert Excel / text date into Y-m-d format.
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calculate S1, S2, recertification and grace period dates.
     */
    private function calculateCycleDates($initialAuditCompletionDate, $certificateExpiryDate)
    {
        $surveillance1DueDate = null;
        $surveillance2DueDate = null;
        $recertificationDueDate = null;
        $gracePeriodEndDate = null;

        if ($initialAuditCompletionDate) {
            $initialDate = Carbon::parse($initialAuditCompletionDate);

            $surveillance1DueDate = $initialDate->copy()->addMonths(12)->format('Y-m-d');
            $surveillance2DueDate = $initialDate->copy()->addMonths(24)->format('Y-m-d');
            $recertificationDueDate = $initialDate->copy()->addMonths(36)->format('Y-m-d');
        }

        if ($certificateExpiryDate) {
            $gracePeriodEndDate = Carbon::parse($certificateExpiryDate)->addMonths(6)->format('Y-m-d');
        }

        return [
            'surveillance_1_due_date'  => $surveillance1DueDate,
            'surveillance_2_due_date'  => $surveillance2DueDate,
            'recertification_due_date' => $recertificationDueDate,
            'grace_period_end_date'    => $gracePeriodEndDate,
        ];
    }
}