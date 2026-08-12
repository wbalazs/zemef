@php
    $rolePermissions = (array) ($rolePermissions ?? []);
@endphp

<ul class="nav nav-tabs nav-fill" id="{{ $id }}" role="tablist">
    @foreach($tabs as $title => $tab)
        <li class="nav-item" role="presentation">
            <a @class(['nav-link', 'active' => $loop->first])
               data-toggle="tab"
               data-target="#{{ $id }}-{{ $loop->index }}"
               type="button"
               role="tab"
               aria-controls="{{ $id }}-{{ $loop->index }}"
               aria-selected="@if($loop->first) true @else false @endif">
                {{ trans("permissions.tab_name.{$title}") ?: $title }}
            </a>
        </li>
    @endforeach
</ul>

<div class="tab-content js-role-permissions-checks" id="{{ $id }}-content">
    @foreach($tabs as $title => $tab)
        <div @class(['tab-pane', 'fade', 'show active' => $loop->first])
             id="{{ $id }}-{{ $loop->index }}"
             role="tabpanel"
             aria-labelledby="{{ $id }}-{{ $loop->index }}"
             tabindex="{{ $loop->index }}">
            @if(is_array(reset($tab)))
                {{-- Nested tabs --}}
                @include('roles.partials.tabs', [
                    'tabs' => $tab,
                    'id' => 'sub-tab-' . $loop->index,
                    'rolePermissions' => $rolePermissions
                ])
            @else
                @include('roles.partials.permissions', [
                    'models' => $tab,
                    'rolePermissions' => $rolePermissions
                ])
            @endif
        </div>
    @endforeach
</div>