@extends('layouts.app', ['activePage' => 'posting', 'activeButton' => 'posting', 'title' => 'Zemef ERP', 'navName' => 'Kiküldetések' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0"><th>Kiküldetések</th></h3>
                                </div>
                                @if(Auth::user()->hasPermission('create', App\Models\Posting::class))
                                    <div class="col-4 text-right">
                                        <a href="{{ route('posting.create') }}" class="btn btn-sm btn-primary">Új kiküldetés</a>
                                        <button type="button" class="btn btn-sm btn-primary generate_posting">Kiküldetési rendelvény generálása</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="table-responsive py-4" id="items-table">
                            <table id="datatables" data-name="postings_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>Időpont</th>
                                        <th>Honnan - hová</th>
                                        <th>Utazás célja</th>
                                        <th>Ügyfél</th>
                                        <th>Távolság (km)</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Posting::class))
                                            <th class="disabled-sorting text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Időpont</th>
                                        <th>Honnan - hová</th>
                                        <th>Utazás célja</th>
                                        <th>Ügyfél</th>
                                        <th>Távolság (km)</th>
                                        @if(Auth::user()->hasPermission('edit', App\Models\Posting::class))
                                            <th class="text-right">Műveletek</th>
                                        @endif
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($postings as $posting)
                                        <tr>
                                            <td>{{ $posting->posting_date }}</td>
                                            <td>{{ $posting->from_to }}</td>
                                            <td>{{ $posting->purpose }}</td>
                                            <td>{{ $posting->customer->name }}</td>
                                            <td>{{ $posting->distance }}</td>
                                            @if(Auth::user()->hasPermission('edit', App\Models\Posting::class))
                                                <td class="text-right">
                                                    @if (auth()->user()->can('update', $posting) || auth()->user()->can('delete', $posting))
                                                            @if(Auth::user()->hasPermission('edit', App\Models\Posting::class))
                                                                <a href="{{ route('posting.edit', $posting->posting_id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                            @endif
                                                            @if(Auth::user()->hasPermission('delete', App\Models\Posting::class))
                                                                <form class="d-inline-block" action="{{ route('posting.destroy', $posting->posting_id) }}" method="POST">
                                                                    @method('delete')
                                                                    @csrf
                                                                    <a class="btn btn-link btn-danger " onclick="confirm('Biztosan törli ezt a kiküldetést?') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                                </form>
                                                            @endif
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
        
            var table = $('#datatables').DataTable();

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });


            $('.generate_posting').click(function(){
                swal({
                    title: 'Válasszon időszakot!',
                    html: '<p><select name="year" class="form-control"><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="2027">2027</option><option value="2028">2028</option><option value="2029">2029</option><option value="2030">2030</option></select></p><p><select name="month" class="form-control"><option value="1">Január</option><option value="2">Február</option><option value="3">Március</option><option value="4">Április</option><option value="5">Május</option><option value="6">Június</option><option value="7">Július</option><option value="8">Augusztus</option><option value="9">Szeptember</option><option value="10">Október</option><option value="11">November</option><option value="11">December</option></select></p>',
                    showCancelButton: true,
                    closeOnConfirm: false,
                    allowOutsideClick: false,
                    confirmButtonText: 'PDF generálás',
                    cancelButtonText: "Mégse",
                },
                function() {

                    $.ajax({
                        method: "POST",
                        url: "/ajax/pdf/posting",
                        data: { year: $('select[name="year"]').val(), month: $('select[name=month]').val() }
                    })
                    .done(function( msg ) {
                        var file_path = "/pdf/postings/" + msg;
                        var a = document.createElement('A');
                        a.href = file_path;
                        a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    });

                })
            });


        });
    </script>
@endpush