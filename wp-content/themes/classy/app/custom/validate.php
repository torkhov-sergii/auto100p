<?php

//form validate
add_filter( 'wpcf7_validate', function ($result, $tag) {
    $form  = WPCF7_Submission::get_instance();
    $qqq = $form->get_posted_data('qqq');

//    if ( 'your-email-confirm' == $tag->name ) {
//        $your_email = isset( $_POST['your-email'] ) ? trim( $_POST['your-email'] ) : '';
//        $your_email_confirm = isset( $_POST['your-email-confirm'] ) ? trim( $_POST['your-email-confirm'] ) : '';
//
//        if ( $your_email != $your_email_confirm ) {
//            $result->invalidate( $tag, "Are you sure this is the correct address?" );
//        }
//    }

    if($qqq != '745643534543745634532') {
        echo 'error 12345';
        exit();
    }

    return $result;
}, 20, 2 );
