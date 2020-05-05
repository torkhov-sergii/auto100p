const hamburgerMenu = (menu, hamburger) => {
    const $menu = $(menu);
    const $button = $(hamburger);
    const $all = $button.add($menu);

    $button.on('click', () => {
        $all.toggleClass('is-active');
    });

    if ($(window).width() < 992) {
        $(window).on('click', (e) => {
            if (!$(e.target).closest($all).length) {
                $all.removeClass('is-active');
            }
        });
    }
};

export default hamburgerMenu;
