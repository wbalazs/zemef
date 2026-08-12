@extends('layouts.app', ['activePage' => 'holiday', 'activeButton' => 'holiday', 'title' => 'Zemef ERP', 'navName' => 'Összes szabadság' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Hozzáférés megtagadva</h3>
                                </div>
                            </div> 
                        </div>
                        <div class="card-body">
                            <p>Sajnáljuk, de nincs jogosultságod az oldal megtekintéséhez.</p>
                            <p><a href="{{ url('/') }}">Vissza a kezdőlapra</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection