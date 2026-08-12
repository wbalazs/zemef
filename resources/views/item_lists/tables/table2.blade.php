<table id="datatables" data-name="items_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Picture') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Tags') }}</th> 
            <th>{{ __('Creation Data') }}</th>
            <th class="disabled-sorting text-right">{{ __('Actions') }}</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td> 
                    @if($item->picture)
                        <img src="{{ $item->path() }}" style="max-width:130px;">
                    @endif
                </td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->tags->implode('name',', ') }} </td>
                <td>{{ $item->created_at }}</td>
                <td class="text-right">
                    @if (auth()->user()->can('update', $item)
                        <a href="{{ route('item.edit', $item->id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                    @endif
                    @if (auth()->user()->can('delete', $item))        
                        <form class="d-inline-block" action="{{ route('item.destroy', $item->id) }}" method="POST">
                            @method('delete')
                            @csrf
                            <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this item?') }}') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                        </form>
                    @endif
                </td>
                
            </tr>
        @endforeach
    </tbody>
</table>