<?php
/**
 * Plugin Overviews.
 * @package Maps
 * @author Flipper Code <flippercode>
 **/

?>

<div class="container wpgmp-docs">
<div class="row flippercode-main">
    <div class="col-md-12">
           <h4 class="alert alert-info"> <?php _e( 'How to Create Google Maps API Key',WPGMP_TEXT_DOMAIN ); ?> </h4>
           <div class="wpgmp-overview">
            <p>
            Since 22th June 2016, google maps doesn't work if google maps api key is not provided. Follow
            our guide to <a target="_blank" href="http://bit.ly/292gCV2">create your google maps api key </a>. Then go to <a target="_blank" href="<?php echo admin_url( 'admin.php?page=wpgmp_manage_settings' ); ?>">settings</a> page and insert your google maps api key.
            </p>
            <p>Below are the api's which will be enabled automatically. </p>
            <ul>
                <li>Google Maps JavaScript API</li>
                <li>Google Maps Geocoding API</li>
                <li>Google Maps Directions API</li>
                <li>Google Maps Distance Matrix API</li>
                <li>Google Maps Elevation API</li>
                <li>Google Places API Web Service</li>
            </ul>
           
            </div>
            <h4 class="alert alert-info"> <?php _e( 'Create your first map',WPGMP_TEXT_DOMAIN ); ?> </h4>
              <div class="wpgmp-overview">
                <ol>
                    <li><?php
                    $url = admin_url( 'admin.php?page=wpgmp_form_location' );
                    $link = sprintf( wp_kses( __( 'Use our auto suggestion enabled location box to add your location <a href="%s">here</a>. You can add multiple locations. All those locations will be available to choose when you create your map.', WPGMP_TEXT_DOMAIN ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
                    echo $link;?>
                    </li>
                    <li><?php
                    $url = admin_url( 'admin.php?page=wpgmp_form_map' );
                    $link = sprintf( wp_kses( __( 'Now <a href="%s">click here</a> to create a map. You can create as many maps you want to add.', WPGMP_TEXT_DOMAIN ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
                    echo $link;?>
                    </li>
                    <li><?php
                    $url = admin_url( 'admin.php?page=wpgmp_manage_map' );
                    $link = sprintf( wp_kses( __( 'When done with administrative tasks, you can display map on posts/pages using. Using shortcode, you can add maps on posts/pages. Enable map in the widgets section to display in sidebar.', WPGMP_TEXT_DOMAIN ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
                    echo $link;?>
                    </li>
                </ol>
            </div>
         
    </div>
</div>