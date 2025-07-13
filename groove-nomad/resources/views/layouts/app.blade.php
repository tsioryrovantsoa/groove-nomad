@include('include.header')

<body>
    @include('include.navigation')

    {{-- <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="#"><i class="fa fa-home"></i> Accueil</a>
                        <span>@yield('title')</span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <section class="discography spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title center-title">
                        <h2>@yield('title')</h2>
                        <h1>@yield('title')</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                @yield('content')
            </div>
        </div>
    </section>

    @include('include.footer')
</body>

</html>
