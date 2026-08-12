@foreach ($categories as $category)
    <tr>
        <td>{{ str_repeat(' – ', $category->depth) }} {{ $category->name }}</td>
        <td>{{ str_repeat(' – ', $category->depth) }} {{ $category->au }}</td>
        <td>@if($category->status) Igen @else Nem @endif</td>
        <td class="text-right">
            @if (auth()->user()->can('update', $category) || auth()->user()->can('delete', $category))
                
                <a href="{{ route('category.edit', $category->id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                
                @if ($category->products->isEmpty() && auth()->user()->can('delete', $category))
                    <form class="d-inline-block" action="{{ route('category.destroy', $category->id) }}" method="POST">
                        @method('delete')
                        @csrf
                        <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a kategóriát?') ? this.parentElement.submit() : ''"><i class="fa fa-times"></i></a>
                    </form>
                @endif
            @endif
        </td>
    </tr>
    @if ($category->children)
        @include('categories.category_children', ['categories' => $category->children])
    @endif
@endforeach