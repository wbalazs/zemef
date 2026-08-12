@foreach ($customer_types as $customer_type)
    <tr>
        <td>{{ str_repeat(' – ', $customer_type->depth) }} {{ $customer_type->name }}</td>
        <td>@if($customer_type->status) Igen @else Nem @endif</td>
        @can('manage-masters', App\User::class)
            <td class="text-right">
                @if (auth()->user()->can('update', $customer_type) || auth()->user()->can('delete', $customer_type))
                    <a href="{{ route('customer_type.edit', $customer_type->customer_type_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                    @if ($customer_type->customers->isEmpty() && auth()->user()->can('delete', $customer_type))
                        <form class="d-inline-block" action="{{ route('customer_type.destroy', $customer_type->customer_type_id) }}" method="POST">
                            @method('delete')
                            @csrf
                            <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a típust?') ? this.parentElement.submit() : ''"><i class="fa fa-times"></i></a>
                        </form>
                    @endif
                @endif
            </td>
        @endcan
    </tr>
    @if ($customer_type->children)
        @include('customer_types.customer_type_children', ['customer_types' => $customer_type->children])
    @endif
@endforeach