<div class="form-group" data-cri-choice="{{ $field }}">
    <div class="form-control-label mb-2">{{ $label }}</div>
    @foreach (['1' => 'Igen', '0' => 'Nem'] as $choiceValue => $choiceLabel)
        <div class="form-check form-check-inline">
            <label class="form-check-label">
                <input type="checkbox" class="form-check-input cri-binary-choice {{ $field }}"
                       data-choice-field="{{ $field }}" value="{{ $choiceValue }}"
                       @if(!empty($yesInputId)) id="{{ $choiceValue == 1 ? $yesInputId : $yesInputId . '_no' }}" @endif
                       @checked($selectedValue !== null && (string) $selectedValue === (string) $choiceValue)
                       @disabled($disabled)>
                <span class="form-check-sign"></span>
                {{ $choiceLabel }}
            </label>
        </div>
    @endforeach
    <div class="invalid-feedback">Válasszon: Igen vagy Nem.</div>
</div>