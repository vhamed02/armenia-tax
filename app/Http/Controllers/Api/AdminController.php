<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Jobs\ScanUserTransactions;
use App\Models\TaxReport;
use App\Models\User;
use App\Services\IncomeAnalyzer;
use App\Services\MonitoringService;
use App\Services\ReportingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly ReportingService $reporting,
        private readonly IncomeAnalyzer $analyzer,
        private readonly MonitoringService $monitoring,
    ) {}

    public function dashboardStats(): JsonResponse
    {
        $stats       = $this->reporting->getDashboardStats();
        $tenantStats = $this->monitoring->getDashboardTenantStats();

        $stats['total_tax_due_amd']       = ApiResponse::amd($stats['total_tax_due_amd']);
        $stats['tenant_count']            = $tenantStats['active_service_providers'];
        $stats['total_wallet_volume_amd'] = ApiResponse::amd($tenantStats['total_wallet_volume_amd']);
        $stats['cross_tenant_flagged_users'] = $tenantStats['cross_platform_users_over_limit'];

        return ApiResponse::success($stats);
    }

    public function users(Request $request): JsonResponse
    {
        $paginated = User::where('is_admin', false)
            ->whereHas('kycProfile', fn($q) => $q->where('status', 'verified'))
            ->with('kycProfile')
            ->paginate(20);

        $items = collect($paginated->items())->map(function ($user) {
            $kyc      = $user->kycProfile;
            $analysis = $this->analyzer->analyzeUser($user, 'annual');

            return [
                'id'                  => $user->id,
                'name'                => $user->name,
                'national_id'         => $user->national_id,
                'email'               => $user->email,
                'risk_level'          => $kyc->risk_level,
                'income_limit'        => ApiResponse::amd($kyc->annual_income_limit),
                'current_year_income' => ApiResponse::amd($analysis['total_income']),
                'is_over_limit'       => $analysis['is_over_limit'],
                'tax_due'             => ApiResponse::amd($analysis['tax_breakdown']['tax_amount']),
            ];
        });

        return ApiResponse::success([
            'data'         => $items,
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'total'        => $paginated->total(),
            'per_page'     => $paginated->perPage(),
        ]);
    }

    public function userReport(int $id): JsonResponse
    {
        $user   = User::with(['kycProfile', 'bankAccounts', 'transactions', 'taxReports', 'notifications', 'scanningJobs'])->findOrFail($id);
        $report = $this->reporting->getUserFullReport($user);

        $report['income_analysis']['total_income']  = ApiResponse::amd($report['income_analysis']['total_income']);
        $report['income_analysis']['income_limit']  = ApiResponse::amd($report['income_analysis']['income_limit']);
        $report['income_analysis']['excess_income'] = ApiResponse::amd($report['income_analysis']['excess_income']);
        $report['income_analysis']['tax_breakdown']['tax_amount'] = ApiResponse::amd(
            $report['income_analysis']['tax_breakdown']['tax_amount']
        );

        return ApiResponse::success($report);
    }

    public function taxReports(Request $request): JsonResponse
    {
        $query = TaxReport::with('user')->orderByDesc('created_at');

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $paginated = $query->paginate(20);

        $items = collect($paginated->items())->map(fn($r) => [
            'id'                  => $r->id,
            'user_id'             => $r->user_id,
            'user_name'           => $r->user->name,
            'national_id'         => $r->user->national_id,
            'period_start'        => $r->report_period_start->toDateString(),
            'period_end'          => $r->report_period_end->toDateString(),
            'total_income'        => ApiResponse::amd($r->total_income),
            'income_limit'        => ApiResponse::amd($r->income_limit),
            'excess_income'       => ApiResponse::amd($r->excess_income),
            'tax_rate'            => $r->tax_rate,
            'tax_amount'          => ApiResponse::amd($r->tax_amount),
            'status'              => $r->status,
            'submitted_to_gov_at' => $r->submitted_to_gov_at?->toDateTimeString(),
            'metadata'            => $r->metadata,
        ]);

        return ApiResponse::success([
            'data'         => $items,
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'total'        => $paginated->total(),
            'per_page'     => $paginated->perPage(),
        ]);
    }

    public function submitTaxReport(int $id): JsonResponse
    {
        $report   = TaxReport::findOrFail($id);
        $updated  = $this->reporting->submitTaxReport($report);

        return ApiResponse::success([
            'id'                  => $updated->id,
            'status'              => $updated->status,
            'submitted_to_gov_at' => $updated->submitted_to_gov_at->toDateTimeString(),
            'tax_amount'          => ApiResponse::amd($updated->tax_amount),
        ], 'Tax report submitted successfully.');
    }

    public function anomalies(): JsonResponse
    {
        $anomalies = $this->reporting->getAnomaliesAcrossAllUsers();

        $mapped = array_map(function ($a) {
            $a['amount'] = ApiResponse::amd($a['amount']);
            return $a;
        }, $anomalies);

        return ApiResponse::success($mapped);
    }

    public function triggerScan(): JsonResponse
    {
        $users = User::whereHas('kycProfile', fn($q) => $q->where('status', 'verified'))->get();

        foreach ($users as $user) {
            ScanUserTransactions::dispatch($user);
        }

        return ApiResponse::success([
            'message'   => 'Scan initiated',
            'job_count' => $users->count(),
        ], 'Scan jobs dispatched successfully.');
    }
}
