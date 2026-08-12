@extends('layouts.app', ['activePage' => 'role', 'activeButton' => 'laravel', 'title' => 'Zemef ERP', 'navName' => 'Csoport szerkesztése'])

@section('content')
<div class="content">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">Szerkesztés: {{ $role->name }}</h3>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('role.index') }}" class="btn btn-sm btn-warning">
                            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form method="post" action="{{ route('role.update', $role->id) }}">
                    @csrf
                    @method('patch')

                    {{-- Role name & description fields --}}
                    <fieldset class="mb-4">
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                {{ __('Name') }}
                            </label>
                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name" maxlength="30" value="{{ old('name', $role->name) }}" required autofocus>
                            @include('alerts.feedback', ['field' => 'name'])
                        </div>

                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">
                                {{ __('Description') }}
                            </label>
                            <textarea name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                      rows="3" maxlength="300">{{ old('description', $role->description) }}</textarea>
                            @include('alerts.feedback', ['field' => 'description'])
                        </div>
                        <div class="form-group mt-4">
                        <button type="submit" class="btn btn-warning">{{ __('Save') }}</button>
                    </div>
                    </fieldset>

                    {{-- Permissions tab panel --}}
                    <div class="mt-4">
                        <h4>Jogok</h4>
                        @include('roles.partials.permissions-tabs', [
                            'permissionsGrouped' => $permissionsGrouped,
                            'rolePermissions'    => $rolePermissions
                        ])
                    </div>

                    
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.js-check-row').on('change', function() {
                $(this).closest('.js-row').find('.js-permission-row input[type="checkbox"]')
                    .prop('checked', $(this).prop('checked'));
            });

             $('.permission-toggle-btn').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var roleId = $btn.data('role-id');
                var permissionId = $btn.data('permission-id');
                var isActive = $btn.data('active') == 1;

                $.ajax({
                    url: '/role/' + roleId + '/permission/' + permissionId + '/toggle',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'POST'
                    },
                    success: function(response) {
                        if (response.active) {
                            $btn.removeClass('btn-danger').addClass('btn-success');
                            $btn.data('active', 1);
                        } else {
                            $btn.removeClass('btn-success').addClass('btn-danger');
                            $btn.data('active', 0);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while toggling the permission.');
                    }
                });
            });

            // Optional: "Check all" for each group
            $('.js-check-row').on('change', function() {
                var $group = $(this).closest('.js-row');
                var $buttons = $group.find('.permission-toggle-btn');
                var shouldActivate = $(this).prop('checked');
                // Trigger click on each button to toggle to desired state (optional)
                // Or you can send multiple AJAX calls. Simpler: just trigger click.
                $buttons.each(function() {
                    var $btn = $(this);
                    var isCurrentlyActive = $btn.data('active') == 1;
                    if ((shouldActivate && !isCurrentlyActive) || (!shouldActivate && isCurrentlyActive)) {
                        $btn.trigger('click');
                    }
                });
            });
        });
   </script>
@endpush

@push('css')
    <style>
        .col-permission {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .permission-toggle-btn {
            margin: 5px;
            min-width: 100px;
            border-radius: 20px;
            transition: all 0.2s;
        }
    </style>
@endpush