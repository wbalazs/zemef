@extends('layouts.app', ['activePage' => 'extension_list', 'activeButton' => 'extension_list', 'title' => 'Zemef ERP', 'navName' => 'Kiegészítendő vételi jegyek'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">
                        <div class="card-header">
                            <h3 class="mb-0">Kiegészítendő vételi jegyek</h3>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4">
                            <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Szállítólevél sorszáma</th>
                                        <th>Szállítólevél dátuma</th>
                                        <th>Vételi jegy sorszáma</th>
                                        <th>Vételi jegy dátuma</th>
                                        <th>Átadó személy</th>
                                        <th>Átadó üzlet</th>
                                        <th>Vételi jegy tételek darab szám</th>
                                        <th>Sum súly (gramm)</th>
                                        <th>Sum összeg</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $row)
                                        <tr>
                                            <td>{{ $row->delivery_note_number ?: '-' }}</td>
                                            <td>{{ $row->delivery_note_date ? date('Y-m-d', strtotime($row->delivery_note_date)) : '-' }}</td>
                                            <td>{{ $row->ticket_number }}</td>
                                            <td>{{ $row->purchase_ticket_date ? date('Y-m-d', strtotime($row->purchase_ticket_date)) : '-' }}</td>
                                            <td>{{ $row->employee_name ?: '-' }}</td>
                                            <td>{{ $row->business_name ?: '-' }}</td>
                                            <td>{{ number_format((float) $row->item_count, 0, ',', ' ') }}</td>
                                            <td>{{ number_format((float) $row->total_weight, 2, ',', ' ') }}</td>
                                            <td>{{ number_format((float) $row->total_amount, 0, ',', ' ') }}</td>
                                            <td>
                                                <a href="{{ route('purchase_tickets.extension_edit', ['purchase_ticket' => $row->purchase_ticket_id]) }}" class="btn btn-success btn-sm">
                                                    <i class="fa fa-edit"></i> Szerkesztés
                                                </a>
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
