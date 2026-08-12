@foreach ($categories as $category)
    <option @if($category->id == $category_id) selected @endif value="{{ $category->id }}" >{{ str_repeat(' | – ', $category->depth) . $category->name }}</option>
    @if ($category->children)
        @include('categories.category_options', ['categories' => $category->children, 'category_id' => $category_id])
    @endif
@endforeach