@php
    $rolePermissions = (array) ($rolePermissions ?? []);
    $models = (array) ($models ?? []);
@endphp

@foreach($models as $modelClass => $permissionList)
    @php
        if (!is_iterable($permissionList)) continue;
        $policyTranslateName = \Illuminate\Support\Str::snake(
            \Illuminate\Support\Str::replace(['App\Models\\', '\\'], '', $modelClass)
        );
    @endphp
    <div class="js-row">
        <label class="h4 pt-3">{{ trans("permissions.policy_name.{$policyTranslateName}") }}</label>
        <div class="float-right pt-3">
            <input type="checkbox" class="js-check-row" />
        </div>
        <div class="row js-permission-row pl-4">
            @foreach($permissionList as $permission)
                @php
                    if (!is_array($permission) && !is_object($permission)) continue;
                    $ability = is_array($permission) ? $permission['ability'] : $permission->ability;
                    $permId = is_array($permission) ? $permission['id'] : $permission->id;
                    $abilityTranslateName = \Illuminate\Support\Str::snake($ability);
                    $abilityClass = \Illuminate\Support\Str::slug($abilityTranslateName);
                    $isActive = in_array($permId, $rolePermissions);
                @endphp
                <div class="col-permission">
                    <button type="button"
                            class="btn permission-toggle-btn {{ $isActive ? 'btn-success' : 'btn-danger' }}"
                            data-role-id="{{ $role->id }}"
                            data-permission-id="{{ $permId }}"
                            data-active="{{ $isActive ? '1' : '0' }}">
                        @switch($abilityTranslateName)
                            @case('viewany') <i class="fas fa-eye"></i> @break
                            @case('access') <i class="fas fa-eye"></i> @break
                            @case('create') <i class="fas fa-plus"></i> @break
                            @case('update') <i class="fas fa-pen"></i> @break
                            @case('delete') <i class="fas fa-ban"></i> @break
                            @case('restore') <i class="fas fa-undo-alt"></i> @break
                            @default <i class="fas fa-lock"></i>
                        @endswitch
                        {{ trans("permissions.ability_name.{$abilityTranslateName}") ?: $abilityTranslateName }}
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endforeach