import 'core-js/stable/promise';
import 'core-js/stable/array/from';
import 'core-js/stable/array/includes';
import 'core-js/stable/array/find-index';
import 'core-js/stable/string/includes';
import * as jQuery from 'jquery';
import './components/form-antispam';

import hamburgerMenu from './components/menu';

window.$ = jQuery.default;

((html) => {
    html.className = html.className.replace(/\bno-js\b/, 'js');
})(document.documentElement);

$(() => {

    hamburgerMenu('.js-menu', '.js-hamburger');

    $('.js-styled-select').niceSelect();

});
