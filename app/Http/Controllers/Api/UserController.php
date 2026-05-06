<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Services\IncomeAnalyzer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly IncomeAnalyzer $analyzer) {}

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('kycProfile');
        $kyc  = $user->kycProfile;

        $analysis = $kyc ? $this->analyzer->analyzeUser($user, 'annual') : null;

        return ApiResponse::success([
            'name'                => $user->name,
            'national_id'         => $user->national_id,
            'email'               => $user->email,
            'kyc_status'          => $kyc?->status,
            'risk_level'          => $kyc?->risk_level,
            'annual_income_limit' => $kyc ? ApiResponse::amd($kyc->annual_income_limit) : null,
            'current_year_income' => $analysis ? ApiResponse::amd($analysis['total_income']) : null,
            'is_over_limit'       => $analysis['is_over_limit'] ?? false,
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $user  = $request->user();
        $year  = $request->query('year', now()->year);
        $month = $request->query('month');

        $query = $user->transactions()->orderByDesc('transaction_date');

        $query->whereYear('transaction_date', $year);

        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }

        $paginated = $query->paginate(20);

        $items = collect($paginated->items())->map(fn($tx) => [
            'id'                 => $tx->id,
            'transaction_type'   => $tx->transaction_type,
            'amount'             => ApiResponse::amd($tx->amount),
            'description'        => $tx->description,
            'transaction_date'   => $tx->transaction_date->toDateString(),
            'source_type'        => $tx->source_type,
            'is_flagged'         => $tx->is_flagged,
            'external_reference' => $tx->external_reference,
        ]);

        return ApiResponse::success([
            'data'         => $items,
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'total'        => $paginated->total(),
            'per_page'     => $paginated->perPage(),
        ]);
    }

    public function incomeSummary(Request $request): JsonResponse
    {
        $user     = $request->user()->load('kycProfile');
        $analysis = $this->analyzer->analyzeUser($user, 'annual');

        $analysis['total_income']  = ApiResponse::amd($analysis['total_income']);
        $analysis['income_limit']  = ApiResponse::amd($analysis['income_limit']);
        $analysis['excess_income'] = ApiResponse::amd($analysis['excess_income']);
        $analysis['tax_breakdown']['tax_amount'] = ApiResponse::amd($analysis['tax_breakdown']['tax_amount']);

        return ApiResponse::success($analysis);
    }

    public function taxReports(Request $request): JsonResponse
    {
        $reports = $request->user()
            ->taxReports()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'id'                  => $r->id,
                'period_start'        => $r->report_period_start->toDateString(),
                'period_end'          => $r->report_period_end->toDateString(),
                'total_income'        => ApiResponse::amd($r->total_income),
                'income_limit'        => ApiResponse::amd($r->income_limit),
                'excess_income'       => ApiResponse::amd($r->excess_income),
                'tax_rate'            => $r->tax_rate,
                'tax_amount'          => ApiResponse::amd($r->tax_amount),
                'status'              => $r->status,
                'submitted_to_gov_at' => $r->submitted_to_gov_at?->toDateTimeString(),
            ]);

        return ApiResponse::success($reports);
    }

    public function notifications(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'is_read'    => $n->is_read,
                'metadata'   => $n->metadata,
                'created_at' => $n->created_at->toDateTimeString(),
            ]);

        return ApiResponse::success($notifications);
    }

    public function markNotificationRead(Request $request, int $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->update(['is_read' => true]);

        return ApiResponse::success(['id' => $notification->id, 'is_read' => true], 'Notification marked as read.');
    }
}
