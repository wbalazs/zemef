@extends('layouts.app', [
    'activePage' => 'billingo-bills',
    'activeButton' => 'billingo-bills',
    'title' => 'Zemef ERP - Számla részletei',
    'navName' => 'Számla részletei'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Bill Details Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Számla részletei</h3>
                            </div>
                            <div class="col-4 text-right">
                                @can('update', $billingoBill)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-cog"></i> Műveletek
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if(!$billingoBill->paid)
                                                <a class="dropdown-item" href="#" onclick="markAsPaid({{ $billingoBill->id }})">
                                                    <i class="fa fa-check text-success"></i> Megjelölés fizetettnek
                                                </a>
                                            @else
                                                <a class="dropdown-item" href="#" onclick="markAsUnpaid({{ $billingoBill->id }})">
                                                    <i class="fa fa-times text-danger"></i> Megjelölés fizetetlennek
                                                </a>
                                            @endif
                                            
                                            <a class="dropdown-item" href="{{ route('billingo-bills.sync', $billingoBill) }}">
                                                <i class="fa fa-sync text-info"></i> Szinkronizálás Billingóval
                                            </a>
                                            
                                            @if($billingoBill->download_url || $billingoBill->pdf_path)
                                                <a class="dropdown-item" href="{{ asset($billingoBill->pdf_path) }}">
                                                    <i class="fa fa-download text-primary"></i> PDF letöltés
                                                </a>
                                            @endif
                                            
                                            @if($billingoBill->public_url)
                                                <a class="dropdown-item" href="{{ $billingoBill->public_url }}" target="_blank">
                                                    <i class="fa fa-external-link-alt text-warning"></i> Nyilvános link
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Alapadatok</h5>
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="width: 40%;">Számlaszám:</th>
                                            <td>
                                                <strong>{{ $billingoBill->bill_number ?? 'N/A' }}</strong>
                                                @if($billingoBill->isOverdue())
                                                    <span class="badge badge-danger ml-2">LEJÁRT</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Típus:</th>
                                            <td>
                                                @php
                                                    $typeBadges = [
                                                        'outgoing' => ['class' => 'success', 'text' => 'Kimenő'],
                                                        'incoming' => ['class' => 'info', 'text' => 'Bejövő'],
                                                        'draft' => ['class' => 'secondary', 'text' => 'Piszkozat'],
                                                    ];
                                                    $type = $typeBadges[$billingoBill->type] ?? ['class' => 'secondary', 'text' => $billingoBill->type];
                                                @endphp
                                                <span class="badge badge-{{ $type['class'] }}">{{ $type['text'] }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Státusz:</th>
                                            <td>
                                                @php
                                                    $status = $billingoBill->status_badge;
                                                @endphp
                                                <span class="badge badge-{{ $status['color'] }}">
                                                    {{ $status['text'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Teljesítés dátuma:</th>
                                            <td>{{ $billingoBill->fulfillment_date ? $billingoBill->fulfillment_date->format('Y. m. d.') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fizetési határidő:</th>
                                            <td>
                                                {{ $billingoBill->due_date ? $billingoBill->due_date->format('Y. m. d.') : '-' }}
                                                @if($billingoBill->due_date && $billingoBill->due_date->isPast() && !$billingoBill->paid)
                                                    <br><small class="text-danger">{{ $billingoBill->due_date->diffForHumans() }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Fizetési mód:</th>
                                            <td>{{ $billingoBill->payment_method }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pénznem:</th>
                                            <td>{{ $billingoBill->currency }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Összegzés</h5>
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="width: 40%;">Nettó összeg:</th>
                                            <td class="text-right">{{ number_format($billingoBill->net_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</td>
                                        </tr>
                                        <tr>
                                            <th>ÁFA összeg:</th>
                                            <td class="text-right">{{ number_format($billingoBill->gross_total - $billingoBill->net_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</td>
                                        </tr>
                                        <tr class="table-active">
                                            <th><strong>Bruttó összeg:</strong></th>
                                            <td class="text-right"><strong>{{ number_format($billingoBill->gross_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</strong></td>
                                        </tr>
                                        @if($billingoBill->paid)
                                            <tr>
                                                <th>Fizetés dátuma:</th>
                                                <td class="text-right">{{ $billingoBill->paid_date ? $billingoBill->paid_date->format('Y. m. d.') : '-' }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Megjegyzés</h5>
                                <div class="card card-body bg-light">
                                    {{ $billingoBill->comment ?: 'Nincs megjegyzés' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Items Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">Tételek</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Megnevezés</th>
                                        <th class="text-right">Mennyiség</th>
                                        <th class="text-right">Nettó egységár</th>
                                        <th class="text-right">ÁFA kulcs</th>
                                        <th class="text-right">Nettó összeg</th>
                                        <th class="text-right">ÁFA összeg</th>
                                        <th class="text-right">Bruttó összeg</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($billingoBill->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $item->name }}</strong>
                                                @if($item->description)
                                                    <br><small class="text-muted">{{ $item->description }}</small>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($item->quantity, 3, ',', ' ') }} {{ $item->unit }}
                                            </td>
                                            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} {{ $billingoBill->currency }}</td>
                                            <td class="text-right">{{ $item->vat }}</td>
                                            <td class="text-right">{{ number_format($item->net_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</td>
                                            <td class="text-right">{{ number_format($item->vat_amount, 0, ',', ' ') }} {{ $billingoBill->currency }}</td>
                                            <td class="text-right">{{ number_format($item->gross_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-active">
                                        <th colspan="5" class="text-right">Összesítés:</th>
                                        <th class="text-right">{{ number_format($billingoBill->net_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</th>
                                        <th class="text-right">{{ number_format($billingoBill->gross_total - $billingoBill->net_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</th>
                                        <th class="text-right">{{ number_format($billingoBill->gross_total, 0, ',', ' ') }} {{ $billingoBill->currency }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Partner Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Partner adatai</h4>
                    </div>
                    <div class="card-body">
                        <h5>{{ $billingoBill->partner_name }}</h5>
                        
                        <table class="table table-sm">
                            <tbody>
                                @if($billingoBill->partner_tax_number)
                                    <tr>
                                        <th style="width: 40%;">Adószám:</th>
                                        <td>{{ $billingoBill->partner_tax_number }}</td>
                                    </tr>
                                @endif
                                
                                @if($billingoBill->partner_email)
                                    <tr>
                                        <th>E-mail:</th>
                                        <td>{{ $billingoBill->partner_email }}</td>
                                    </tr>
                                @endif
                                
                                @if($billingoBill->partner_phone)
                                    <tr>
                                        <th>Telefon:</th>
                                        <td>{{ $billingoBill->partner_phone }}</td>
                                    </tr>
                                @endif
                                
                                <tr>
                                    <th>Cím:</th>
                                    <td>
                                        {{ $billingoBill->partner_zip_code }} {{ $billingoBill->partner_city }}<br>
                                        {{ $billingoBill->partner_address }}
                                    </td>
                                </tr>
                                
                                @if($billingoBill->partner_is_company)
                                    <tr>
                                        <th>Típus:</th>
                                        <td><span class="badge badge-info">Cég</span></td>
                                    </tr>
                                @else
                                    <tr>
                                        <th>Típus:</th>
                                        <td><span class="badge badge-secondary">Magánszemély</span></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Billingo Information Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">Billingo információk</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 50%;">Billingo ID:</th>
                                    <td>{{ $billingoBill->billingo_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Létrehozva:</th>
                                    <td>{{ $billingoBill->created_at->format('Y. m. d. H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Utolsó módosítás:</th>
                                    <td>{{ $billingoBill->updated_at->format('Y. m. d. H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Elektronikus:</th>
                                    <td>{{ $billingoBill->electronic ? 'Igen' : 'Nem' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            @if($billingoBill->pdf_path)
                                <a href="{{ asset($billingoBill->pdf_path) }}" 
                                   class="btn btn-success btn-sm btn-block" 
                                   target="_blank">
                                    <i class="fa fa-file-pdf"></i> Helyi PDF megtekintése
                                </a>
                            @endif
                            
                            @if($billingoBill->download_url)
                                <a href="{{ route('billingo-bills.download-pdf', $billingoBill) }}" 
                                   class="btn btn-primary btn-sm btn-block mt-2">
                                    <i class="fa fa-download"></i> PDF letöltése Billingóból
                                </a>
                            @endif
                            
                            @if($billingoBill->public_url)
                                <a href="{{ $billingoBill->public_url }}" 
                                   class="btn btn-warning btn-sm btn-block mt-2" 
                                   target="_blank">
                                    <i class="fa fa-external-link-alt"></i> Nyilvános link megnyitása
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0">Gyors műveletek</h4>
                    </div>
                    <div class="card-body">
                        @if(!$billingoBill->paid)
                            <button type="button" class="btn btn-success btn-block" onclick="markAsPaid({{ $billingoBill->id }})">
                                <i class="fa fa-check"></i> Megjelölés fizetettnek
                            </button>
                        @else
                            <button type="button" class="btn btn-danger btn-block" onclick="markAsUnpaid({{ $billingoBill->id }})">
                                <i class="fa fa-times"></i> Megjelölés fizetetlennek
                            </button>
                        @endif
                        
                        <a href="{{ route('billingo-bills.sync', $billingoBill) }}" 
                           class="btn btn-info btn-block mt-2">
                            <i class="fa fa-sync"></i> Szinkronizálás Billingóval
                        </a>
                        
                        <a href="{{ route('billingo-bills.index') }}" 
                           class="btn btn-secondary btn-block mt-2">
                            <i class="fa fa-arrow-left"></i> Vissza a listához
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
    // Quick action functions
    function markAsPaid(billId) {
        if (confirm('Biztosan megjelöli ezt a számlát fizetettként?')) {
            $.ajax({
                url: `/billingo-bills/${billId}/quick-action`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: 'mark_paid'
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Hiba történt a művelet során.');
                }
            });
        }
    }

    function markAsUnpaid(billId) {
        if (confirm('Biztosan megjelöli ezt a számlát fizetetlenként?')) {
            $.ajax({
                url: `/billingo-bills/${billId}/quick-action`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: 'mark_unpaid'
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Hiba történt a művelet során.');
                }
            });
        }
    }
</script>
@endpush

@push('css')
<style>
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .table td.text-right,
    .table th.text-right {
        text-align: right !important;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endpush