<div>
    @push('breadcrumbs')
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Dashboard</h6>
    @endpush
    <div class="row">
            @can('agency')
                <div class="col-md-12">
                    <div class="row justify-content-center">
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            @include('livewire.partials.card', ['label' => 'Vouchers', 'total_count' => $totalVoucher, 'icon' => '<i class="fas fa-stream"></i>'])
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            @include('livewire.partials.card', ['label' => 'Deployed', 'total_count' => $totalDeployed, 'icon' => '<i class="fas fa-plane"></i>'])
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            @include('livewire.partials.card', ['label' => 'Applicants', 'total_count' => $totalCandidate, 'icon' => '<i class="fas fa-portrait"></i>'])
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            @include('livewire.partials.card', ['label' => 'Deployment Rate', 'total_count' => $totalVoucher > 0 ? round($totalDeployed/$totalVoucher*100,1).'%' : '0%', 'icon' => '<i class="fas fa-chart-line"></i>'])
                        </div>
                    </div>
                </div>
                
                {{-- Monthly Voucher Trend --}}
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Monthly Voucher Trend</h6>
                            <select wire:model="selectedYear" class="form-select form-select-sm" style="width: auto;">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="card-body" style="position: relative; height: 300px;">
                            <div style="width: 100%; height: 100%; position: relative;">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- FRA Performance Table --}}
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6 class="mb-0">FRA Performance Summary</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>FRA</th>
                                            <th class="text-center">Total Vouchers</th>
                                            <th class="text-center">Deployed</th>
                                            <th class="text-center">Rate %</th>
                                            <th class="text-center">Total Cost</th>
                                            <th class="text-center">Avg Cost/Voucher</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($fraDetails as $fra)
                                        <tr>
                                            <td>{{ $fra['name'] }}</td>
                                            <td class="text-center">{{ $fra['total'] }}</td>
                                            <td class="text-center">{{ $fra['deployed'] }}</td>
                                            <td class="text-center">{{ $fra['rate'] }}%</td>
                                            <td class="text-center">₱{{ number_format($fra['total_cost'], 0) }}</td>
                                            <td class="text-center">₱{{ number_format($fra['avg_cost'], 0) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <livewire:dashboard-admin-livewire/>
            @endcan
        </div>
    
    @push('scripts')
        <script>
            // ============ Monthly Trend Line Chart ============
            const monthLabels = @json($monthLabels);
            const monthCounts = @json($monthCounts);
            
            const ctxMonth = document.getElementById('monthlyChart').getContext('2d');
            
            // Destroy existing chart instance if it exists
            if (window.monthlyChart instanceof Chart) {
                window.monthlyChart.destroy();
            }
            
            window.monthlyChart = new Chart(ctxMonth, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Vouchers Created',
                        data: monthCounts,
                        fill: false,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.3,
                        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Monthly Vouchers ({{ $selectedYear }})'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
</div>
