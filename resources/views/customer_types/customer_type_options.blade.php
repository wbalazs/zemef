@foreach ($customer_types as $customer_type)
    <option @if(in_array($customer_type->customer_type_id, $customer_type_ids)) selected @endif value="{{ $customer_type->customer_type_id }}" >{{ str_repeat(' | – ', $customer_type->depth) . $customer_type->name }}</option>
    @if ($customer_type->children->count() > 0)
        @include('customer_types.customer_type_options', ['customer_types' => $customer_type->children, 'customer_type_ids' => $customer_type_ids])
    @endif
@endforeach