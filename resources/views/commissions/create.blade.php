{{-- resources/views/commissions/create.blade.php --}}
@extends('layouts.app', [
    'activePage' => 'commissions',
    'activeButton' => 'laravel',
    'title' => 'Új jutalék kampány',
    'navName' => 'Új kampány'
])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('commissions.index') }}">Jutalék kampányok</a>
                        </li>
                        <li class="breadcrumb-item active">Új kampány</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Új jutalék kampány létrehozása</h4>
                        <p class="card-category">Add meg a kampány adatait és jutalékszabályait</p>
                    </div>
                    
                    <form method="POST" action="{{ route('commissions.store') }}" id="campaignForm">
                        @csrf
                        
                        <div class="card-body">
                            {{-- Alapadatok --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary">Alapadatok</h5>
                                    <hr>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Kampány neve <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Kampány típusa <span class="text-danger">*</span></label>
                                        <select class="form-control select2 @error('campaign_type') is-invalid @enderror" 
                                                name="campaign_type" id="campaign_type" required>
                                            <option value="">Válassz típust</option>
                                            <option value="sales" {{ old('campaign_type') == 'sales' ? 'selected' : '' }}>
                                                Eladás utáni jutalék
                                            </option>
                                            <option value="gold_purchase" {{ old('campaign_type') == 'gold_purchase' ? 'selected' : '' }}>
                                                Törtarany felvásárlás
                                            </option>
                                            <option value="expired_ticket" {{ old('campaign_type') == 'expired_ticket' ? 'selected' : '' }}>
                                                Lejárt jegy
                                            </option>
                                            <option value="pawn" {{ old('campaign_type') == 'pawn' ? 'selected' : '' }}>
                                                Zálog
                                            </option>
                                            <option value="other" {{ old('campaign_type') == 'other' ? 'selected' : '' }}>
                                                Egyéb
                                            </option>
                                        </select>
                                        @error('campaign_type')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kezdő dátum <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker @error('start_date') is-invalid @enderror" 
                                               name="start_date" value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vég dátum <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker @error('end_date') is-invalid @enderror" 
                                               name="end_date" value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Érintett boltok <span class="text-danger">*</span></label>
                                        <select class="form-control select2 @error('businesses') is-invalid @enderror" 
                                                name="businesses[]" multiple required>
                                            @foreach($businesses as $business)
                                                <option value="{{ $business->business_id }}" 
                                                    {{ in_array($business->business_id, old('businesses', [])) ? 'selected' : '' }}>
                                                    {{ $business->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            Több bolt is kiválasztható
                                        </small>
                                        @error('businesses')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Jutalékszabályok --}}
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5 class="text-primary">Jutalékszabályok</h5>
                                    <hr>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Itt adhatod meg a jutalékszámítás szabályait. Több szabály is megadható.
                                    </div>
                                    
                                    <div id="rules-container">
                                        {{-- Itt jelennek meg a szabályok --}}
                                    </div>
                                    
                                    <button type="button" class="btn btn-success btn-sm" onclick="addRule()">
                                        <i class="fas fa-plus"></i> Új szabály hozzáadása
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer text-right">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                Mégse
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Kampány létrehozása
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let ruleCount = 0;
    
    $(document).ready(function() {
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
            width: '100%',
            placeholder: 'Válassz...'
        });
        
        // Első szabály hozzáadása
        addRule();
    });
    
    function addRule() {
        const template = `
            <div class="card rule-card mb-3" id="rule-${ruleCount}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeRule(${ruleCount})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Szabály típusa</label>
                                <select class="form-control" name="rules[${ruleCount}][rule_type]" onchange="handleRuleTypeChange(${ruleCount})">
                                    <option value="basic">Alap jutalék</option>
                                    <option value="target">Cél jutalék</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jutalék típusa</label>
                                <select class="form-control" name="rules[${ruleCount}][commission_type]">
                                    <option value="percentage">Százalék</option>
                                    <option value="fixed_amount">Fix összeg</option>
                                    <option value="per_gram">Grammonként</option>
                                    <option value="difference_percentage">Különbözet %-a</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jutalék értéke</label>
                                <input type="number" step="0.01" class="form-control" 
                                       name="rules[${ruleCount}][commission_value]" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row threshold-row" id="threshold-${ruleCount}">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Minimum küszöb</label>
                                <input type="number" step="1000" class="form-control" 
                                       name="rules[${ruleCount}][threshold_min]">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Maximum küszöb</label>
                                <input type="number" step="1000" class="form-control" 
                                       name="rules[${ruleCount}][threshold_max]">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Időszak típusa</label>
                                <select class="form-control" name="rules[${ruleCount}][period_type]">
                                    <option value="monthly">Havi</option>
                                    <option value="weekly">Heti</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4 period-week" id="period-week-${ruleCount}" style="display: none;">
                            <div class="form-group">
                                <label>A hónap hete</label>
                                <select class="form-control" name="rules[${ruleCount}][period_week]">
                                    <option value="1">1. hét</option>
                                    <option value="2">2. hét</option>
                                    <option value="3">3. hét</option>
                                    <option value="4">4. hét</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#rules-container').append(template);
        
        // Period type change handler
        $(`select[name="rules[${ruleCount}][period_type]"]`).on('change', function() {
            if ($(this).val() === 'weekly') {
                $(`#period-week-${ruleCount}`).show();
            } else {
                $(`#period-week-${ruleCount}`).hide();
            }
        });
        
        ruleCount++;
    }
    
    function handleRuleTypeChange(index) {
        var type = $(`select[name="rules[${index}][rule_type]"]`).val();
        
        if (type === 'target') {
            $(`#threshold-${index}`).show();
        } else {
            $(`#threshold-${index}`).hide();
        }
    }
    
    function removeRule(index) {
        $(`#rule-${index}`).remove();
    }
    
    // Form validation
    $('#campaignForm').on('submit', function(e) {
        var rulesCount = $('.rule-card').length;
        
        if (rulesCount === 0) {
            e.preventDefault();
            swal('Hiba!', 'Legalább egy jutalékszabályt meg kell adni!', 'error');
            return false;
        }
    });
</script>
@endpush