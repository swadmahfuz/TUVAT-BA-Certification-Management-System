<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CertificationCertificate;
use App\Models\CertificationClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function data(): array
    {
        $today = Carbon::today();
        $next90Days = Carbon::today()->addDays(90);

        $total = CertificationCertificate::count();
        $pendingReview = CertificationCertificate::pendingReview()->count();
        $pendingApproval = CertificationCertificate::pendingApproval()->count();
        $expired = CertificationCertificate::approved()
            ->whereNotNull('certificate_expiry_date')
            ->whereDate('certificate_expiry_date', '<', $today)
            ->count();
        $approved = CertificationCertificate::approved()
            ->where(function ($query) use ($today) {
                $query->whereNull('certificate_expiry_date')
                    ->orWhereDate('certificate_expiry_date', '>=', $today);
            })
            ->count();

        $statusCounts = [
            'Approved' => $approved,
            'Pending Review' => $pendingReview,
            'Pending Approval' => $pendingApproval,
            'Expired' => $expired,
        ];

        $myAssignments = $this->myAssignments();

        return [
            'stats' => [
                'total' => $total,
                'approved' => $approved,
                'pending_review' => $pendingReview,
                'pending_approval' => $pendingApproval,
                'expired' => $expired,
                'total_clients' => CertificationClient::count(),
                'active_certificates' => CertificationCertificate::where('certificate_status', 'Active')->count(),
                'upcoming_surveillance_1' => CertificationCertificate::whereBetween('surveillance_1_due_date', [$today, $next90Days])->count(),
                'upcoming_surveillance_2' => CertificationCertificate::whereBetween('surveillance_2_due_date', [$today, $next90Days])->count(),
                'upcoming_recertification' => CertificationCertificate::whereBetween('recertification_due_date', [$today, $next90Days])->count(),
                'expired_within_grace' => CertificationCertificate::whereDate('certificate_expiry_date', '<', $today)
                    ->whereDate('grace_period_end_date', '>=', $today)
                    ->count(),
                'expired_beyond_grace' => CertificationCertificate::whereDate('grace_period_end_date', '<', $today)->count(),
            ],
            'myAssignments' => $myAssignments,
            'percentages' => collect($statusCounts)->map(function ($count) use ($total) {
                return $total > 0 ? round(($count / $total) * 100, 1) : 0;
            })->all(),
            'statusChart' => [
                'labels' => array_keys($statusCounts),
                'values' => array_values($statusCounts),
            ],
            'monthlyChart' => $this->monthlyIssues(),
            'recentCertificates' => CertificationCertificate::with(['client', 'standard'])
                ->latest('created_at')
                ->limit(5)
                ->get([
                    'id',
                    'certification_client_id',
                    'certification_standard_id',
                    'certificate_number',
                    'status',
                    'certificate_issue_date',
                ]),
            'recentActivities' => $this->recentActivities(),
        ];
    }

    public function myAssignments(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return [
                'review' => 0,
                'approval' => 0,
                'total' => 0,
            ];
        }

        $review = CertificationCertificate::assignedForReview($userId)->count();
        $approval = CertificationCertificate::assignedForApproval($userId)->count();

        return [
            'review' => $review,
            'approval' => $approval,
            'total' => $review + $approval,
        ];
    }

    private function monthlyIssues(): array
    {
        $months = collect(range(11, 1))->map(function ($monthsAgo) {
            return now()->startOfMonth()->subMonths($monthsAgo);
        })->push(now()->startOfMonth());

        $start = $months->first()->format('Y-m-d');
        $counts = CertificationCertificate::whereNotNull('certificate_issue_date')
            ->where('certificate_issue_date', '>=', $start)
            ->get(['certificate_issue_date'])
            ->groupBy(function ($certificate) {
                try {
                    return Carbon::parse($certificate->certificate_issue_date)->format('Y-m');
                } catch (\Throwable $exception) {
                    return 'invalid';
                }
            })
            ->map->count();

        return [
            'labels' => $months->map->format('M')->all(),
            'values' => $months->map(function ($month) use ($counts) {
                return $counts->get($month->format('Y-m'), 0);
            })->all(),
        ];
    }

    private function recentActivities()
    {
        if (!Schema::hasTable('certification_activity_logs')) {
            return collect();
        }

        return ActivityLog::latest('created_at')->limit(6)->get();
    }
}
