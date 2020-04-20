/**
 * Smooth scrolling to anchor links
 *
 * @example
 * scrollToAnchorLinks('.nav-menu');
 * @author Fedor Kudinov <brothersrabbits@mail.ru>
 * @param {(string|Object)} element - selected item to perform the a clicked
 * @param {(number|string)} [scrollDuration=600] - determining how long the animation will run
 */
const scrollToElement = (element, scrollDuration) => {

    const $el = $(element), duration = scrollDuration || 600;
    const $header = $('.js-header');

    let offset, headerHeight = 0;

    $el.on('click', 'a[href*="#"]:not([href="#"])', function () {

        const target = $(this).attr('href');
        const $element = $(target);

        if ($header.length) {
            headerHeight = $header.outerHeight();
        }

        if (!$element.length) return;

        offset = $element.offset().top - headerHeight;

        $('html, body').animate({scrollTop: offset}, duration);

        return false;

    });

};

export default scrollToElement;
