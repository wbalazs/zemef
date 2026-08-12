{{-- resources/views/commissions/partials/calculate-modal.blade.php --}}
<div class="modal fade" id="calculateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="calculateForm" method="POST" action="{{ route('commissions.calculate') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator"></i> Jutalék számolás
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" name="campaign_id" id="calc_campaign_id" value="{{ $campaign->id ?? '' }}">
                    
                    <div class="form-group">
                        <label>Kezdő dátum <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" name="start_date" 
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}" required>
                        <small class="form-text text-muted">
                            A számolás kezdő dátuma
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Vég dátum <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" name="end_date" 
                               value="{{ now()->format('Y-m-d') }}" required>
                        <small class="form-text text-muted">
                            A számolás vég dátuma
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Bolt (opcionális)</label>
                        <select class="form-control select2" name="business_id">
                            <option value="">Összes bolt</option>
                            @foreach($businesses ?? [] as $business)
                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Ha nem választasz boltot, minden érintett boltra számol
                        </small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Figyelem!</strong> A számolás felülírhatja a már meglévő jutalékokat!
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Mégse
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calculator"></i> Számolás
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        // Datepicker inicializálás a modalban
        $('#calculateModal .datepicker').datetimepicker({
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
        
        // Select2 inicializálás a modalban
        $('#calculateModal .select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('#calculateModal'),
            placeholder: 'Válassz...'
        });
    });
</script>
@endpush