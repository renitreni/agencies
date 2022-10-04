<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Voucher;
use Gridjs\VoucherGridjs;
use Livewire\Component;

class Vouchers extends Component
{
    public array $params = [];

    public array $accounts = [];

    public array $details = [];

    protected $listeners = ['editVoucher' => 'edit'];

    public function mount()
    {
        $this->params['account'] = auth()->id();
        $this->accounts = User::query()
                              ->select(['id', 'email'])
                              ->where('agency_id', auth()->user()->agency_id)
                              ->get()
                              ->toArray();
    }

    public function render()
    {
        return view('livewire.vouchers');
    }

    public function updatedParams()
    {
        $this->emit('outsideFilter', $this->params);
    }

    public function updatedFiltered()
    {
        $this->emit('voucherFiltered');
    }

    public function edit($id)
    {
        $this->details = Voucher::query()->find($id)->toArray()[0];
    }

    public function store()
    {
        $params = array_merge($this->details, ['created_by' => auth()->id(), 'agency_id' => auth()->user()->agency_id]);
        Voucher::query()->updateOrCreate(['id' => $this->details['id'] ?? null], $params);
        $this->emit('callToaster', [
            'message' => isset($this->details['id']) ? 'Voucher has been updated!' : 'New Voucher has been Added!',
        ]);
        $this->details = [];
    }

    public function destroy()
    {
        Voucher::query()->find($this->details['id'])->delete();
        $this->emit('callToaster', ['message' => 'Voucher has been deleted!']);
        $this->details = [];
    }
}
