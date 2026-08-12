<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}">
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}">
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __($title) }}</title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  
        
        <!-- Fonts and Icon-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />


        <!-- CSS file-->
        @stack('css')
        <link type="text/css" href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('css/bootstrap-tourist.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('css/light-bootstrap-dashboard.css?v=2.0.1') }}" rel="stylesheet">
      
        <script src="{{ asset('js/core/bootstrap.min.js') }}" type="text/javascript"></script>
        <link href="{{ asset("css/fa-brands.css") }}" rel="stylesheet" />
        <link href="{{ asset("css/fa-duotone.css") }}" rel="stylesheet" />
        <link href="{{ asset("css/fa-light.css") }}" rel="stylesheet" />
        <link href="{{ asset("css/fa-regular.css") }}" rel="stylesheet" />
        <link href="{{ asset("css/fa-solid.css") }}" rel="stylesheet" />


        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        

        <link href="{{ asset("plugins/dropzone/dropzone.css") }}" rel="stylesheet" />
        <link href="{{ asset("plugins/lightgallery/lightgallery.bundle.css") }}" rel="stylesheet" />

        <link href="{{ asset("css/datatables.min.css") }}" rel="stylesheet" />
        <!--<link href="https://cdn.datatables.net/v/dt/dt-1.13.6/date-1.5.1/datatables.min.css" rel="stylesheet">-->

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
        <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">


        <link href="{{ asset("css/custom.css") }}" rel="stylesheet" />

    </head>
    <body>
        <div class="wrapper @guest wrapper-full-page @endguest">
            @auth
                @include('layouts.navbars.sidebar')
            @endauth
            <div class="@auth main-panel @endauth">
                @include('layouts.navbars.navbar')
                @yield('content')
                @include('layouts.footer.nav')
            </div>
        </div>
    </body>
    <!--   Core JS Files   -->
    <script src="{{ asset('js/core/jquery.3.6.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/core/popper.min.js') }}" type="text/javascript"></script>
    
    <script src="{{ asset('js/plugins/bootstrap-switch.js') }}"></script>

    <!--  Notifications Plugin    -->
    <script src="{{ asset('js/plugins/bootstrap-notify.js') }}"></script>
    <!--  Plugin for Date Time Picker and Full Calendar Plugin-->
    <script src="{{ asset('js/plugins/moment.min.js') }}"></script>
    <!--  DatetimePicker   -->
    <script src="{{ asset('js/plugins/bootstrap-datetimepicker.js') }}"></script>
    <!--  Sweet Alert  -->
    <script src="{{asset('js/plugins/sweetalert2.min.js') }}" type="text/javascript"></script>
    <!--  Tags Input  -->
    <script src="{{ asset('js/plugins/bootstrap-tagsinput.js') }}" type="text/javascript"></script>
    <!--  Sliders  -->
    <script src="{{ asset('js/plugins/nouislider.js') }}" type="text/javascript"></script>
    <!--  Bootstrap Select  -->
    <script src="{{ asset('js/plugins/bootstrap-selectpicker.js') }}" type="text/javascript"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!--  jQueryValidate  -->
    <script src="{{ asset('js/plugins/jquery.validate.min.js') }}" type="text/javascript"></script>

    <!--  Bootstrap Table Plugin -->
    <script src="{{ asset('js/plugins/bootstrap-table.js') }}"></script>
    <!--  DataTable Plugin -->
    <script src="{{ asset('js/plugins/jquery.dataTables.min.js') }}"></script>

    <!--  Full Calendar   -->
    <script src="{{ asset('js/plugins/fullcalendar.min.js') }}"></script>
    <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('js/light-bootstrap-dashboard.js?v=2.0.1') }}" type="text/javascript"></script>

    <!-- <script src="{{ asset('js/demo.js?v=1.0') }}"></script> -->
    <script src="{{ asset('plugins/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('plugins/lightgallery/lightgallery.bundle.js') }}"></script>
    <script src="{{ asset('js/plugins/inputmask.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
    
    <script src="{{ asset('js/script.js?v=1.0') }}"></script>

    @stack('js')
    <script>
        $(document).ready(function () {
            @if (session('status'))
              $.notify({
                icon: "done",
                message: "{{ session('status') }}"
              }, {
                type: 'success',
                timer: 3000,
                placement: {
                  from: 'top',
                  align: 'right'
                }
              });
            @endif

            @if (session('info'))
              $.notify({
                icon: "done",
                message: "{{ session('info') }}"
              }, {
                type: 'info',
                timer: 3000,
                placement: {
                  from: 'top',
                  align: 'right'
                }
              });
            @endif

            @if (session('error'))
              $.notify({
                icon: "done",
                message: "{{ session('error') }}"
              }, {
                type: 'danger',
                timer: 3000,
                placement: {
                  from: 'top',
                  align: 'right'
                }
              });
            @endif
            
        });
      </script>
</html>
