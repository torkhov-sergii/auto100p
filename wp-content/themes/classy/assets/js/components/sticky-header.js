/**
 * Sticky Header
 *
 * @returns {void}
 */
const stickyHeader = () => {
    const className = 'is-sticky';
    const header = $('.js-header');
    const height = header.outerHeight();

    let scrollTop = 0;
    let hasClass = false;

    $(window).on('scroll', function () {
        scrollTop = $(this).scrollTop();
        hasClass = header.hasClass(className);

        if (scrollTop > height && !hasClass) {
            header.addClass(className);
        }

        if (scrollTop <= 1 && hasClass) {
            header.removeClass(className);
        }
    });
};

export default stickyHeader;
