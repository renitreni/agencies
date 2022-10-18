<div class="card">
    <div class="card-body p-3">
        <div class="row">
            <div class="col-8">
                <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $label }}</p>
                    <h5 class="font-weight-bolder mb-0">
                        {{ $total_count }}
                    </h5>
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    {!! $icon !!}
                </div>
            </div>
        </div>
    </div>
</div>