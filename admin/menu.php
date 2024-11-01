<?php 

/*
* Plugin Name : World Prayer Times
* Plugin Author : Syed Umair Hussain Shah
* Support : go to umairshahblog.blogspot.com
*/


/****** Register Main Menu ******/
add_action('admin_menu', 'WPT_Plugin_Menu');



/****** Create Menu ******/
function WPT_Plugin_Menu(){

	add_menu_page( 'World Prayer Times', 'World Prayer Times', 'manage_options', 'world_prayer_times', 'WPT_Page_Tuning');
	add_submenu_page( 'world_prayer_times', 'Author', 'Author', 'manage_options', 'world_prayer_plugin_author',  'WPT_Page_Author');

}



/****** Create Menu Page ******/
function WPT_Page_Tuning()
{
	require( dirname( __FILE__ ) . '/tuning.php' );
}

/****** Create Menu Page ******/
function WPT_Page_Author()
{
	require( dirname( __FILE__ ) . '/author.php' );
}


?>