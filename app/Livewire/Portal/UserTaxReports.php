<?php

namespace App\Livewire\Portal;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserTaxReports extends Component
{
    public array $reports = [];

    public function mount(): void
    {
        $this->reports = Auth::user()
            ->taxReports()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'period_start' => $r->report_period_start->toDateString(),
                'period_end'   => $r->report_period_end->toDateString(),
                'total_income' => $r->total_income,
                'income_limit' => $r->income_limit,
                'excess_income'=> $r->excess_income,
                'tax_amount'   => $r->tax_amount,
                'tax_rate'     => $r->tax_rate,
                'status'       => $r->status,
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.portal.user-tax-reports')
            ->layout('layouts.portal', ['title' => 'My Tax Reports']);
    }
}
