<footer class="footer-wrapper">
    <div class="footer-wrapper__inside">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="footer-copyright">
                        @php
                            $footerText = getSetting('footer_text');
                        @endphp
                        <p><span>{{ $footerText }}</span></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="footer-menu text-end">
                        <ul>
                            <li>
                                <a href="#">About</a>
                            </li>
                            <li>
                                <a href="#">Team</a>
                            </li>
                            <li>
                                <a href="#">Contact</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</footer>
