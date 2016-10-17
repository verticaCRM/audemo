<?php
add_theme_support( 'post-thumbnails' ); 
function show_template() {
     if( is_super_admin() ){
         global $template;
         print_r($template);
     } 
 }
// add_action('wp_footer', 'show_template');



//jQuery Insert From Google
// function my_jquery_enqueue() {
//    wp_deregister_script('jquery');
//    wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") .
//         "://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js", false, null);
//    wp_enqueue_script('jquery');
// }
// add_action("wp_enqueue_scripts", "my_jquery_enqueue", 11);

