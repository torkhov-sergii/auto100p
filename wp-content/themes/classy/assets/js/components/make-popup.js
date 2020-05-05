//Only inline type now. Write the new condition to add new popup types

const makePopup = (selector, type) => {

    const $selector = $(selector);

    if(type === 'inline') {
        $selector.magnificPopup({
            src: $(this).attr('href'),
            type: type,
            removalDelay: 300,
            callbacks: {
                beforeOpen: function() {
                    this.st.mainClass = 'mfp-zoom-out';
                }
            },
        })
    }
    else {
        console.log('Write condition for this popup type in make_popup, please');
    }

};

export default makePopup;
