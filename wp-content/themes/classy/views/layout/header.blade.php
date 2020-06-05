<header class="header">

    <div class="top-strip">
        <div class="container">
            <div class="top-strip__list">
                <div class="top-strip__item">СТО Автосервис на Троещине</div>
                <div class="top-strip__item"><i class="fal fa-compass"></i>г. Киев, Троещина, ул. Пожарского, 2</div>
                <div class="top-strip__item"><i class="fal fa-envelope"></i><a class="top-strip__link" href="mailto:info@auto100p.com.ua">info@auto100p.com.ua</a></div>
                <div class="top-strip__item"><i class="fal fa-phone"></i><a class="top-strip__link" href="tel:+38 068 7897667">8 (068) 789-76-67</a></div>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="container">
            <div class="main__logo">
                <a href="/">
                    <img src="/wp-content/themes/classy/dist/img/logo.png">
                </a>
            </div>

            <div class="main__aside">
                <div class="main__info info-main">
                    <div class="info-main__item">
                        <i class="info-main__icon fal fa-phone"></i>

                        <div class="info-main__content">
                            <div><a href="tel:+38 068 7897667">8 (068) 789-76-67</a></div>
                            <div><a href="tel:+38 093 7897667">8 (093) 789-76-67</a></div>
                        </div>
                    </div>

                    <div class="info-main__item">
                        <i class="info-main__icon fal fa-alarm-clock"></i>

                        <div class="info-main__content">
                            <div>10:00 - 20:00</div>
                            <div>Ежедневно</div>
                        </div>
                    </div>
                </div>

                <div class="main__call">
                    <a href="#form" class="btn js-magnific-popup-inline-container">ПЕРЕЗВОНИТЕ МНЕ</a>
                </div>

                <div id="form" class="popup mfp-hide {{ isset($popup['classes']) ? $popup['classes'] : '' }}">

                    <div class="popup__title">
                        Перезвоните мне
                    </div>
                    <div class="popup__body">
                        {!! do_shortcode( '[contact-form-7 id="5" title="Contact form 1"]' ) !!}
                    </div>
                    <div class="popup__body form-section_contacts contact-form">{!! do_shortcode($popup['content']) !!}</div>

                </div>
            </div>

            <div class="main__hamburger">
                <button class="hamburger js-hamburger" type="button" tabindex="0" aria-label="Menu trigger">
                    <span class="hamburger__box"><i class="hamburger__inner"></i></span>
                </button>
            </div>
        </div>
    </div>

    {{
       wp_nav_menu([
           'menu' => 'menu-header',
           'container' => 'nav',
           'container_id' => FALSE,
           'container_class' => 'menu js-menu',
           'menu_class' => 'container',
           'depth' => 3,
           'walker' => new \Helpers\Excerpt_Walker
       ])
    }}
</header>
