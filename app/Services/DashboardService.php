<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CertificationCertificate;
use App\Models\CertificationClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function __construct(private CertificateFilterService $certificateFilters)
    {
    }

    public function data(): array
    {
        $ttl = config('cvs.cache_ttl.dashboard', 300);
        $userId = Auth::id() ?? 0;

        return Cache::remember(
            'cvs.dashboard.' . config('cvs.app_key') . '.' . $userId,
            $ttl,
            fn () => $this->buildData()
        );
    }

    private function buildData(): array
    {
        $today = Carbon::today();
        $next90Days = Carbon::today()->addDays(90);

        $baseQuery = CertificationCertificate::query();
        $total = (clone $baseQuery)->count();
        $pendingReview = CertificationCertificate::pendingReview()->count();
        $pendingApproval = CertificationCertificate::pendingApproval()->count();
        $expired = $this->certificateFilters->countExpired(clone $baseQuery);
        $approved = $this->certificateFilters->countApproved(clone $baseQuery);
        $expiring30 = $this->certificateFilters->countExpiringWithin(clone $baseQuery, 30);
        $expiring60 = $this->certificateFilters->countExpiringWithin(clone $baseQuery, 60);
        $expiring90 = $this->certificateFilters->countExpiringWithin(clone $baseQuery, 90);

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
                'expiring_30' => $expiring30,
                'expiring_60' => $expiring60,
                'expiring_90' => $expiring90,
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
        $months = collect(range(11, 0))->map(function ($monthsAgo) {
            return now()->startOfMonth()->subMonths($monthsAgo);
        });

        $start = $months->first()->format('Y-m-d');

        $driver = DB::connection()->getDriverName();
        $monthExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m', certificate_issue_date)"
            : "DATE_FORMAT(certificate_issue_date, '%Y-%m')";

        $counts = CertificationCertificate::query()
            ->whereNotNull('certificate_issue_date')
            ->where('certificate_issue_date', '>=', $start)
            ->selectRaw($monthExpression . ' as month_key, COUNT(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        return [
            'labels' => $months->map->format('M')->all(),
            'values' => $months->map(function ($month) use ($counts) {
                return (int) $counts->get($month->format('Y-m'), 0);
            })->all(),
        ];
    }

    private function recentActivities()
    {
        $table = config('cvs.app_key', 'certification') . '_activity_logs';

        if (!Schema::hasTable($table)) {
            return collect();
        }

        return ActivityLog::latest('created_at')->limit(6)->get();
    }
}

