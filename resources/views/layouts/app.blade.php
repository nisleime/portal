<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" />
    <title>{{ env('APP_EMPRESA') }} - @yield('title')</title>

    <!-- plugins -->
    @stack('plugins-styles')

    <!-- bundle -->
    <link rel="stylesheet" href="{{ asset('assets/bundle/app.css') }}">

    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @stack('component-styles')
    <livewire:styles />
</head>

<body>

    <div class="box-general">

        <div class="header">

            <div class="logo">
                <a href="{{ route('panel.dashboard.index') }}" title="Portal do Contador">
                    <img src="{{ asset('assets/images/logo-fold.png') }}" alt="C.MANAGER">
                </a>
            </div>

            <div class="topbar">

                <div class="toggle-menu">
                    <a href="#" class="btn btn-default"><i class="fas fa-outdent"></i></a>
                </div>

                <div class="right-box">
                    {{-- <livewire:general.notification/> --}}
                    <livewire:general.profile-dropdown/>
                </div>

            </div>

        </div>

        <livewire:general.menu-sidebar/>

        <div class="container">
            {{ $slot }}
        </div>

    </div>
    <!-- box-general -->

    @stack('modals')

    <!-- bundle -->
    <script src="{{ asset('assets/bundle/app.js') }}"></script>

    <!-- plugins -->
    @stack('plugins-scripts')

    <!-- custom js -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- fix error -->
    <script src="{{ asset('assets/js/fix-error.js') }}"></script>

    @stack('component-scripts')

    <livewire:scripts />
</body>

</html>
