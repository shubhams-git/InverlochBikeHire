<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* Here you can insert your functions, filters and actions. */







/* That's all, stop editing! Make a great website!. */

/* Init of the framework */

/* This function exist in WP 4.7 and above.
 * Theme has protection from runing it on version below 4.7
 * However, it has to at least run, to give user info about having not compatible WP version :-)
 */
if( function_exists('get_theme_file_path') ){
	/** @noinspection PhpIncludeInspection */
	require_once( get_theme_file_path( '/advance/class-apollo13-framework.php' ) );
}
else{
	/** @noinspection PhpIncludeInspection */
	require_once( get_template_directory() . '/advance/class-apollo13-framework.php' );
}

global $apollo13framework_a13;
$apollo13framework_a13 = new Apollo13Framework();
$apollo13framework_a13->start();


//mainly to help remove any extra recaptha
function rankya_contactform7check_dequeue() {
	$check_cf7 = false;
	if( is_page('Contact Us') ) {//this name follow contact us name
        	$check_cf7 = true;
    }
    if( !$check_cf7 ) {
      wp_deregister_script( 'contact-form-7' );
	  wp_dequeue_script( 'contact-form-7' );
	
	
	  //leave this here you MUST specify this if using recaptcha
	  wp_dequeue_script( 'google-recaptcha' );
	  wp_deregister_script( 'google-recaptcha' );
	  
	   //Styles
	   wp_deregister_style('contact-form-7');
       wp_dequeue_style( 'contact-form-7' );
		
    }
}
add_action( 'wp_enqueue_scripts', 'rankya_contactform7check_dequeue', 78 );