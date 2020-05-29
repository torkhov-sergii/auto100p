/**
 * Antispam for contact form
 *
 * @example
 * initWPCF7Antispam();
 *
 * @returns {void}
 */
const initWPCF7Antispam = () => {
    const key = '745643534543745634532';

    $('.wpcf7-form').append("<input type='hidden' name='qqq'/>");

    $('input[name ="qqq"]').val(key);

    // To resubmit the form without reloading the page
    document.addEventListener('wpcf7submit', (event) => {
        setTimeout(() => {
            console.log('wpcf7submit');
            $('input[name ="qqq"]').val(key);
        }, 1000);

    }, false);
};

initWPCF7Antispam();
