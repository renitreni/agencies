<?php

namespace App\Http\Livewire;

use App\Models\Candidate;
use App\Models\ForeignAgency;
use App\Models\JobOrder;
use App\Models\Voucher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardLivewire extends Component
{
    public int $totalVoucher   = 0;
    public int $totalDeployed  = 0;
    public int $totalCandidate = 0;
    
    // FRA Analytics
    public array $fraLabels = [];
    public array $fraTotalCounts = [];
    public array $fraDeployedCounts = [];
    public array $fraFinancials = [];
    public array $fraDetails = [];
    
    // Monthly Trend Year Filter
    public string $selectedYear = '';
    public array $availableYears = [];

    public function mount()
    {
        $agencyId = auth()->user()->agency_id;
        
        $this->totalVoucher   = Voucher::query()->where('agency_id', $agencyId)->count();
        $this->totalDeployed  = Voucher::query()
                                       ->where('status', 'deployed')
                                       ->where('agency_id', $agencyId)
                                       ->count();
        $this->totalCandidate = Candidate::query()->where('agency_id', $agencyId)->count();
        
        $this->loadAvailableYears();
        $this->selectedYear = $this->availableYears[0] ?? (string)now()->year;
        $this->loadFRAVoucherAnalytics();
        $this->loadMonthlyTrend();
    }

    public function setYear($year)
    {
        $this->selectedYear = $year;
        $this->loadMonthlyTrend();
    }

    private function loadAvailableYears()
    {
        $agencyId = auth()->user()->agency_id;
        
        $years = Voucher::query()
            ->where('agency_id', $agencyId)
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn($y) => (string)$y)
            ->toArray();
        
        $this->availableYears = $years ?: [(string)now()->year];
    }

    private function loadFRAVoucherAnalytics()
    {
        $agencyId = auth()->user()->agency_id;
        
        // Get FRAs for this agency
        $fras = ForeignAgency::query()
            ->select(['id', 'agency_name'])
            ->where('agency_id', $agencyId)
            ->get()
            ->keyBy('id');
        
        // Get voucher counts and financials per FRA via job_orders
        // Use LEFT JOIN to include FRAs even without vouchers
        $query = DB::table('foreign_agencies as fa')
            ->leftJoin('job_orders as jo', 'jo.foreign_agency_id', '=', 'fa.id')
            ->leftJoin('vouchers as v', function ($join) use ($agencyId) {
                $join->on('v.id', '=', 'jo.voucher_id')
                     ->where('v.agency_id', '=', $agencyId);
            })
            ->where('fa.agency_id', $agencyId);
        
        $fraVoucherData = $query->selectRaw('
                fa.id as fra_id,
                fa.agency_name,
                COUNT(DISTINCT v.id) as total,
                SUM(CASE WHEN v.status = "deployed" THEN 1 ELSE 0 END) as deployed,
                SUM(COALESCE(v.passporting_allowance,0)) as passporting,
                SUM(COALESCE(v.ticket,0)) as tickets,
                SUM(COALESCE(v.ticket_to_kuwait,0) + COALESCE(v.ticket_to_qatar,0)) as special_tickets,
                SUM(COALESCE(v.medical_allowance,0) + COALESCE(v.travel_allowance,0) + 
                    COALESCE(v.office_allowance,0) + COALESCE(v.owwa_allowance,0) + 
                    COALESCE(v.tesda_allowance,0) + COALESCE(v.weekly_allowance,0)) as allowances,
                SUM(COALESCE(v.nbi_renewal,0) + COALESCE(v.pdos,0) + 
                    COALESCE(v.info_sheet,0) + COALESCE(v.medical_follow_up,0) + 
                    COALESCE(v.fit_to_work,0) + COALESCE(v.stamping,0) + 
                    COALESCE(v.vaccine_fare,0)) as processing_fees,
                SUM(COALESCE(v.nbi_refund,0) + COALESCE(v.psa_refund,0) + 
                    COALESCE(v.passport_refund,0) + COALESCE(v.fare_refund,0) + 
                    COALESCE(v.red_rebon_nbi,0)) as refunds,
                SUM(COALESCE(v.repat,0) + COALESCE(v.total,0)) as other_costs
            ')
            ->groupBy('fa.id', 'fa.agency_name')
            ->get();
        
        $this->fraLabels = [];
        $this->fraTotalCounts = [];
        $this->fraDeployedCounts = [];
        $this->fraFinancials = [];
        $this->fraDetails = [];
        
        foreach ($fraVoucherData as $item) {
            $fraName = $item->agency_name ?? 'Unknown FRA';
            $total = (int) $item->total;
            $deployed = (int) $item->deployed;
            
            $this->fraLabels[] = $fraName;
            $this->fraTotalCounts[] = $total;
            $this->fraDeployedCounts[] = $deployed;
            
            $financials = [
                'passporting' => (float) $item->passporting,
                'tickets' => (float) $item->tickets,
                'special_tickets' => (float) $item->special_tickets,
                'allowances' => (float) $item->allowances,
                'processing_fees' => (float) $item->processing_fees,
                'refunds' => (float) $item->refunds,
                'other_costs' => (float) $item->other_costs,
            ];
            $this->fraFinancials[] = $financials;
            
            $totalCost = array_sum($financials);
            $this->fraDetails[] = [
                'name' => $fraName,
                'total' => $total,
                'deployed' => $deployed,
                'rate' => $total > 0 ? round($deployed / $total * 100, 1) : 0,
                'avg_cost' => $total > 0 ? round($totalCost / $total, 0) : 0,
                'total_cost' => round($totalCost, 0),
            ];
        }
        
        // If no FRAs exist at all, provide empty state
        if (empty($this->fraLabels)) {
            $this->fraLabels = ['No Data'];
            $this->fraTotalCounts = [0];
            $this->fraDeployedCounts = [0];
            $this->fraFinancials = [[]];
            $this->fraDetails = [['name' => 'No Data', 'total' => 0, 'deployed' => 0, 'rate' => 0, 'avg_cost' => 0, 'total_cost' => 0]];
        }
    }

    private function loadMonthlyTrend()
    {
        $agencyId = auth()->user()->agency_id;
        
        $query = Voucher::query()->where('agency_id', $agencyId);
        $query->whereYear('created_at', (int)$this->selectedYear);
        
        $monthData = $query->selectRaw('MONTH(created_at) as month, MONTHNAME(created_at) as month_name, COUNT(*) as total')
            ->groupBy('month', 'month_name')
            ->orderBy('month')
            ->get();
        
        $this->monthLabels = [];
        $this->monthCounts = [];
        
        $dataMap = $monthData->keyBy('month');
        for ($m = 1; $m <= 12; $m++) {
            $this->monthLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            $this->monthCounts[] = $dataMap->has($m) ? (int) $dataMap[$m]->total : 0;
        }
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.dashboard');
    }
}
