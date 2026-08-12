@php
    $permissionsGrouped = (array) ($permissionsGrouped ?? []);
    $rolePermissions = (array) ($rolePermissions ?? []);
    $id = 'role-permissions-tabs';
@endphp

<ul class="nav nav-tabs nav-fill" id="{{ $id }}" role="tablist">
    @foreach($permissionsGrouped as $modelClass => $permissions)
        @php
            $policyTranslateName = \Illuminate\Support\Str::snake(
                \Illuminate\Support\Str::replace(['App\Models\\', '\\'], '', $modelClass)
            );
            $tabTitle = $policyTranslateName;
        @endphp
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $loop->first ? 'active' : '' }}"
               data-toggle="tab"
               href="#{{ $id }}-{{ $loop->index }}"
               role="tab"
               aria-controls="{{ $id }}-{{ $loop->index }}"
               aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                {{ trans("permissions.policy_name.{$tabTitle}") ?: $tabTitle }}
            </a>
        </li>
    @endforeach
    
</ul>

<div class="tab-content">
    @foreach($permissionsGrouped as $modelClass => $permissions)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
             id="{{ $id }}-{{ $loop->index }}"
             role="tabpanel">
            @include('roles.partials.permissions', [
                'models' => [$modelClass => $permissions],
                'rolePermissions' => $rolePermissions,
                'role' => $role   // <-- add this
            ])
        </div>
    @endforeach
</div>