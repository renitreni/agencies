<div class="collapse navbar-collapse w-auto ">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
           aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex flex-column justify-content-center py-1" href="{{ route('dashboard') }}">
            <div class="d-flex justify-content-center">
                @php 
                    $agency = auth()->user()->agency;
                    $logoUrl = $agency ? $agency->logo_url : null;
                @endphp
                @isset($logoUrl)
                    <img src="{{ $logoUrl }}"
                         class="navbar-brand-img" alt="main_logo">
                @endisset
            </div>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                <div
                    class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                    <i class="fas fa-dashboard fs-6"></i>
                </div>
                <span class="nav-link-text ms-1">Dashboard</span>
            </a>
        </li>
        @can('agency')
            {{-- @if(!in_array('applicants', $deprive)) Temporary Disabled
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('applicants')) active @endif"
                       href="{{ route('applicants') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-people-group fs-6"></i>
                        </div>
                        <span class="nav-link-text ms-1">Applicants</span>
                    </a>
                </li>
            @endif --}}
            @if(!in_array('finance.vouchers', $deprive))
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('finance.vouchers')) active @endif"
                       href="{{ route('finance.vouchers') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-receipt fs-6"></i>
                        </div>
                        <span class="nav-link-text ms-1">Vouchers</span>
                    </a>
                </li>
            @endif
            @if(!in_array('deployments', $deprive))
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('deployments')) active @endif"
                       href="{{ route('deployments') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-plane fs-6"></i>
                        </div>
                        <span class="nav-link-text ms-1">Deployments</span>
                    </a>
                </li>
            @endif
        @endcan

        @can('admin')
            @if(!in_array('ofw.monitoring', $deprive))
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('ofw.monitoring')) active @endif"
                       href="{{ route('ofw.monitoring') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-signal fs-6"></i>
                        </div>
                        <span class="nav-link-text ms-1">OFW Details</span>
                    </a>
                </li>
            @endif
            @if(!in_array('agencies', $deprive))
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('agencies')) active @endif"
                       href="{{ route('agencies') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-building fs-5"></i>
                        </div>
                        <span class="nav-link-text ms-1">Agency</span>
                    </a>
                </li>
            @endif
            @if(!in_array('cases', $deprive))
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('cases')) active @endif" href="{{ route('cases') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-envelope fs-5"></i>
                        </div>
                        <span class="nav-link-text ms-1">Cases</span>
                    </a>
                </li>
            @endif
            @if(!in_array('rescues', $deprive))
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('rescues')) active @endif" href="{{ route('rescues') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-hands-helping fs-5"></i>
                        </div>
                        <span class="nav-link-text ms-1">Rescues</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('report')) active @endif" href="{{ route('report') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-lightbulb fs-5"></i>
                    </div>
                    <span class="nav-link-text ms-1">Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('users')) active @endif" href="{{ route('users') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users fs-5"></i>
                    </div>
                    <span class="nav-link-text ms-1">Users</span>
                </a>
            </li>
            @if(!in_array('deprive', $deprive))
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('deprive')) active @endif" href="{{ route('deprive') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-ban fs-5"></i>
                        </div>
                        <span class="nav-link-text ms-1">Deprive</span>
                    </a>
                </li>
            @endif
        @endcan
    </ul>
</div>

{{--<div class="sidenav-footer mx-3 ">--}}
{{--    <div class="card card-background shadow-none card-background-mask-secondary" id="sidenavCard">--}}
{{--        <div class="full-background"--}}
{{--             style="background-image: url('{{ asset('theme/soft-ui/assets/img/curved-images/white-curved.jpg') }}')"></div>--}}
{{--        <div class="card-body text-start p-3 w-100">--}}
{{--            <div--}}
{{--                class="icon icon-shape icon-sm bg-white shadow text-center mb-3 d-flex align-items-center justify-content-center border-radius-md">--}}
{{--                <i class="ni ni-diamond text-dark text-gradient text-lg top-0" aria-hidden="true"--}}
{{--                   id="sidenavCardIcon"></i>--}}
{{--            </div>--}}
{{--            <div class="docs-info">--}}
{{--                <h6 class="text-white up mb-0">Need help?</h6>--}}
{{--                <p class="text-xs font-weight-bold">Please check our docs</p>--}}
{{--                <a href="https://www.creative-tim.com/learning-lab/bootstrap/license/soft-ui-dashboard" target="_blank"--}}
{{--                   class="btn btn-white btn-sm w-100 mb-0">Documentation</a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <a class="btn bg-gradient-primary mt-3 w-100"--}}
{{--       href="https://www.creative-tim.com/product/soft-ui-dashboard-pro?ref=sidebarfree">Upgrade to pro</a>--}}
{{--</div>--}}
