    <header class="header {{ request()->routeIs('home') ? 'header' : 'header--normal' }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-2">
                    <div class="header__logo">
                        <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt=""
                                width="60" /></a>
                    </div>
                </div>
                <div class="col-lg-10 col-md-10">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>
                                <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                                    <a href="{{ route('home') }}">Accueil</a>
                                </li>
                                <li class="{{ request()->routeIs(patterns: 'festival.*') ? 'active' : '' }}">
                                    <a href="{{ route('festival.index') }}">Festival</a>
                                </li>
                                <li class="{{ request()->routeIs('request.create') ? 'active' : '' }}">
                                    <a href="{{ route('request.create') }}">Preferences</a>
                                </li>
                                @guest
                                    <li class="{{ request()->routeIs('auth.*') ? 'active' : '' }}">
                                        <a href="{{ route('auth.login') }}">Connexion</a>
                                    </li>
                                @endguest

                                @auth
                                    <li class="{{ request()->routeIs('request.index') ? 'active' : '' }}">
                                        <a href="{{ route('request.index') }}">Devis</a>
                                    </li>
                                    <li class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">
                                        <a href="{{ route('chat.index') }}">Chat IA</a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('auth.logout') }}">
                                            @csrf
                                            <button type="submit"
                                                style="background: none; border: none; padding: 0; color: white; cursor: pointer;">
                                                DECONNEXION
                                            </button>
                                        </form>
                                    </li>

                                @endauth
                            </ul>
                        </nav>
                        <div class="header__right__social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
