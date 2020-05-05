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

window.$ = jQuery.default;

((html) => {
    html.className = html.className.replace(/\bno-js\b/, 'js');
})(document.documentElement);

$(() => {

    hamburgerMenu('.js-menu', '.js-hamburger');

    tns({
        container: '.js-hero-slider',
        slideBy: 'page',
        items: 1,
        autoplay: true,
        autoplayButtonOutput: false,
        prevButton: 'prev-button',
    });


    //$('.js-styled-select').niceSelect();

});


