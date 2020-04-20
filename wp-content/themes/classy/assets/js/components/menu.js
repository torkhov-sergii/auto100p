const hamburgerMenu = (menu, hamburger) => {
    const $menu = $(menu);
    const $button = $(hamburger);
    const $all = $button.add($menu);

    $button.on('click', () => {
        $all.toggleClass('is-active');
        $menu.slideToggle(600);
    });

    if ($(window).width() < 992) {
        $(window).on('click', (e) => {
            if (!$(e.target).closest($all).length) {
                $all.removeClass('is-active');
                $menu.slideUp(600);
            }
        });
    }

    $(window).resize( () => {
        if($(window).width() > 992 && !$menu.hasClass('is-active')){
            $menu.css('display', 'block')
        }
        else if ($(window).width() < 992 && !$menu.hasClass('is-active')){
            $menu.css('display', 'none')
        }
    });
};

export default hamburgerMenu;