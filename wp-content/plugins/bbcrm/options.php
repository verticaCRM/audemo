<?php
add_action( 'admin_menu', 'bbcrm_add_admin_menu' );
add_action( 'admin_init', 'bbcrm_settings_init' );


function bbcrm_add_admin_menu(  ) { 

	add_menu_page( 'BBCRM Settings', 'BBCRM Settings', 'manage_options', 'bbcrm_settings', 'bbcrm_settings_options_page' );

}


function bbcrm_settings_init(  ) { 

	register_setting( 'pluginPage', 'bbcrm_settings' );

	add_settings_section(
		'bbcrm_pluginPage_section', 
		__( 'Your section description', 'bbcrm' ), 
		'bbcrm_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'bbcrm_text_field_0', 
		__( 'Settings field description', 'bbcrm' ), 
		'bbcrm_text_field_0_render', 
		'pluginPage', 
		'bbcrm_pluginPage_section' 
	);

	add_settings_field( 
		'bbcrm_select_field_1', 
		__( 'Settings field description', 'bbcrm' ), 
		'bbcrm_select_field_1_render', 
		'pluginPage', 
		'bbcrm_pluginPage_section' 
	);


}


function bbcrm_text_field_0_render(  ) { 

	$options = get_option( 'bbcrm_settings' );
	?>
	<input type='text' name='bbcrm_settings[bbcrm_text_field_0]' value='<?php echo $options['bbcrm_text_field_0']; ?>'>
	<?php

}


function bbcrm_select_field_1_render(  ) { 

	$options = get_option( 'bbcrm_settings' );
	?>
	<select name='bbcrm_settings[bbcrm_select_field_1]'>
		<option value='1' <?php selected( $options['bbcrm_select_field_1'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['bbcrm_select_field_1'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function bbcrm_settings_section_callback(  ) { 

	echo __( 'This section description', 'bbcrm' );

}


function bbcrm_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>BBCRM Settings</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}

?>
