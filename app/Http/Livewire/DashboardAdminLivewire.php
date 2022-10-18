<?php

namespace App\Http\Livewire;

use App\Models\Agency;
use App\Models\Candidate;
use App\Models\Complains;
use App\Models\Report;
use App\Models\Rescue;
use Livewire\Component;

class DashboardAdminLivewire extends Component
{
    public array $results = [];

    public $keyword;

    public $keyIn = 0;

    public array $candidate = [];

    public array $recues = [];

    public int $reportCount = 0;

    public int $casesCount = 0;

    public int $agencyCount = 0;

    public int $rescueCount = 0;

    public function mount()
    {
        $this->reportCount = Report::query()->count();
        $this->casesCount  = Complains::query()->count();
        $this->agencyCount = Agency::query()->count();
    }

    public function render()
    {
        $this->rescueCount = Rescue::query()
                                   ->leftJoin('responds as rs', 'rs.rescue_id', '=', 'rescues.id')
                                   ->whereNull('rs.rescue_id')
                                   ->orWhere('rs.status', '<>', 'resolved')
                                   ->count();
        return view('livewire.dashboard-admin-livewire');
    }

    public function searchCandidate()
    {
        $this->keyIn = $this->keyword ? 1 : 0;
        $model       = Candidate::search($this->keyword ?: null)
                                ->paginate(10);

        $this->results = $model->load('agency')->toArray();
    }

    public function bindSearch($id, $keyword)
    {
        $this->keyIn     = 0;
        $this->keyword   = $keyword;
        $this->candidate = Candidate::query()->find($id)->toArray();
    }

    public function showRecues()
    {
        $this->recues = Rescue::query()
                              ->leftJoin('responds as rs', 'rs.rescue_id', '=', 'rescues.id')
                              ->whereNull('rs.rescue_id')
                              ->orWhere('rs.status', '<>', 'resolved')
                              ->with(['candidate'])
                              ->get()
                              ->toArray();
    }
}
