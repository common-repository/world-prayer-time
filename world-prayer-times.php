<?php 

/**
* Plugin Name: World Prayer Times
* Plugin URI: https://umairshahblog.blogspot.com
* Description: prayer time for all over the world countries  
* Version: 2.0
* Author: Syed Umair Hussain Shah
* Author URI: https://umairshahblog.blogspot.com
**/


/***** No direct access to plugin PHP Files *****/
	defined( 'ABSPATH' ) or die( 'No script please!' );
	require_once( dirname( __FILE__ ) . '/admin/menu.php' );
	add_action( 'wp_ajax_my_action', 'WPT_Ajax_Callback' );
	add_action('wp_ajax_nopriv_my_action', 'WPT_Ajax_Callback');
	function WPT_Ajax_Callback() {
		require( dirname(__FILE__ ) . '/admin/class-world-prayer-time.php' );
		$ChangeCity = sanitize_text_field($_POST['ChangeCity']);
		$Object = new WPT_Prayer_Times();
		$Object->WPT_Prayer_Initialization($ChangeCity);
		wp_die();
	}

	function World_Prayer_Time_func( $atts ){ 
		wp_enqueue_script( 'WPT_Ajax_Callback', '/path/to/settings.js', array( 'jquery' ) );
		wp_localize_script( 'WPT_Ajax_Callback', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		?>
		<style type="text/css">
			.DropDownLoader{
					background-image: url('<?php echo plugins_url( 'images/loader.gif', __FILE__ ); ?>');
				    display: block;
				    height: 82px;
				    background-repeat: no-repeat;
				    margin-top: -41px;
			}
		</style>
		<script type="text/javascript">
			jQuery(function(){
				jQuery('.DropDownLoader').hide();
				jQuery(document).on('change', '#WPT_CityDropDown', function(){
					var $ChangeCity = jQuery(this).val();
					jQuery('.DropDownLoader').show();
					jQuery('#WPT_CityDropDown').prop('disabled', true);
					jQuery.ajax({
						type : 'POST',
						url  : MyAjax.ajaxurl,
						data : {ChangeCity : $ChangeCity, 'action' : 'my_action'},
						success : function(response)
						{	
							jQuery('.refreshTR').html(response);
							jQuery('.DropDownLoader').hide();
							jQuery('#WPT_CityDropDown').prop('disabled', false);
						}

					});
				});
			});
		</script>
		<?php

		require( dirname( __FILE__ ) . '/admin/class-world-prayer-time.php' );
		$Object = new WPT_Prayer_Times();
		echo '<div id="ShowPrayerTime">';
		$GetOption  = $Object->WPT_Prayer_GetOption();
		echo $Object->WPT_Prayer_BlockTitle($GetOption).'<table><tr><td>'.$Object->WPT_Prayer_getCity().'</td><td><span class="DropDownLoader"></span></td></tr></table>';
		ob_start();
		?>
		<script type="text/javascript">document.write(jQuery('#WPT_CityDropDown').val());</script>
		<?php
		$City = ob_end_clean();
		$Object->WPT_Prayer_Initialization(print_r($City, true));
		echo '</div>';
	}
    add_shortcode( 'WorldPrayerTime', 'World_Prayer_Time_func' );
	
	
	function World_Prayer_Time_Act_activation()
	{
		if (get_option('WPT_Prayer_Times')) 
		{
			delete_option( 'WPT_Prayer_Times' );
		}
	}
	register_activation_hook(   __FILE__ , 'World_Prayer_Time_Act_activation' );


?>