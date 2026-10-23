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
    
    public array $fraLabels = [];
    public array $fraCounts = [];

    public function mount()
    {
        $this->totalVoucher   = Voucher::query()->where('agency_id', auth()->user()->agency_id)->count();
        $this->totalDeployed  = Voucher::query()
                                       ->where('status', 'deployed')
                                       ->where('agency_id', auth()->user()->agency_id)
                                       ->count();
        $this->totalCandidate = Candidate::query()->where('agency_id', auth()->user()->agency_id)->count();
        
        $this->loadFRAVoucherAnalytics();
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
        
        // Get voucher counts per FRA via job_orders
        $fraVoucherCounts = DB::table('vouchers')
            ->join('job_orders', 'job_orders.voucher_id', '=', 'vouchers.id')
            ->where('vouchers.agency_id', $agencyId)
            ->selectRaw('job_orders.foreign_agency_id, COUNT(DISTINCT vouchers.id) as total')
            ->groupBy('job_orders.foreign_agency_id')
            ->get();
        
        $this->fraLabels = [];
        $this->fraCounts = [];
        
        foreach ($fraVoucherCounts as $item) {
            $fraName = $fras->get($item->foreign_agency_id)?->agency_name ?? 'Unknown FRA';
            $this->fraLabels[] = $fraName;
            $this->fraCounts[] = (int) $item->total;
        }
        
        // If no data, provide empty arrays for chart
        if (empty($this->fraLabels)) {
            $this->fraLabels = ['No Data'];
            $this->fraCounts = [0];
        }
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.dashboard');
    }
}
