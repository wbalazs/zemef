<div class="sidebar" data-color="orange" data-image="{{ asset('img/sidebar-5.jpg') }}">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="http://www.zemef.hu" class="simple-text logo-mini">Z</a>
            <a href="http://www.zemef.hu" class="simple-text logo-normal">Zemef ERP</a>
        </div>
        <div class="user">
            <div class="photo">
                <img src="{{ auth()->user()->profilePicture() }}" />
            </div>
            <div class="info ">
                <a data-toggle="collapse" href="#collapseExample" class="collapsed">
                    <span>{{ auth()->user()->name }} <b class="caret"></b></span>
                </a>
                <div class="collapse" id="collapseExample">
                    <ul class="nav">
                        <li>
                            <a class="profile-dropdown" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <span class="sidebar-mini">K</span>
                                <span class="sidebar-normal">Kilépés</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <ul class="nav">
            {{-- Users section (keeps @can as before) --}}
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#users" @if($activeButton =='users') aria-expanded="true" @endif>
                    <i class="fal fa-users"></i>
                    <p>Felhasználók <b class="caret"></b></p>
                </a>
                <div class="collapse @if($activeButton =='users') show @endif" id="users">
                    <ul class="nav">
                        @can('manage-users', App\User::class)
                            <li class="nav-item @if($activePage == 'role') active @endif">
                                <a class="nav-link" href="{{ route('role.index') }}">
                                    <span class="sidebar-mini">CS</span>
                                    <span class="sidebar-normal">Csoportok</span>
                                </a>
                            </li>
                        @endcan
                        @can('manage-users', App\User::class)
                            <li class="nav-item @if($activePage == 'user') active @endif">
                                <a class="nav-link" href="{{ route('user.index') }}">
                                    <span class="sidebar-mini">F</span>
                                    <span class="sidebar-normal">Felhasználók</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>

            {{-- Employee --}}
            @if(Auth::user()->hasPermission('index', App\Models\Employee::class))
                <li class="nav-item @if($activePage == 'employee') active @endif">
                    <a class="nav-link" href="{{ route('employee.index') }}">
                        <i class="fal fa-id-card"></i>
                        <p>Munkavállalók</p>
                    </a>
                </li>
            @endif

            {{-- Customer --}}
            @if(Auth::user()->hasPermission('index', App\Models\Customer::class))
                <li class="nav-item @if($activePage == 'customer') active @endif">
                    <a class="nav-link" href="{{ route('customer.index') }}">
                        <i class="fal fa-users-class"></i>
                        <p>Ügyfelek</p>
                    </a>
                </li>
            @endif

            {{-- Értékesítés – CashRegister --}}
            @if(Auth::user()->hasPermission('index', App\Models\CashRegister::class))
                <li class="nav-item @if($activePage == 'sale_panel') active @endif">
                    <a class="nav-link" href="{{ route('sale_panel.index') }}">
                        <i class="fal fa-cash-register"></i>
                        <p>Értékesítés</p>
                    </a>
                </li>
            @endif
            {{-- Ékszer vásárlás (StockEntry) --}}
            @if(Auth::user()->hasPermission('index', App\Models\StockEntry::class))
                <li class="nav-item @if($activePage == 'stock_entry') active @endif">
                    <a class="nav-link" href="{{ route('stock_entry.index') }}">
                        <i class="fal fa-gem"></i>
                        <p>Ékszer vásárlás</p>
                    </a>
                </li>
            @endif

            {{-- Kényszerlista (ItemList) --}}
            @if(Auth::user()->hasPermission('index', App\Models\ItemList::class))
                <li class="nav-item @if($activePage == 'item-list') active @endif">
                    <a class="nav-link" href="{{ route('item_list.index') }}">
                        <i class="fal fa-list"></i>
                        <p>Kényszerlista</p>
                    </a>
                </li>
            @endif

            {{-- Bizományosi átvétel (ConsignmentReceipt) --}}
            @if(Auth::user()->hasPermission('index', App\Models\ConsignmentReceipt::class))
                <li class="nav-item @if($activePage == 'consignment_receipt') active @endif">
                    <a class="nav-link" href="{{ route('consignment_receipt.index') }}">
                        <i class="fal fa-money-check-alt"></i>
                        <p>Bizományosi átvétel</p>
                    </a>
                </li>
            @endif

            {{-- Webshop section – sub‑items use WebshopOrder permission --}}
            @if(Auth::user()->hasPermission('index', App\Models\WebshopOrder::class))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#webshop" @if($activeButton =='webshop') aria-expanded="true" @endif>
                        <i class="fal fa-shopping-bag"></i>
                        <p>Webshop <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton =='webshop' || $activeButton =='discount-price') show @endif" id="webshop">
                        <ul class="nav">
                            @if(Auth::user()->hasPermission('index', App\Models\WebshopOrder::class))
                                <li class="nav-item @if($activePage == 'webshop') active @endif">
                                    <a class="nav-link" href="{{ route('webshop.index') }}">
                                        <span class="sidebar-mini">M</span>
                                        <span class="sidebar-normal">Megrendelések</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Kedvezmény – no model yet, visible if webshop permission exists (optional) --}}
                            <li class="nav-item @if($activePage == 'discount-price') active @endif">
                                <a class="nav-link" href="{{ route('discount-price.index') }}">
                                    <span class="sidebar-mini">Ke</span>
                                    <span class="sidebar-normal">Kedvezmény</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif    
            {{-- Csomagküldés – uses Package model --}}
            @if(Auth::user()->hasPermission('index', App\Models\Package::class))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#package" @if($activeButton =='package') aria-expanded="true" @endif>
                        <i class="fal fa-boxes"></i>
                        <p>Csomagküldés <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton =='package' || $activeButton =='cash-on-delivery' || $activePage == 'shipping-service') show @endif" id="package">
                        <ul class="nav">
                            @if(Auth::user()->hasPermission('index', App\Models\Package::class))
                                <li class="nav-item @if($activePage == 'package') active @endif">
                                    <a class="nav-link" href="{{ route('package.index') }}">
                                        <span class="sidebar-mini">Cs</span>
                                        <span class="sidebar-normal">Csomagok</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Utánvétel – no dedicated model, visible if Package permission exists --}}
                            <li class="nav-item @if($activePage == 'cash-on-delivery') active @endif">
                                <a class="nav-link" href="{{ route('cash-on-delivery.index') }}">
                                    <span class="sidebar-mini">UV</span>
                                    <span class="sidebar-normal">Utánvétel</span>
                                </a>
                            </li>
                            <li class="nav-item @if($activePage == 'shipping-service') active @endif">
                                <a class="nav-link" href="{{ route('shipping-service.index') }}">
                                    <span class="sidebar-mini">Sz</span>
                                    <span class="sidebar-normal">Szállítási szolgáltatók</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif  
            {{-- Készlet – these three pages use Inventory (or custom logic) – visible to all for now --}}
            @if(Auth::user()->hasPermission('index', App\Models\Product::class))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#actual_stock" @if($activeButton =='actual_stock') aria-expanded="true" @endif>
                        <i class="fal fa-box-full"></i>
                        <p>Készlet <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton =='actual_stock' || $activeButton =='wait_to_accept_list' || $activeButton =='repair') show @endif" id="actual_stock">
                        <ul class="nav">
                            <li class="nav-item @if($activePage == 'actual_stock') active @endif">
                                <a class="nav-link" href="/actual_stock">
                                    <span class="sidebar-mini">A</span>
                                    <span class="sidebar-normal">Aktuális készlet</span>
                                </a>
                            </li>
                            <li class="nav-item @if($activePage == 'wait_to_accept_list') active @endif">
                                <a class="nav-link" href="/wait_to_accept_list">
                                    <span class="sidebar-mini">Á</span>
                                    <span class="sidebar-normal">Átvételre váró tételek</span>
                                </a>
                            </li>
                            <li class="nav-item @if($activePage == 'repair') active @endif">
                                <a class="nav-link" href="/repair_list">
                                    <span class="sidebar-mini">J</span>
                                    <span class="sidebar-normal">Javítandó tételek</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif  
            {{-- Leltározás (InventoryControl) --}}
            @if(Auth::user()->hasPermission('index', App\Models\InventoryControl::class))
                <li class="nav-item @if($activePage == 'inventory_control') active @endif">
                    <a class="nav-link" href="{{ route('inventory_control.index') }}">
                        <i class="fal fa-scanner"></i>
                        <p>Leltározás</p>
                    </a>
                </li>
            @endif

            {{-- Szett kezelés (ProductSet) --}}
            @if(Auth::user()->hasPermission('index', App\Models\ProductSet::class))
                <li class="nav-item @if($activePage == 'product_set') active @endif">
                    <a class="nav-link" href="/product_set">
                        <i class="fal fa-layer-group"></i>
                        <p>Szett kezelés</p>
                    </a>
                </li>
            @endif

            {{-- Tárgyi eszközök (Tool) --}}
            @if(Auth::user()->hasPermission('index', App\Models\Tool::class))
                <li class="nav-item @if($activePage == 'tool') active @endif">
                    <a class="nav-link" href="{{ route('tool.index') }}">
                        <i class="fal fa-tools"></i>
                        <p>Tárgyi eszközök</p>
                    </a>
                </li>
            @endif

            {{-- Fotózás (Photography) – note: model not in your list but likely exists; if not, add or remove check --}}
            @if(Auth::user()->hasPermission('index', App\Models\Photography::class))
                <li class="nav-item @if($activePage == 'photography') active @endif">
                    <a class="nav-link" href="{{ route('photography.index') }}">
                        <i class="fal fa-camera-alt"></i>
                        <p>Fotózás</p>
                    </a>
                </li>
            @endif

            {{-- Üzletek (Business) --}}
            @if(Auth::user()->hasPermission('index', App\Models\Business::class))
                <li class="nav-item @if($activePage == 'business') active @endif">
                    <a class="nav-link" href="{{ route('business.index') }}">
                        <i class="fal fa-store"></i>
                        <p>Üzletek</p>
                    </a>
                </li>
            @endif

            {{-- Kiküldetések (Posting) – model not in list, but you have Posting model; add if needed --}}
            @if(Auth::user()->hasPermission('index', App\Models\Posting::class))
                <li class="nav-item @if($activePage == 'posting') active @endif">
                    <a class="nav-link" href="{{ route('posting.index') }}">
                        <i class="fal fa-car-alt"></i>
                        <p>Kiküldetések</p>
                    </a>
                </li>
            @endif

            {{-- Saját cégek (Company) --}}
            @if(Auth::user()->hasPermission('index', App\Models\Company::class))
                <li class="nav-item @if($activePage == 'company') active @endif">
                    <a class="nav-link" href="{{ route('company.index') }}">
                        <i class="fal fa-industry-alt"></i>
                        <p>Saját cégek</p>
                    </a>
                </li>
            @endif

            {{-- Bizonylatok (receipts) – each sub‑item individually checked --}}
            @if(Auth::user()->hasPermission('index', App\Models\Document::class))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#receipt" @if($activeButton =='receipt') aria-expanded="true" @endif>
                        <i class="fal fa-file-invoice"></i>
                        <p>Bizonylatok <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton == 'receipt' || $activeButton == 'all_purchase_ticket' || $activeButton == 'all_warehouse_transfer' || $activeButton == 'all_delivery_note' || $activeButton == 'all_stock_entry' || $activeButton == 'all_consignment_receipt' || Str::startsWith($activeButton, 'billingo-bills') || Str::startsWith($activeButton, 'vat-reports') || $activeButton == 'all_booking') show @endif" id="receipt">
                        <ul class="nav">
                            @if(Auth::user()->hasPermission('index', App\Models\PurchaseTicket::class))
                                <li class="nav-item @if($activePage == 'all_purchase_ticket') active @endif">
                                    <a class="nav-link" href="{{ route('receipt.all_purchase_ticket') }}">
                                        <span class="sidebar-mini">V</span>
                                        <span class="sidebar-normal">Vételi jegyek</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\ConsignmentReceipt::class))
                                <li class="nav-item @if($activePage == 'all_consignment_receipt') active @endif">
                                    <a class="nav-link" href="{{ route('receipt.all_consignment_receipt') }}">
                                        <span class="sidebar-mini">B</span>
                                        <span class="sidebar-normal">Bizományosi átvételek</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\StockEntry::class))
                                <li class="nav-item @if($activePage == 'all_stock_entry') active @endif">
                                    <a class="nav-link" href="{{ route('receipt.all_stock_entry') }}">
                                        <span class="sidebar-mini">É</span>
                                        <span class="sidebar-normal">Ékszer vásárlások</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Booking::class))
                                <li class="nav-item @if($activePage == 'all_booking') active @endif">
                                    <a class="nav-link" href="{{ route('receipt.all_booking') }}">
                                        <span class="sidebar-mini">F</span>
                                        <span class="sidebar-normal">Foglalók</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\WarehouseTransfer::class))
                                <li class="nav-item @if($activePage == 'all_warehouse_transfer') active @endif">
                                    <a class="nav-link" href="{{ route('receipt.all_warehouse_transfer') }}">
                                        <span class="sidebar-mini">R</span>
                                        <span class="sidebar-normal">Raktárközi bizonylatok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\DeliveryNote::class))
                                <li class="nav-item @if($activePage == 'all_delivery_note') active @endif">
                                    <a class="nav-link" href="{{ route('receipt.all_delivery_note') }}">
                                        <span class="sidebar-mini">SZ</span>
                                        <span class="sidebar-normal">Szállítólevelek</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\BillingoBill::class))
                                <li class="nav-item @if($activePage == 'billingo-bills/index') active @endif">
                                    <a class="nav-link" href="{{ route('billingo-bills.index') }}">
                                        <span class="sidebar-mini">B</span>
                                        <span class="sidebar-normal">Billingo számlák</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Áfa kimutatás – no model, visible to all (or could be hidden with a custom permission) --}}
                            <li class="nav-item @if($activePage == 'vat-reports/index') active @endif">
                                <a class="nav-link" href="{{ route('vat-reports.index') }}">
                                    <span class="sidebar-mini">B</span>
                                    <span class="sidebar-normal">Áfa kimutatás</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            {{-- Vételi jegyek (separate menu, uses PurchaseTicket) --}}
            @if(Auth::user()->hasPermission('index', App\Models\PurchaseTicket::class))

                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#purchase_ticket" @if($activeButton =='purchase_ticket') aria-expanded="true" @endif>
                        <i class="fal fa-file-invoice-dollar"></i>
                        <p>Vételi jegyek <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton =='purchase_ticket' || $activeButton =='waited_list' || $activeButton =='extension_list' || $activeButton =='own_list') show @endif" id="purchase_ticket">
                        <ul class="nav">
                            @if(Auth::user()->hasPermission('index', App\Models\PurchaseTicket::class))
                                <li class="nav-item @if($activePage == 'purchase_ticket') active @endif">
                                    <a class="nav-link" href="{{ route('purchase_ticket.index') }}">
                                        <span class="sidebar-mini">V</span>
                                        <span class="sidebar-normal">Vételi jegyek</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item @if($activePage == 'waited_list') active @endif">
                                <a class="nav-link" href="/waited_list">
                                    <span class="sidebar-mini">Á</span>
                                    <span class="sidebar-normal">Átadásra váró tételek</span>
                                </a>
                            </li>
                            <li class="nav-item @if($activePage == 'extension_list') active @endif">
                                <a class="nav-link" href="{{ route('purchase_tickets.extension_list') }}">
                                    <span class="sidebar-mini">K</span>
                                    <span class="sidebar-normal">Kiegészítendő vételi jegyek</span>
                                </a>
                            </li>
                            @if(Auth::user()->is_goldsmith)
                                <li class="nav-item @if($activePage == 'own_list') active @endif">
                                    <a class="nav-link" href="/own_list">
                                        <span class="sidebar-mini">N</span>
                                        <span class="sidebar-normal">Nálam lévő tételek</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif        
            {{-- Jutalékok (CommissionCampaign) --}}
            @if(Auth::user()->hasPermission('index', App\Models\CommissionCampaign::class))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#cash-registers" @if($activeButton =='cash-registers' || $activeButton =='commissions' || $activeButton =='denominations') aria-expanded="true" @endif>
                        <i class="fal fa-user-clock"></i>
                        <p>Jutalékok <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton =='commissions*') show @endif" id="cash-registers">
                        <ul class="nav">
                            @if(Auth::user()->hasPermission('index', App\Models\CommissionCampaign::class))
                                <li class="nav-item @if($activePage == 'commissions') active @endif">
                                    <a class="nav-link" href="{{ route('commissions.index') }}">
                                        <span class="sidebar-mini">J</span>
                                        <span class="sidebar-normal">Jutalékok</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif        
            {{-- Házi pénztár (CashRegister, Denomination, CashTransaction) --}}
            @if(Auth::user()->hasPermission('index', App\Models\CashTransaction::class))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#cash-registers" @if($activeButton =='cash-registers') aria-expanded="true" @endif>
                        <i class="fal fa-user-clock"></i>
                        <p>Házi pénztár <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton =='cash-registers' || $activeButton =='denominations' || $activeButton =='cash-transactions') show @endif" id="cash-registers">
                        <ul class="nav">
                            @if(Auth::user()->hasPermission('index', App\Models\CashRegister::class))
                                <li class="nav-item @if($activePage == 'cash-registers') active @endif">
                                    <a class="nav-link" href="{{ route('cash-registers.index') }}">
                                        <span class="sidebar-mini">P</span>
                                        <span class="sidebar-normal">Pénztárak</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Denomination::class))
                                <li class="nav-item @if($activePage == 'denominations') active @endif">
                                    <a class="nav-link" href="{{ route('denominations.index') }}">
                                        <span class="sidebar-mini">C</span>
                                        <span class="sidebar-normal">Címletek</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\CashTransaction::class))
                                <li class="nav-item @if($activePage == 'cash-transactions') active @endif">
                                    <a class="nav-link" href="{{ route('cash-transactions.index') }}">
                                        <span class="sidebar-mini">PT</span>
                                        <span class="sidebar-normal">Pénztár tranzakciók</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif     
            {{-- Szabadságok (Holiday, EmployeeHoliday) --}}
                @if(Auth::user()->hasPermission('index', App\Models\Holiday::class))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#holiday" @if($activeButton =='holiday') aria-expanded="true" @endif>
                        <i class="fal fa-user-clock"></i>
                        <p>Szabadságok <b class="caret"></b></p>
                    </a>
                    <div class="collapse @if($activeButton =='holiday' || $activeButton =='wait_approve') show @endif" id="holiday">
                        <ul class="nav">
                            @if(Auth::user()->hasPermission('index', App\Models\Holiday::class) || Auth::user()->hasPermission('index', App\Models\EmployeeHoliday::class))
                                <li class="nav-item @if($activePage == 'holiday') active @endif">
                                    <a class="nav-link" href="{{ route('holiday.index') }}">
                                        <span class="sidebar-mini">SZ</span>
                                        <span class="sidebar-normal">Szabadságaim</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\EmployeeHoliday::class))
                                <li class="nav-item @if($activePage == 'wait_approve') active @endif">
                                    <a class="nav-link" href="{{ route('holiday.wait_approve') }}">
                                        <span class="sidebar-mini">J</span>
                                        <span class="sidebar-normal">Jóváhagyásra várók</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            {{-- Termékek (Product) --}}
            @if(Auth::user()->hasPermission('index', App\Models\Product::class))
                <li class="nav-item @if($activePage == 'product') active @endif">
                    <a class="nav-link" href="{{ route('product.index') }}">
                        <i class="fal fa-rings-wedding"></i>
                        <p>Termékek</p>
                    </a>
                </li>
            @endif

            {{-- Törzsadatok – each master item individually checked; @can('manage-masters') remains for some --}}
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#masters" @if($activeButton =='masters') aria-expanded="true" @endif>
                    <i class="fal fa-database"></i>
                    <p>Törzsadatok <b class="caret"></b></p>
                </a>
                <div class="collapse @if($activeButton =='bank' || $activeButton =='tool_type' || $activeButton =='currency' || $activeButton =='payment' || $activeButton =='product_status' || $activeButton =='product_sign' || $activeButton =='activity' || $activeButton =='category' || $activeButton =='category2' || $activeButton =='calendar' || $activeButton =='bank_account' || $activeButton =='karat' || $activeButton =='warehouse' || $activeButton =='settlements' || $activeButton =='vehicle' || $activeButton =='unit_price_type' || $activeButton =='vat_type' || $activeButton =='hallmark' || $activeButton =='booking' || $activeButton =='package-size' || $activeButton =='doc_type') show @endif" id="masters">
                    <ul class="nav">
                        {{-- All items under @can('manage-masters') – you can replace @can with hasPermission if desired, but keep as is for now --}}
                        
                            @if(Auth::user()->hasPermission('index', App\Models\VatType::class))
                                <li class="nav-item @if($activePage == 'vat_type') active @endif">
                                    <a class="nav-link" href="/master/vat_types">
                                        <span class="sidebar-mini">Á</span>
                                        <span class="sidebar-normal">Áfa típusok</span>
                                    </a>
                                </li>
                            @endif
                            
                            @if(Auth::user()->hasPermission('index', App\Models\Bank::class))
                                <li class="nav-item @if($activePage == 'bank') active @endif">
                                    <a class="nav-link" href="/master/banks">
                                        <span class="sidebar-mini">B</span>
                                        <span class="sidebar-normal">Bankok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\BankAccount::class))
                                <li class="nav-item @if($activePage == 'bank_account') active @endif">
                                    <a class="nav-link" href="/master/bank_accounts">
                                        <span class="sidebar-mini">B</span>
                                        <span class="sidebar-normal">Bankszámlák</span>
                                    </a>
                                </li>
                            @endif
                            {{-- PackageSize – model not in your list but likely exists --}}
                            @if(Auth::user()->hasPermission('index', App\Models\Package\PackageSize::class))
                                <li class="nav-item @if($activePage == 'package-size') active @endif">
                                    <a class="nav-link" href="/package-size/index">
                                        <span class="sidebar-mini">CSM</span>
                                        <span class="sidebar-normal">Csomag Méretek</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Currency::class))
                                <li class="nav-item @if($activePage == 'currency') active @endif">
                                    <a class="nav-link" href="/master/currencies">
                                        <span class="sidebar-mini">D</span>
                                        <span class="sidebar-normal">Devizák</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\DocType::class))
                                <li class="nav-item @if($activePage == 'doc_type') active @endif">
                                    <a class="nav-link" href="/master/doc_types">
                                        <span class="sidebar-mini">D</span>
                                        <span class="sidebar-normal">Dokumentum típusok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\UnitPriceType::class))
                                <li class="nav-item @if($activePage == 'unit_price_type') active @endif">
                                    <a class="nav-link" href="/master/unit_price_types">
                                        <span class="sidebar-mini">E</span>
                                        <span class="sidebar-normal">Egységár típusok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\ToolType::class))
                                <li class="nav-item @if($activePage == 'tool_type') active @endif">
                                    <a class="nav-link" href="/master/tool_types">
                                        <span class="sidebar-mini">E</span>
                                        <span class="sidebar-normal">Eszköz típusok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Hallmark::class))
                                <li class="nav-item @if($activePage == 'hallmark') active @endif">
                                    <a class="nav-link" href="/master/hallmarks">
                                        <span class="sidebar-mini">F</span>
                                        <span class="sidebar-normal">Fémjelzések</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Payment::class))
                                <li class="nav-item @if($activePage == 'payment') active @endif">
                                    <a class="nav-link" href="/master/payments">
                                        <span class="sidebar-mini">F</span>
                                        <span class="sidebar-normal">Fizetési módok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Booking::class))
                                <li class="nav-item @if($activePage == 'booking') active @endif">
                                    <a class="nav-link" href="/master/bookings">
                                        <span class="sidebar-mini">F</span>
                                        <span class="sidebar-normal">Foglalási szabályok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Vehicle::class))
                                <li class="nav-item @if($activePage == 'vehicle') active @endif">
                                    <a class="nav-link" href="{{ route('vehicle.index') }}">
                                        <span class="sidebar-mini">G</span>
                                        <span class="sidebar-normal">Gépkocsik / járművek</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Attribute::class))
                                <li class="nav-item @if($activePage == 'attribute') active @endif">
                                    <a class="nav-link" href="/master/attributes">
                                        <span class="sidebar-mini">F</span>
                                        <span class="sidebar-normal">Ingatlan tulajdonságai</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Karat::class))
                                <li class="nav-item @if($activePage == 'karat') active @endif">
                                    <a class="nav-link" href="/master/karats">
                                        <span class="sidebar-mini">K</span>
                                        <span class="sidebar-normal">Karátok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Category2::class))
                                <li class="nav-item @if($activePage == 'category2') active @endif">
                                    <a class="nav-link" href="/master/category2s">
                                        <span class="sidebar-mini">K2</span>
                                        <span class="sidebar-normal">Kategória 2.</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Category::class))
                                <li class="nav-item @if($activePage == 'category') active @endif">
                                    <a class="nav-link" href="{{ route('category.index') }}">
                                        <span class="sidebar-mini">K</span>
                                        <span class="sidebar-normal">Kategóriák</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Calendar::class))
                                <li class="nav-item @if($activePage == 'calendar') active @endif">
                                    <a class="nav-link" href="/master/calendar">
                                        <span class="sidebar-mini">N</span>
                                        <span class="sidebar-normal">Naptár</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\NAVStandard::class))
                                <li class="nav-item @if($activePage == 'nav_standard') active @endif">
                                    <a class="nav-link" href="/master/nav_standard">
                                        <span class="sidebar-mini">N</span>
                                        <span class="sidebar-normal">NAV gépjármű norma</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\NAVFuel::class))
                                <li class="nav-item @if($activePage == 'nav_fuel') active @endif">
                                    <a class="nav-link" href="/master/nav_fuels">
                                        <span class="sidebar-mini">N</span>
                                        <span class="sidebar-normal">NAV üzemanyag költség</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Warehouse::class))
                                <li class="nav-item @if($activePage == 'warehouse') active @endif">
                                    <a class="nav-link" href="{{ route('warehouse.index') }}">
                                        <span class="sidebar-mini">R</span>
                                        <span class="sidebar-normal">Raktárak</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Activity::class))
                                <li class="nav-item @if($activePage == 'activities') active @endif">
                                    <a class="nav-link" href="/master/activities">
                                        <span class="sidebar-mini">T</span>
                                        <span class="sidebar-normal">Tevékenységek</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\Address\Settlement::class) || Auth::user()->hasPermission('index', App\Models\Address\City::class))
                                <li class="nav-item @if($activePage == 'settlements') active @endif">
                                    <a class="nav-link" href="/master/settlements">
                                        <span class="sidebar-mini">T</span>
                                        <span class="sidebar-normal">Települések</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\ProductStatus::class))
                                <li class="nav-item @if($activePage == 'product_status') active @endif">
                                    <a class="nav-link" href="/master/product_statuses">
                                        <span class="sidebar-mini">T</span>
                                        <span class="sidebar-normal">Termék állapotok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\ProductType::class))
                                <li class="nav-item @if($activePage == 'product_type') active @endif">
                                    <a class="nav-link" href="/master/product_types">
                                        <span class="sidebar-mini">T</span>
                                        <span class="sidebar-normal">Termék típusok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\ProductSign::class))
                                <li class="nav-item @if($activePage == 'product_sign') active @endif">
                                    <a class="nav-link" href="/master/product_signs">
                                        <span class="sidebar-mini">T</span>
                                        <span class="sidebar-normal">Termék jelzések</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\ProductColor::class))
                                <li class="nav-item @if($activePage == 'product_color') active @endif">
                                    <a class="nav-link" href="/master/product_colors">
                                        <span class="sidebar-mini">T</span>
                                        <span class="sidebar-normal">Termék szín típusok</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\CustomerCard::class))
                                <li class="nav-item @if($activePage == 'customercard') active @endif">
                                    <a class="nav-link" href="{{ route('customercard.index') }}">
                                        <span class="sidebar-mini">ÜK</span>
                                        <span class="sidebar-normal">Ügyfél kártya</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->hasPermission('index', App\Models\CustomerType::class))
                                <li class="nav-item @if($activePage == 'customer_type') active @endif">
                                    <a class="nav-link" href="{{ route('customer_type.index') }}">
                                        <span class="sidebar-mini">Ü</span>
                                        <span class="sidebar-normal">Ügyfél típusok</span>
                                    </a>
                                </li>
                            @endif
                        
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>