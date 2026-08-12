<!-- Navbar -->
<nav class="navbar navbar-expand-lg ">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <div class="navbar-minimize">
                <button id="minimizeSidebar" class="btn btn-warning btn-fill btn-round btn-icon d-none d-lg-block">
                    <i class="fa fa-ellipsis-v visible-on-sidebar-regular"></i>
                    <i class="fa fa-navicon visible-on-sidebar-mini"></i>
                </button>
            </div>
            <a class="navbar-brand" href="#pablo"> {{ $navName }} </a>
        </div>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <!-- Cég választó select -->
                    @if(Auth::user()->hasPermission('index', App\Models\Company::class) && Auth::user()?->employee?->companies->count() > 0)
                        <select class="form-control-sm" id="employee_company_select" style="margin-top: 27px; height: calc(1.70rem + 2px);width: 150px;">
                            @foreach(Auth::User()?->employee?->companies as $company)
                                <option value="{{ $company->company_id }}" {{ $company->company_id == Auth::User()->employee->active_company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    <!-- Üzlet választó select -->
                    @if(Auth::user()->hasPermission('index', App\Models\Business::class) && Auth::user()?->employee?->businesses->count() > 0)
                        <select class="form-control-sm" id="employee_business_select" style="margin-top: 27px; height: calc(1.70rem + 2px);width: 150px;">
                            @foreach(Auth::User()?->employee?->businesses as $business)
                                <option value="{{ $business->business_id }}" {{ $business->business_id == Auth::User()->employee->active_business_id ? 'selected' : '' }}>{{ $business->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="navbarcalendar" href="{{ route('holiday.userCalendar') }}">
                        <i class="fas fa-calendar-alt user-calendar" style="font-size:20px !important;cursor:pointer;"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fal fa-list" style="font-size:20px !important;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink" id="logout">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="nc-icon nc-single-02"></i> Profilom
                        </a>
                        <div class="divider"></div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                        <a class="dropdown-item text-danger" id="logout-btn" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nc-icon nc-button-power"></i> Kilépés
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
