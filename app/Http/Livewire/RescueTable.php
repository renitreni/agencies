<?php

namespace App\Http\Livewire;

use App\Models\Rescue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RescueTable extends DataTableComponent
{

    public function columns(): array
    {
        return [
            Column::make('Created At', 'created_at')
                  ->format(fn($value) => Carbon::parse($value)->format('F j, Y'))
                  ->sortable(),
            Column::make('IP Address', 'ip_address')
                  ->sortable(),
            Column::make('OFW name', 'last_name')
                  ->format(fn($value, $row, $data) => $data['last_name'].', '.$data['first_name'])
                  ->sortable(),
            Column::make('Locate', 'id')
                  ->format(function ($value, $row, $data) {
                      $route = route('map', [
                          'latitude' => $data['actual_latitude'],
                          'longitude' => $data['actual_longitude'],
                      ]);

                      return "<a href='$route' target='_blank' class='btn btn-link m-0'>Locate</a>";
                  })
                  ->asHtml()
                  ->sortable(),
            Column::make('Feedback', 'id')
                  ->format(function ($value, $row, $data) {
                      if (! $data['feedback']) {
                          return '<button type="button" class="btn btn-sm btn-info my-auto">Add Feedback</button>';
                      }
                      return '<button type="button" class="btn btn-sm btn-link my-auto">View</button>';
                  })
                  ->asHtml()
                  ->sortable(),
        ];
    }

    public function query(): Builder
    {
        return Rescue::query()
                     ->selectRaw('c.last_name, c.first_name, rescues.*, res.feedback')
                     ->join('candidates as c', 'c.id', '=', 'rescues.candidate_id')
                     ->leftJoin('responds as res', 'res.rescue_id', '=', 'rescues.id');
    }
}