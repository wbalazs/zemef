@foreach ($product_types as $product_type)
    <option @if((string) $product_type->product_type_id === (string) $product_type_id) selected @endif value="{{ $product_type->product_type_id }}">{{ $product_type->name }}</option>
@endforeach
