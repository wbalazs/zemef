{{-- resources/views/customercard/index.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'customercard', 
    'activeButton' => 'customercard', 
    'title' => 'Zemef ERP', 
    'navName' => 'Ügyfél Kártyák'
])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Ügyfél Kártyák</h3>
                                    <p class="text-sm mb-0">
                                        Kedvezmény kártyák kezelése
                                    </p>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\CustomerCard::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('customercard.create') }}" class="btn btn-sm btn-primary">Új kártya</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="customer_cards_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Név</th>
                                        <th>Kód</th>
                                        <th>Típus</th>
                                        <th>Érték</th>
                                        <th>Kezdő dátum</th>
                                        <th>Vég dátum</th>
                                        <th>Státusz</th>
                                        <th>Üzletek</th>
                                        <th>Ügyfelek</th>
                                        <th class="disabled-sorting text-right">Műveletek</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Név</th>
                                        <th>Kód</th>
                                        <th>Típus</th>
                                        <th>Érték</th>
                                        <th>Kezdő dátum</th>
                                        <th>Vég dátum</th>
                                        <th>Státusz</th>
                                        <th>Üzletek</th>
                                        <th>Ügyfelek</th>
                                        <th class="text-right">Műveletek</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($customerCards as $card)
                                        <tr>
                                            <td>{{ $card->name }}</td>
                                            <td><span class="badge badge-info">{{ $card->code }}</span></td>
                                            <td>
                                                @switch($card->type)
                                                    @case('percent')
                                                        <span class="badge badge-success">Százalék</span>
                                                        @break
                                                    @case('price')
                                                        <span class="badge badge-warning">Fix összeg</span>
                                                        @break
                                                    @case('product_item')
                                                        <span class="badge badge-primary">Termék alapú</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $card->getDiscountTypeLabel() }}</td>
                                            <td>{{ $card->start_date->format('Y.m.d') }}</td>
                                            <td>{{ $card->end_date->format('Y.m.d') }}</td>
                                            <td>
                                                @if($card->is_active)
                                                    @if($card->isCurrentlyActive)
                                                        <span class="badge badge-success">Aktív</span>
                                                    @elseif($card->isUpcoming)
                                                        <span class="badge badge-info">Előjegyzett</span>
                                                    @else
                                                        <span class="badge badge-danger">Lejárt</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Inaktív</span>
                                                @endif
                                            </td>
                                            <td>{{ $card->businesses->count() }}</td>
                                            <td>{{ $card->customers->count() }}</td>
                                            <td class="text-right">
                                                <div class="btn-group" role="group">
                                                    @if(Auth::user()->hasPermission('edit', App\Models\CustomerCard::class))
                                                        <a href="{{ route('customercard.show', $card->id) }}" 
                                                           class="btn btn-link btn-info btn-sm" 
                                                           title="Megtekintés"
                                                           data-toggle="tooltip">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->hasPermission('edit', App\Models\CustomerCard::class))
                                                        <a href="{{ route('customercard.edit', $card->id) }}" 
                                                           class="btn btn-link btn-warning btn-sm" 
                                                           title="Szerkesztés"
                                                           data-toggle="tooltip">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->hasPermission('delete', App\Models\CustomerCard::class))
                                                        <form class="d-inline-block" 
                                                              action="{{ route('customercard.destroy', $card->id) }}" 
                                                              method="POST"
                                                              onsubmit="return confirm('Biztosan törli ezt a kártyát?');">
                                                           @method('DELETE')
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-link btn-danger btn-sm remove" 
                                                                    title="Törlés"
                                                                    data-toggle="tooltip">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

