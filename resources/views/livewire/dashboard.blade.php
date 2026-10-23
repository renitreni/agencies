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
                
                {{-- FRA Voucher Analytics Chart --}}
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Vouchers per Foreign Recruitment Agency (FRA)</h6>
                            <div class="btn-group btn-group-sm" role="group">
                                @foreach(['30'=>'30 Days','90'=>'90 Days','365'=>'365 Days','all'=>'All Time'] as $value => $label)
                                    <button wire:click="setDateFilter('{{$value}}')" 
                                            class="btn btn-{{$dateFilter===$value?'primary':'outline-secondary'}} btn-sm">
                                        {{$label}}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="fraVoucherChart" width="600" height="250"></canvas>
                        </div>
                    </div>
                </div>
                
                {{-- Status Distribution Donut Chart --}}
                <div class="col-md-6 mt-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6 class="mb-0">Voucher Status Distribution</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" width="300" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                {{-- Monthly Trend Line Chart --}}
                <div class="col-md-6 mt-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6 class="mb-0">Monthly Voucher Trend ({{ now()->year }})</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyChart" width="300" height="200"></canvas>
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
            // ============ FRA Grouped Bar Chart ============
            const fraLabels = @json($fraLabels);
            const fraTotalCounts = @json($fraTotalCounts);
            const fraDeployedCounts = @json($fraDeployedCounts);
            const fraFinancials = @json($fraFinancials);
            
            const ctxFra = document.getElementById('fraVoucherChart').getContext('2d');
            const fraVoucherChart = new Chart(ctxFra, {
                type: 'bar',
                data: {
                    labels: fraLabels,
                    datasets: [
                        { 
                            label: 'Total Vouchers', 
                            data: fraTotalCounts, 
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        { 
                            label: 'Deployed', 
                            data: fraDeployedCounts, 
                            backgroundColor: 'rgba(40, 167, 69, 0.6)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Vouchers by FRA ({{ $filterLabel }})'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const idx = context.dataIndex;
                                    const fin = fraFinancials[idx] || {};
                                    const total = fraTotalCounts[idx];
                                    const deployed = fraDeployedCounts[idx];
                                    const lines = [];
                                    lines.push(`${context.dataset.label}: ${context.raw}`);
                                    if (context.dataset.label === 'Total Vouchers') {
                                        lines.push(`Deployed: ${deployed}`);
                                        lines.push(`Deployment Rate: ${total > 0 ? ((deployed/total)*100).toFixed(1) : 0}%`);
                                        lines.push('--- Financials ---');
                                        lines.push(`Passporting: ₱${fin.passporting?.toLocaleString() || 0}`);
                                        lines.push(`Tickets: ₱${fin.tickets?.toLocaleString() || 0}`);
                                        lines.push(`Special Tickets (KWT/QAT): ₱${fin.special_tickets?.toLocaleString() || 0}`);
                                        lines.push(`Allowances: ₱${fin.allowances?.toLocaleString() || 0}`);
                                        lines.push(`Processing Fees: ₱${fin.processing_fees?.toLocaleString() || 0}`);
                                        lines.push(`Refunds: ₱${fin.refunds?.toLocaleString() || 0}`);
                                        lines.push(`Other Costs: ₱${fin.other_costs?.toLocaleString() || 0}`);
                                        const totalCost = Object.values(fin).reduce((a,b)=>a+b,0);
                                        lines.push(`Total Financial: ₱${totalCost.toLocaleString()}`);
                                    }
                                    return lines;
                                }
                            }
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
            
            // ============ Status Distribution Donut Chart ============
            const statusLabels = @json($statusLabels);
            const statusCounts = @json($statusCounts);
            
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        label: 'Vouchers',
                        data: statusCounts,
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',   // Deployed - green
                            'rgba(255, 193, 7, 0.7)',   // In Process - yellow
                            'rgba(220, 53, 69, 0.7)',   // Back Out - red
                            'rgba(108, 117, 125, 0.7)'  // Other - gray
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(108, 117, 125, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Voucher Status ({{ $filterLabel }})'
                        }
                    }
                }
            });
            
            // ============ Monthly Trend Line Chart ============
            const monthLabels = @json($monthLabels);
            const monthCounts = @json($monthCounts);
            
            const ctxMonth = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(ctxMonth, {
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
                            text: 'Monthly Vouchers ({{ now()->year }})'
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
