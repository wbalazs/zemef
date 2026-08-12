@extends('layouts.app', ['activePage' => 'tool', 'activeButton' => 'tool', 'title' => 'Zemef ERP', 'navName' => 'Eszköz szerkesztése' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">

                <div class="card-header ">

                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="base_data-tab" data-toggle="pill" href="#base_data" role="tab" aria-controls="base_data" aria-selected="true">Alap adatok</a>
                        </li>
                        @if($tool->review_needed)
                        <li class="nav-item">
                            <a class="nav-link" id="investigation_data-tab" data-toggle="pill" href="#investigation_data" role="tab" aria-controls="investigation_data" aria-selected="true">Felülvizsgálat</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" id="docs_data-tab" data-toggle="pill" href="#docs_data" role="tab" aria-controls="docs_data" aria-selected="true">Dokumentumok</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="buy_invoice_data-tab" data-toggle="pill" href="#buy_invoice_data" role="tab" aria-controls="buy_invoice_data" aria-selected="true">Vásárlási számla adatok</a>
                        </li>
                    </ul>

                    <div class="row align-items-center">
                        <div class="col-8">
                            
                        </div>
                        <div class="col-4 text-right mt10">
                            <a href="{{ route('tool.index') }}" class="btn btn-sm btn-warning"><i class="fa fa-arrow-left"></i>  Vissza</a>
                        </div>
                    </div>
                </div>


                <div class="card-body ">
                    <form id="tool_form" method="post" action="{{ route('tool.update', $tool->tool_id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade active show" id="base_data" role="tabpanel" aria-labelledby="base_data-tab">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4">Eszköz alap adatai</h6>
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('name') ? ' has-danger' : '' }}">
                                                                Név</label>
                                                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" maxlength="100" value="{{ old('name', $tool->name) }}" placeholder="" required autofocus>
                                                            @include('alerts.feedback', ['field' => 'name'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-5">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('from') ? ' has-danger' : '' }}">
                                                                Partner
                                                            </label>
                                                            <select name="from" class="select2 form-control{{ $errors->has('from') ? ' is-invalid' : '' }}" required>
                                                                <option value="">- válasszon -</option>
                                                                @foreach($maintainers as $maintainer)
                                                                    <option @if($tool->from == $maintainer->customer_id) selected @endif value="{{ $maintainer->customer_id }}">
                                                                        {{ $maintainer->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @include('alerts.feedback', ['field' => 'from'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-2" style="align-self: end;text-align: left;">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <a href="{{ route('customer.create') }}" class="btn btn-success mb-0"><i class="fa fa-plus"></i> Új partner</a>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                

                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('identity_number') ? ' has-danger' : '' }}">
                                                                Azonosító szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('identity_number') ? ' is-invalid' : '' }}" name="identity_number" maxlength="100" value="{{ old('identity_number', $tool->identity_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'identity_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('serial_number') ? ' has-danger' : '' }}">
                                                                Gyári szám</label>
                                                            <input type="text" class="form-control{{ $errors->has('serial_number') ? ' is-invalid' : '' }}" name="serial_number" maxlength="100" value="{{ old('serial_number', $tool->serial_number) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'serial_number'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">
                                                                Leírás</label>
                                                            <textarea class="zm_textarea form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description">{{ old('description', $tool->description) }}</textarea>
                                                            @include('alerts.feedback', ['field' => 'description'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('failure_description') ? ' has-danger' : '' }}">
                                                                Hibaleírás</label>
                                                            <textarea class="zm_textarea form-control{{ $errors->has('failure_description') ? ' is-invalid' : '' }}" name="failure_description">{{ old('failure_description', $tool->failure_description) }}</textarea>
                                                            @include('alerts.feedback', ['field' => 'failure_description'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('entry_date') ? ' has-danger' : '' }}">
                                                                Bekerülés dátuma</label>
                                                            <input type="date" class="form-control{{ $errors->has('entry_date') ? ' is-invalid' : '' }}" name="entry_date" maxlength="100" value="{{ old('entry_date', $tool->entry_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'entry_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('withdrawal_date') ? ' has-danger' : '' }}">
                                                                Kikerülés dátuma</label>
                                                            <input type="date" class="form-control{{ $errors->has('withdrawal_date') ? ' is-invalid' : '' }}" name="withdrawal_date" maxlength="100" value="{{ old('withdrawal_date', $tool->withdrawal_date) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'withdrawal_date'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('value') ? ' has-danger' : '' }}">
                                                                Érték</label>
                                                            <input type="number" class="form-control{{ $errors->has('value') ? ' is-invalid' : '' }}" name="value" maxlength="100" value="{{ old('value', $tool->value) }}" placeholder="">
                                                            @include('alerts.feedback', ['field' => 'value'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">

                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                <input @if($tool->review_needed) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="review_needed">
                                                                <span class="form-check-sign"></span>
                                                                Felülvizsgálat kell
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <fieldset>
                                                        <div class="form-group">

                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                <input @if($tool->status) checked @endif class="form-check-input" type="checkbox" value="1" data-gtm-form-interact-field-id="1" name="status">
                                                                <span class="form-check-sign"></span>
                                                                Aktív
                                                                </label>
                                                            </div>

                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="col-lg-6">
                                                    
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4"></h6>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tool_type_id') ? ' has-danger' : '' }}">
                                                                Típus</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="tool_type_id" class="selectpicker form-control{{ $errors->has('tool_type_id') ? ' is-invalid' : '' }}">
                                                            @foreach($tool_types as $tool_type)
                                                                <option @if($tool->tool_type_id == $tool_type->tool_type_id) selected @endif value="{{ $tool_type->tool_type_id }}" >{{ $tool_type->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('tool_type_comment') ? ' has-danger' : '' }}">
                                                                Típus megjegyzés</label>
                                                            <input type="text" class="form-control{{ $errors->has('tool_type_comment') ? ' is-invalid' : '' }}" name="tool_type_comment" value="{{ old('tool_type_comment', $tool->tool_type_comment) }}">
                                                            @include('alerts.feedback', ['field' => 'tool_type_comment'])
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('business_id') ? ' has-danger' : '' }}">
                                                                Üzlet</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="business_id" class="selectpicker form-control{{ $errors->has('business_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($businesses as $business)
                                                                    <option @if($business->business_id == $tool->business_id) selected @endif value="{{ $business->business_id }}" >{{ $business->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('employee_id') ? ' has-danger' : '' }}">
                                                                Munkavállaló</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="employee_id" class="selectpicker form-control{{ $errors->has('employee_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                                @foreach($employees as $employee)
                                                                    <option @if($employee->employee_id == $tool->employee_id) selected @endif value="{{ $employee->employee_id }}" >{{ $employee->lastname . ' ' . $employee->firstname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="card-body ">
                                            <h6 class="heading-small text-muted mb-4"></h6>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <fieldset>
                                                        <div class="form-group">
                                                            <label class="form-control-label{{ $errors->has('maintainer_id') ? ' has-danger' : '' }}">
                                                                Karbantartó cég</label>
                                                            <select title="- válasszon -" data-style="btn-info btn-fill btn-block" data-menu-style="" data-style="" name="maintainer_id" class="selectpicker form-control{{ $errors->has('maintainer_id') ? ' is-invalid' : '' }}">
                                                                <option value="">- válasszon -</option>
                                                            @foreach($maintainers as $maintainer)
                                                                <option @if($tool->maintainer_id == $maintainer->customer_id) selected @endif value="{{ $maintainer->customer_id }}" >{{ $maintainer->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    
                                
                                </div>

                                <button type="button" class="btn btn-success save_tool">Mentés</button>
                            </div>

                            <div class="tab-pane fade" id="investigation_data" role="tabpanel" aria-labelledby="investigation_data-tab">
                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <!-- new nvestigation button -->
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#new_investigation"><i class="fa fa-plus"></i> Új felülvizsgálat</button>
                                            <!-- investgation table -->
                                            <table class="table table-sm m-0" >
                                                <thead>
                                                    <tr>
                                                        <th>Dátum</th>
                                                        <th>Érték</th>
                                                        <th>Felhasználó</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="investigation_tbody">
                                                    @foreach($tool->investigations as $investigation)
                                                        <tr class='grey_background'>
                                                            <td>{{ $investigation->date }}</td>
                                                            <td>{{ $investigation->value }}</td>
                                                            <td>{{ $investigation->employee_name }}</td>
                                                            <td>
                                                                <div data-investigation_id="{{ $investigation->investigation_id }}" class="investigation_wrapper" >
                                                                    <button type="button" class="btn btn-danger delete_investigation" data-investigation_id="{{ $investigation->investigation_id }}">
                                                                        <i class="nc-icon nc-simple-remove"></i> Törlés
                                                                    </button>
                                                                </div> 
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                                    
                            </div>

                            <div class="tab-pane fade" id="docs_data" role="tabpanel" aria-labelledby="docs_data-tab">

                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0" >
                                                <tr class='grey_background'>
                                                    <td>
                                                        <form action="" class="tool_doc_form">
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-tool_doc_comment">Megjegyzés</label>
                                                                <input type="text" name="tool_doc_comment" id="input-tool_doc_comment" class="form-control">
                                                                <input type="hidden" name="tool_doc_input" value="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-doc_type_id">Dokumentum típus</label>
                                                                <select required name="doc_type_id" id="input-doc_type_id" class="form-control">
                                                                    <option value="">-</option>
                                                                    @foreach ($doc_types as $doc_type)
                                                                        <option value="{{ $doc_type->doc_type_id }}">{{ $doc_type->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-tool_doc_issue_date">Kiállítás dátuma</label>
                                                                <input type="date" name="tool_doc_issue_date" id="input-tool_doc_issue_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-tool_doc_expire_date">Lejárat dátuma</label>
                                                                <input type="date" name="tool_doc_expire_date" id="input-tool_doc_expire_date" class="form-control">
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="form-control-label" for="input-tool_doc_reminder_days">Emlékeztető határideje (nap)</label>
                                                                <input type="number" name="tool_doc_reminder_days" id="input-tool_doc_reminder_days" class="form-control">
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr class='grey_background'>
                                                    <td>
                                                        <div class="dropzone_area" >
                                                            <form action="/ajax/upload_tool_doc" id="tool_doc_form" class="dropzone needsclick" style="min-height: 7rem;" enctype='multipart/form-data'>
                                                                <div class="dz-message needsclick">
                                                                    <span class="">Húzza ide a fájlt vagy kattintson a feltöltéshez! Egyszerre maximum 1 fájlt tud feltölteni.</span>
                                                                    <input type="hidden" name="tool_id" value="{{ $tool->tool_id }}">
                                                                    @csrf
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-success mt-4 upload_tool_doc">Feltöltés</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="table table-sm m-0 tool_doc_table" >
                                                <thead>
                                                    <tr>
                                                        <th>Dokumentum</th>
                                                        <th>Megjegyzés</th>
                                                        <th>Típus</th>
                                                        <th>Kiállítás dátuma</th>
                                                        <th>Lejárat dátuma</th>
                                                        <th>Emlékeztető határideje (nap)</th>
                                                        <th>Feltöltés időpontja</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tool->documents as $doc)
                                                        <tr class='grey_background'>
                                                            <td><a href="{{ getenv('APP_URL') .'/document_storage/'. $doc->document_path }}" target="_blank">{{ $doc->document_path }}</a></td>
                                                            <td>{{ $doc->document_comment }}</td>
                                                            <td>@if($doc->document_type_id){{ $doc->doc_type->name }}@endif</td>
                                                            <td>{{ $doc->doc_issue_date }}</td>
                                                            <td>{{ $doc->doc_expire_date }}</td>
                                                            <td>{{ $doc->doc_reminder_days }}</td>
                                                            <td>{{ date('Y. m. d. H:i', strtotime($doc->created_at)) }}</td>
                                                            <td>
                                                                <div data-doc_id="{{ $doc->document_id }}" class="doc_wrapper" >
                                                                    <button type="button" class="btn btn-danger delete_doc" data-document_id="{{ $doc->document_id }}">
                                                                        <i class="nc-icon nc-simple-remove"></i> Törlés
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </fieldset>
                                    
                            </div>


                            <div class="tab-pane fade" id="buy_invoice_data" role="tabpanel" aria-labelledby="buy_invoice_data-tab">
                                <fieldset>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            
                                        </div>
                                    </div>
                                </fieldset>
                                    
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- new investigation modal -->
    <div class="modal fade" id="new_investigation" tabindex="-1" role="dialog" aria-labelledby="new_investigation" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                    <input type="hidden" name="tool_id" value="{{ $tool->tool_id }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Új felülvizsgálat</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Bezárás">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <fieldset>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label form-control-label" for="input-date">Dátum</label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" name="investigation_date" value="{{ date('Y-m-d') }}" id="input-date" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label form-control-label" for="input-sign_code">Aláíró kód</label>
                                        <div class="col-lg-8">
                                            <input type="text" oninput="handlePasswordInput(this)" autocomplete="one-time-code" class="form-control" name="investigation_sign_code" id="input-sign_code" required>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-lg-6">
                                <fieldset>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label form-control-label" for="input-value">Érték</label>
                                        <div class="col-lg-8">
                                            <input type="number" class="form-control" name="investigation_value" id="input-value" required>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Bezárás</button>
                        <button type="button" class="btn btn-primary save_investigation">Mentés</button>
                    </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
    <script>
        function handlePasswordInput(inputElement) {
            inputElement.type = 'password';
        }

        Dropzone.autoDiscover = false;

        $("#tool_doc_form").dropzone({
            maxFilesize: 50, // MB
            parallelUploads: 1,
            paramName: 'tool_doc',
            uploadMultiple: false,
            addRemoveLinks: true,
            maxFiles: 1,
            acceptedFiles: '.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf,.txt,.csv',
            dictRemoveFile: 'Törlés',
            dictCancelUpload: 'mégse',
            dictDefaultMessage: 'Kattintson vagy húzza ide a fájlt! <br /> (max 50MB)',
            renameFilename: cleanImageNameFn,
            success: function(file, response) {
                //add the file name as hidden input
                $('input[name=tool_doc_input]').val(response);
            },
            removedfile: function(file) {
                $('input[name=tool_doc_input]').val('');

                var class_name = file.name;
                class_name = class_name.replace(/[. ]/g,'');
                jQuery('.' + class_name).remove();
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            },
        });



        $(document).ready(function () {
            ClassicEditor
                .create( document.querySelector( '#editor' ) )
                .then( editor => {
                } )
                .catch( error => {
            } );
        

            $('.save_tool').click(function(){
                $('#tool_form').submit();
            });

            // Init Sliders
            $('.datepicker').datetimepicker({
                format: 'DD-MM-YYYY',
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-screenshot',
                    clear: 'fa fa-trash',
                    close: 'fa fa-remove'
                }
            });



            //save_investigation click
            $('.save_investigation').click(function(){

                if(!$('input[name=investigation_date]').val().length)
                {
                    $.notify({ icon: "done", message: "A dátum kitöltése kötelező." }, {type: 'danger',timer: 3000,z_index: 9000, placement: {from: 'top',align: 'right'}});
                    return false;
                }

                if(!$('input[name=investigation_sign_code]').val().length)
                {
                    $.notify({ icon: "done", message: "Az aláíró kód kitöltése kötelező." }, {type: 'danger',timer: 3000,z_index: 9000, placement: {from: 'top',align: 'right'}});
                    return false;
                }

                if(!$('input[name=investigation_value]').val().length)
                {
                    $.notify({ icon: "done", message: "Az érték kitöltése kötelező." }, {type: 'danger',timer: 3000,z_index: 9000, placement: {from: 'top',align: 'right'}});
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_tool_investigation",
                    data: { tool_id: '{{ $tool->tool_id }}', date: $('input[name=investigation_date]').val(), sign_code: $('input[name=investigation_sign_code]').val(), value: $('input[name=investigation_value]').val() }
                })
                .done(function( msg ) {
                    if(msg.success){
                        $('input[name=investigation_date]').val('');
                        $('input[name=investigation_sign_code]').val('');
                        $('input[name=investigation_value]').val('');

                        //felülvizsgálatok újra listázása
                        $.ajax({
                            method: "POST",
                            url: "/ajax/load_tool_investigations",
                            data: { tool_id: '{{ $tool->tool_id }}' }
                        })
                        .done(function( html ) {
                            $('.investigation_tbody').html(html);
                        });
                        $('#new_investigation').modal('hide');

                        $.notify({ icon: "done", message: msg.message }, {type: 'success',timer: 3000,z_index: 9000, placement: {from: 'top',align: 'right'}});
                    }else{
                        $.notify({ icon: "done", message: msg.message }, {type: 'danger',timer: 3000,z_index: 9000, placement: {from: 'top',align: 'right'}});
                    }
                });
            });

            $('.upload_tool_doc').click(function(){

                
                //ha van feltöltve kép, akkor legyen hozzá megadva név
                if(!$('input[name=tool_doc_input]').val().length)
                {
                    $.notify({ icon: "done", message: "Hiányzik a feltöltött file." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }

                if(!$('input[name=tool_doc_comment]').val().length)
                {
                    $.notify({ icon: "done", message: "A megjegyzés kitöltése kötelező." }, {type: 'danger',timer: 3000, placement: {from: 'top',align: 'right'}});
                    return false;
                }
                
                /*
                if(!$(".tool_doc_form")[0].checkValidity()) {
                    $('<input type="submit">').hide().appendTo($('.tool_doc_form')).click().remove();
                    return false;
                }
                */

                $.ajax({
                    method: "POST",
                    url: "/ajax/save_tool_doc_data",
                    data: { tool_id: "{{ $tool->tool_id }}", 
                            tool_doc_comment: $('input[name=tool_doc_comment]').val(), 
                            doc_type_id: $('select[name=doc_type_id]').val(), 
                            tool_doc_issue_date: $('input[name=tool_doc_issue_date]').val(), 
                            tool_doc_expire_date: $('input[name=tool_doc_expire_date]').val(), 
                            tool_doc_reminder_days: $('input[name=tool_doc_reminder_days]').val(), 
                            doc_file: $('input[name=tool_doc_input]').val() }
                })
                .done(function( msg ) {

                    var myDropzone = Dropzone.forElement("#tool_doc_form");
                    myDropzone.removeAllFiles(true);

                    $('input[name=tool_doc_comment]').val('');
                    $('input[name=tool_doc_input]').val('');
                    $('select[name=doc_type_id]').val('');
                    $('input[name=tool_doc_issue_date]').val('');
                    $('input[name=tool_doc_expire_date]').val('');
                    $('input[name=tool_doc_reminder_days]').val('');

                    //dokumentumok újra listázása
                    $.ajax({
                        method: "POST",
                        url: "/ajax/load_tool_docs",
                        data: { tool_id: '{{ $tool->tool_id }}' }
                    })
                    .done(function( html ) {
                        $('.tool_doc_table tbody').html(html);
                    });

                    $.notify({ icon: "done", message: "A dokumentum sikeresen feltöltve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                });
            });

            $('body').on('click', '.delete_doc', function() {

            let doc_id = $(this).attr('data-document_id');
            let row = $(this).parents('tr');

            swal({
                title: "Biztosan törli ezt a dokumentumot?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn btn-success",
                cancelButtonClass: "btn btn-danger",
                confirmButtonText: "Igen, törlöm",
                cancelButtonText: "Nem",
                closeOnConfirm: true, 
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        method: "POST",
                        url: "/ajax/delete_doc",
                        data: { doc_id: doc_id }
                    }).done(function(msg) {
                        row.remove();
                        $.notify({ icon: "done", message: "A dokumentum sikeresen törölve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                    });
                }
            });

            });

            //delete investigation
            $('body').on('click', '.delete_investigation', function() {

                let investigation_id = $(this).attr('data-investigation_id');
                let row = $(this).parents('tr');

                swal({
                    title: "Biztosan törli ezt a felülvizsgálatot?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn btn-success",
                    cancelButtonClass: "btn btn-danger",
                    confirmButtonText: "Igen, törlöm",
                    cancelButtonText: "Nem",
                    closeOnConfirm: true, 
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            method: "POST",
                            url: "/ajax/delete_tool_investigation",
                            data: { investigation_id: investigation_id }
                        }).done(function(msg) {
                            row.remove();
                            $.notify({ icon: "done", message: "A felülvizsgálat sikeresen törölve." }, {type: 'success',timer: 3000, placement: {from: 'top',align: 'right'}});
                        });
                    }
                });

            });
        });
    </script>
@endpush