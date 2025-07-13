<footer class="footer {{ request()->routeIs('home') ? '' : 'footer--normal' }} spad set-bg" data-setbg="{{ asset('img/footer-bg.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="footer__address">
                    <ul>
                        <li>
                            <i class="fa fa-phone"></i>
                            <p>Phone</p>
                            <h6>1-677-124-44227</h6>
                        </li>
                        <li>
                            <i class="fa fa-envelope"></i>
                            <p>Email</p>
                            <h6>DJ.Music@gmail.com</h6>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 offset-lg-1 col-md-6">
                <div class="footer__social">
                    <h2>Groove Nomad</h2>
                    <div class="footer__social__links">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-instagram"></i></a>
                        <a href="#"><i class="fa fa-dribbble"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 offset-lg-1 col-md-6">
                <div class="footer__newslatter">
                    <h4>Stay With me</h4>
                    <form action="#">
                        <input type="text" placeholder="Email" />
                        <button type="submit"><i class="fa fa-send-o"></i></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer__copyright__text">
            <p>
                Copyright &copy;
                <script>
                    document.write(new Date().getFullYear());
                </script>
                Tous droits réservés
            </p>
        </div>
    </div>
</footer>

<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('js/jquery.barfiller.js') }}"></script>
<script src="{{ asset('js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('js/jquery.slicknav.js') }}"></script>
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

<script src="{{ asset('js/jquery.jplayer.min.js') }}"></script>
<script src="{{ asset('js/jplayerInit.js') }}"></script>
