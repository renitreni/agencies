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
    
    // Date Filter
    public string $dateFilter = '90';
    public string $filterLabel = 'Last 90 Days';
    
    // Status Distribution
    public array $statusLabels = [];
    public array $statusCounts = [];
    
    // Monthly Trend
    public array $monthLabels = [];
    public array $monthCounts = [];

    public function mount()
    {
        $agencyId = auth()->user()->agency_id;
        
        $this->totalVoucher   = Voucher::query()->where('agency_id', $agencyId)->count();
        $this->totalDeployed  = Voucher::query()
                                       ->where('status', 'deployed')
                                       ->where('agency_id', $agencyId)
                                       ->count();
        $this->totalCandidate = Candidate::query()->where('agency_id', $agencyId)->count();
        
        $this->loadFRAVoucherAnalytics();
        $this->loadStatusDistribution();
        $this->loadMonthlyTrend();
    }

    public function setDateFilter($days)
    {
        $this->dateFilter = $days;
        $labels = ['30' => 'Last 30 Days', '90' => 'Last 90 Days', '365' => 'Last 365 Days', 'all' => 'All Time'];
        $this->filterLabel = $labels[$days] ?? 'Last 90 Days';
        $this->loadFRAVoucherAnalytics();
        $this->loadStatusDistribution();
        $this->loadMonthlyTrend();
    }

    private function getDateFilterQuery($query)
    {
        if ($this->dateFilter !== 'all') {
            $query->whereDate('vouchers.created_at', '>=', now()->subDays((int)$this->dateFilter));
        }
        return $query;
    }

    private function loadFRAVoucherAnalytics()
    {
        $agencyId = auth()->user()->agency_id;
        
        $fras = ForeignAgency::query()
            ->select(['id', 'agency_name'])
            ->where('agency_id', $agencyId)
            ->get()
            ->keyBy('id');
        
        $query = DB::table('vouchers')
            ->join('job_orders', 'job_orders.voucher_id', '=', 'vouchers.id')
            ->where('vouchers.agency_id', $agencyId);
        
        $this->getDateFilterQuery($query);
        
        $fraVoucherData = $query->selectRaw('
                job_orders.foreign_agency_id,
                COUNT(DISTINCT vouchers.id) as total,
                SUM(CASE WHEN vouchers.status = "deployed" THEN 1 ELSE 0 END) as deployed,
                SUM(COALESCE(vouchers.passporting_allowance,0)) as passporting,
                SUM(COALESCE(vouchers.ticket,0)) as tickets,
                SUM(COALESCE(vouchers.ticket_to_kuwait,0) + COALESCE(vouchers.ticket_to_qatar,0)) as special_tickets,
                SUM(COALESCE(vouchers.medical_allowance,0) + COALESCE(vouchers.travel_allowance,0) + 
                    COALESCE(vouchers.office_allowance,0) + COALESCE(vouchers.owwa_allowance,0) + 
                    COALESCE(vouchers.tesda_allowance,0) + COALESCE(vouchers.weekly_allowance,0)) as allowances,
                SUM(COALESCE(vouchers.nbi_renewal,0) + COALESCE(vouchers.pdos,0) + 
                    COALESCE(vouchers.info_sheet,0) + COALESCE(vouchers.medical_follow_up,0) + 
                    COALESCE(vouchers.fit_to_work,0) + COALESCE(vouchers.stamping,0) + 
                    COALESCE(vouchers.vaccine_fare,0)) as processing_fees,
                SUM(COALESCE(vouchers.nbi_refund,0) + COALESCE(vouchers.psa_refund,0) + 
                    COALESCE(vouchers.passport_refund,0) + COALESCE(vouchers.fare_refund,0) + 
                    COALESCE(vouchers.red_rebon_nbi,0)) as refunds,
                SUM(COALESCE(vouchers.repat,0) + COALESCE(vouchers.total,0)) as other_costs
            ')
            ->groupBy('job_orders.foreign_agency_id')
            ->get();
        
        $this->fraLabels = [];
        $this->fraTotalCounts = [];
        $this->fraDeployedCounts = [];
        $this->fraFinancials = [];
        $this->fraDetails = [];
        
        foreach ($fraVoucherData as $item) {
            $fraName = $fras->get($item->foreign_agency_id)?->agency_name ?? 'Unknown FRA';
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
        
        if (empty($this->fraLabels)) {
            $this->fraLabels = ['No Data'];
            $this->fraTotalCounts = [0];
            $this->fraDeployedCounts = [0];
            $this->fraFinancials = [[]];
            $this->fraDetails = [['name' => 'No Data', 'total' => 0, 'deployed' => 0, 'rate' => 0, 'avg_cost' => 0, 'total_cost' => 0]];
        }
    }

    private function loadStatusDistribution()
    {
        $agencyId = auth()->user()->agency_id;
        
        $query = Voucher::query()->where('agency_id', $agencyId);
        $this->getDateFilterQuery($query);
        
        $statusData = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        $this->statusLabels = [];
        $this->statusCounts = [];
        
        foreach ($statusData as $item) {
            $label = $item->status ? ucfirst(str_replace('-', ' ', $item->status)) : 'Unknown';
            $this->statusLabels[] = $label;
            $this->statusCounts[] = (int) $item->count;
        }
        
        if (empty($this->statusLabels)) {
            $this->statusLabels = ['No Data'];
            $this->statusCounts = [0];
        }
    }

    private function loadMonthlyTrend()
    {
        $agencyId = auth()->user()->agency_id;
        
        $query = Voucher::query()->where('agency_id', $agencyId);
        $query->whereYear('created_at', now()->year);
        
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
