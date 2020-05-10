import 'core-js/stable/promise';
import 'core-js/stable/array/from';
import 'core-js/stable/array/includes';
import 'core-js/stable/array/find-index';
import 'core-js/stable/string/includes';
import * as jQuery from 'jquery';
import "tiny-slider/dist/tiny-slider.css";
import {tns} from 'tiny-slider/src/tiny-slider';

import './components/form-antispam';
import hamburgerMenu from './components/menu';
import 'magnific-popup';

window.$ = jQuery.default;

((html) => {
    html.className = html.className.replace(/\bno-js\b/, 'js');
})(document.documentElement);

$(() => {

    hamburgerMenu('.js-menu', '.js-hamburger');

    if ($('.js-hero-slider').length) {
        tns({
            container: '.js-hero-slider',
            slideBy: 'page',
            items: 1,
            autoplay: true,
            autoplayButtonOutput: false,
            prevButton: 'prev-button',
        });
    }

    $('.js-magnific-popup-container').magnificPopup({
        delegate: 'a', // child items selector, by clicking on it popup will open
        gallery: {
            enabled: true
        },
        type: 'image'
        // other options
    });

    //$('.js-styled-select').niceSelect();

});


