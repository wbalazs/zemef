{{-- resources/views/commissions/index.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'commissions',
    'activeButton' => 'laravel',
    'title' => 'Jutalék kampányok',
    'navName' => 'Jutalék kampányok'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Jutalék kampányok</h4>
                            @if(Auth::user()->hasPermission('create', App\Models\CommissionCampaign::class))
                                <a href="{{ route('commissions.create') }}" class="btn btn-primary btn-round">
                                    <i class="fas fa-plus"></i> Új kampány
                                </a>
                            @endif
                        </div>
                        <p class="card-category">Aktív és befejezett jutalék kampányok</p>
                    </div>
                    
                    <div class="card-body">
                        {{-- Szűrők --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="business_filter">Bolt szűrő</label>
                                    <select class="form-control select2" id="business_filter" name="business_filter">
                                        <option value="">Összes bolt</option>
                                        @foreach($businesses ?? [] as $business)
                                            <option value="{{ $business->id }}">{{ $business->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status_filter">Státusz</label>
                                    <select class="form-control" id="status_filter">
                                        <option value="">Mind</option>
                                        <option value="active">Aktív</option>
                                        <option value="inactive">Inaktív</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type_filter">Típus</label>
                                    <select class="form-control" id="type_filter">
                                        <option value="">Mind</option>
                                        <option value="sales">Eladás</option>
                                        <option value="gold_purchase">Törtarany felvásárlás</option>
                                        <option value="expired_ticket">Lejárt jegy</option>
                                        <option value="pawn">Zálog</option>
                                        <option value="other">Egyéb</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-sm btn-secondary" onclick="clearFilters()">
                                    <i class="fas fa-undo"></i> Szűrők törlése
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="campaigns-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Név</th>
                                        <th>Típus</th>
                                        <th>Időszak</th>
                                        <th>Bolthálózat</th>
                                        <th>Státusz</th>
                                        <th>Szabályok</th>
                                        <th>Műveletek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($campaigns as $campaign)
                                    <tr>
                                        <td>{{ $campaign->id }}</td>
                                        <td>{{ $campaign->name }}</td>
                                        <td>
                                            @switch($campaign->campaign_type)
                                                @case('sales')
                                                    <span class="badge badge-success">Eladás</span>
                                                    @break
                                                @case('gold_purchase')
                                                    <span class="badge badge-warning">Törtarany</span>
                                                    @break
                                                @case('expired_ticket')
                                                    <span class="badge badge-info">Lejárt jegy</span>
                                                    @break
                                                @case('pawn')
                                                    <span class="badge badge-primary">Zálog</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">Egyéb</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ $campaign->start_date->format('Y.m.d') }} - 
                                            {{ $campaign->end_date->format('Y.m.d') }}
                                        </td>
                                        <td>
                                            @foreach($campaign->businesses as $index => $business)
                                                @if($index < 3)
                                                    <span class="badge badge-info">{{ $business->name }}</span>
                                                @endif
                                            @endforeach
                                            @if($campaign->businesses->count() > 3)
                                                <span class="badge badge-info">+{{ $campaign->businesses->count() - 3 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($campaign->status == 'active')
                                                <span class="badge badge-success">Aktív</span>
                                            @else
                                                <span class="badge badge-secondary">Inaktív</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $campaign->commissionRules->count() }} db</span>
                                        </td>
                                        <td>
                                            @if(Auth::user()->hasPermission('edit', App\Models\CommissionCampaign::class))
                                                <a href="{{ route('commissions.show', $campaign) }}" 
                                                class="btn btn-sm btn-info" title="Részletek">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            @if(Auth::user()->hasPermission('edit', App\Models\CommissionCampaign::class))
                                                <a href="#" class="btn btn-sm btn-warning" 
                                                onclick="calculateCommission({{ $campaign->id }})" title="Jutalék számolás">
                                                    <i class="fas fa-calculator"></i>
                                                </a>
                                            @endif
                                            @if(Auth::user()->hasPermission('delete', App\Models\CommissionCampaign::class))
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="deleteCampaign({{ $campaign->id }})" title="Törlés">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $campaigns->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
{{-- Számolás modal --}}
<div class="modal fade" id="calculateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="calculateForm" method="POST" action="{{ route('commissions.calculate') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Jutalék számolás</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="campaign_id" id="calc_campaign_id">
                    
                    <div class="form-group">
                        <label>Kezdő dátum</label>
                        <input type="text" class="form-control datepicker" name="start_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Vég dátum</label>
                        <input type="text" class="form-control datepicker" name="end_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Bolt (opcionális)</label>
                        <select class="form-control select2" name="business_id">
                            <option value="">Összes bolt</option>
                            @foreach($businesses ?? [] as $business)
                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Mégse</button>
                    <button type="submit" class="btn btn-primary">Számolás</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // DataTable inicializálás
        $('#campaigns-table').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "language": {
                "search": "Keresés:",
                "zeroRecords": "Nincs találat",
                "info": "_START_ - _END_ / _TOTAL_ találat",
                "infoEmpty": "Nincs találat",
                "infoFiltered": "(_MAX_ összes rekordból)",
            }
        });
        
        // Datepicker inicializálás
        $('.datepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            useCurrent: false,
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-screenshot',
                clear: 'fa fa-trash',
                close: 'fa fa-remove'
            }
        });
        
        // Select2 inicializálás
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        // Szűrők kezelése
        $('#business_filter, #status_filter, #type_filter').on('change', function() {
            filterTable();
        });
    });
    
    function filterTable() {
        var business = $('#business_filter').val();
        var status = $('#status_filter').val();
        var type = $('#type_filter').val();
        
        var table = $('#campaigns-table').DataTable();
        
        // Custom filtering
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var row = table.row(dataIndex).node();
                var show = true;
                
                // Itt lehet egyedi szűrési logikát implementálni
                // Mivel a DataTable már keresi a teljes szövegben, ezért lehet hogy elég az alap keresés
                
                return show;
            }
        );
        
        table.draw();
        $.fn.dataTable.ext.search.pop();
    }
    
    function clearFilters() {
        $('#business_filter').val('').trigger('change');
        $('#status_filter').val('');
        $('#type_filter').val('');
        $('#campaigns-table').DataTable().search('').draw();
    }
    
    function calculateCommission(campaignId) {
        $('#calc_campaign_id').val(campaignId);
        $('#calculateModal').modal('show');
    }
    
    function deleteCampaign(id) {
    swal({
        title: 'Biztosan törli?',
        text: 'Ez a művelet nem visszavonható!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-secondary',
        confirmButtonText: 'Igen, törlöm',
        cancelButtonText: 'Mégsem'
    }, function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: '/commissions/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            });
            location.reload();
        }
    });
}
</script>
@endpush
@endsection