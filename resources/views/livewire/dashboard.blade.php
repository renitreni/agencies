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
                    </div>
                </div>
                
                {{-- FRA Voucher Analytics Chart --}}
                <div class="col-md-12 mt-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6 class="mb-0">Vouchers per Foreign Recruitment Agency (FRA)</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="fraVoucherChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            @else
                <livewire:dashboard-admin-livewire/>
            @endcan
        </div>
    
    @push('scripts')
        <script>
            const fraLabels = @json($fraLabels);
            const fraCounts = @json($fraCounts);
            
            const ctx = document.getElementById('fraVoucherChart').getContext('2d');
            const fraVoucherChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: fraLabels,
                    datasets: [{
                        label: 'Voucher Count',
                        data: fraCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Vouchers by FRA'
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
