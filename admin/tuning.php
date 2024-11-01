<?php 

/*
* Plugin Name : World Prayer Times
* Plugin Author : Syed Umair Hussain Shah
* Support : go to umairshahblog.blogspot.com
*/

ini_set('max_execution_time', 5000);
$JsonCountries = file_get_contents("http://www.geognos.com/api/en/countries/info/all.json");
$JsonCountries = json_decode($JsonCountries);
$WPT_Get_Prayer_Times = get_option("WPT_Prayer_Times");
$WPT_CountryDropDown = esc_html($WPT_Get_Prayer_Times['WPT_CountryDropDown']);
$WPT_Specifc_PrayerTime = esc_html($WPT_Get_Prayer_Times['WPT_Specifc_PrayerTime']);
$WPT_Specifc_PrayerTime_Latitude = esc_html($WPT_Get_Prayer_Times['WPT_Specifc_PrayerTime_Latitude']);
$WPT_Specifc_PrayerTime_Longitude = esc_html($WPT_Get_Prayer_Times['WPT_Specifc_PrayerTime_Longitude']);
$WPT_SchoolDropDown = esc_html($WPT_Get_Prayer_Times['WPT_SchoolDropDown']);
$WPT_Specifc_PrayerTime_Title = esc_html($WPT_Get_Prayer_Times['WPT_Specifc_PrayerTime_Title']);

if(empty($WPT_CountryDropDown))
{
	$WPT_CountryDropDown = 'Saudi Arabia';
}

if(isset($_POST['WPT_Specifc_PrayerTime_Submit']))
{
	$WPT_CountryDropDown = (isset($_POST['WPT_CountryDropDown']) && !empty($_POST['WPT_CountryDropDown']) ? $_POST['WPT_CountryDropDown'] : '');
	$WPT_Specifc_PrayerTime = (isset($_POST['WPT_Specifc_PrayerTime']) && !empty($_POST['WPT_Specifc_PrayerTime']) ? $_POST['WPT_Specifc_PrayerTime'] : '');
	$WPT_Specifc_PrayerTime_Latitude = (isset($_POST['WPT_Specifc_PrayerTime_Latitude']) && !empty($_POST['WPT_Specifc_PrayerTime_Latitude']) ? $_POST['WPT_Specifc_PrayerTime_Latitude'] : '');
	$WPT_Specifc_PrayerTime_Longitude = (isset($_POST['WPT_Specifc_PrayerTime_Longitude']) && !empty($_POST['WPT_Specifc_PrayerTime_Longitude']) ? $_POST['WPT_Specifc_PrayerTime_Longitude'] : '');
	$WPT_SchoolDropDown = (isset($_POST['WPT_SchoolDropDown']) && $_POST['WPT_SchoolDropDown'] != "" ? $_POST['WPT_SchoolDropDown'] : '');
	$WPT_Specifc_PrayerTime_Title = (isset($_POST['WPT_Specifc_PrayerTime_Title']) && !empty($_POST['WPT_Specifc_PrayerTime_Title']) ? $_POST['WPT_Specifc_PrayerTime_Title'] : '');

	$WPT_Prayer_Times =  array(
		'WPT_CountryDropDown'              => sanitize_text_field($WPT_CountryDropDown), 
		'WPT_Specifc_PrayerTime'           => sanitize_text_field($WPT_Specifc_PrayerTime), 
		'WPT_Specifc_PrayerTime_Latitude'  => sanitize_text_field($WPT_Specifc_PrayerTime_Latitude), 
		'WPT_Specifc_PrayerTime_Longitude' => sanitize_text_field($WPT_Specifc_PrayerTime_Longitude),
		'WPT_SchoolDropDown'               => sanitize_text_field($WPT_SchoolDropDown), 
		'WPT_Specifc_PrayerTime_Title'     => sanitize_text_field($WPT_Specifc_PrayerTime_Title)
	);

	if (!get_option('WPT_Prayer_Times')) {
		add_option('WPT_Prayer_Times',    $WPT_Prayer_Times);
	}else{
		update_option('WPT_Prayer_Times', $WPT_Prayer_Times);
	}

	$filename       = plugin_dir_path( __FILE__ ) . "cities.txt";
	$capitalCity    = WPT_Prayer_GetCapitalCity($WPT_CountryDropDown);
	$writeCitiesTxt = WPT_Prayer_getCity($WPT_CountryDropDown, $capitalCity);
	
	try{
		$handle = fopen($filename, 'w');
		fwrite($handle, $writeCitiesTxt);	
		fclose($handle);
	} catch (Exception $e) {
        die("file permision error");
    }
	//echo "<script>window.location=window.location.href;</script>";
	//exit();
}

$ArrayCountries = (array) $JsonCountries->Results;
sort($ArrayCountries);
?>

<center><h1>World Prayer Time</h1></center>
<div id="Messages">
	<div id="error" class="DisplayNone" style="display: none; border: 1px solid red;background-color: pink;padding: 12px;color: white;"></div>
	<div id="success" class="DisplayNone"></div>
</div>
<form method="post" id="WPT_Namaz">
	<table>
	<tr><h1>Country Setting</h1></tr>
	<tr><td>Select Country : </td><td>
		<select name="WPT_CountryDropDown" id="WPT_CountryDropDown" valid="true" error="Please Select Country">
			<option value="">Please Select</option>
			<?php 
				$UrlCountry = (isset($_GET['Country']) ? $_GET['Country'] : '');
				foreach ($ArrayCountries as $Countries) {
					if($Countries->Name == $WPT_CountryDropDown)
					{
						echo '<option value="'.$Countries->Name.'" selected="selected">'.$Countries->Name.'</option>';
					}else{
						echo '<option value="'.$Countries->Name.'">'.$Countries->Name.'</option>';	
					}
				}
			?>
		</select>
	</td></tr>
	<!-- For Version 2
		<tr><td>Show Specific Location Prayer Time : </td><td>
			<input type="checkbox" name="WPT_Specifc_PrayerTime" id="WPT_Specifc_PrayerTime" <?php //echo (!empty($WPT_Specifc_PrayerTime) ? 'checked="checked"': ''); ?> />
		</td></tr>
		<tr class="WPT_Specifc_PrayerTime_div"><td>Latitude : </td><td>
			<input type="text" name="WPT_Specifc_PrayerTime_Latitude" value="<?php //echo (!empty($WPT_Specifc_PrayerTime_Latitude) ? $WPT_Specifc_PrayerTime_Latitude : ''); ?>" size="20">
		</td></tr>
		<tr class="WPT_Specifc_PrayerTime_div"><td>Longitude : </td><td>
			<input type="text" name="WPT_Specifc_PrayerTime_Longitude" value="<?php //echo (!empty($WPT_Specifc_PrayerTime_Longitude) ? $WPT_Specifc_PrayerTime_Longitude : ''); ?>" size="20">
		</td></tr> 
	-->
		<tr class="WPT_Specifc_PrayerTime_School"><td>Schools : </td><td>
			<select name="WPT_SchoolDropDown" valid="true" error="Please Select School">
				<option value="3" <?php echo ($WPT_SchoolDropDown != "" && $WPT_SchoolDropDown == 3 ? 'selected="selected"' : ''); ?>>Muslim World League (MWL)</option>
				<option value="1" <?php echo ($WPT_SchoolDropDown != "" && $WPT_SchoolDropDown == 1 ? 'selected="selected"' : ''); ?> >University of Islamic Sciences, Karachi</option>
				<option value="0" <?php echo ($WPT_SchoolDropDown != "" && $WPT_SchoolDropDown == 0 ? 'selected="selected"' : ''); ?> >Shia Ithna-Ashari</option>
				<option value="2" <?php echo ($WPT_SchoolDropDown != "" && $WPT_SchoolDropDown == 2 ? 'selected="selected"' : ''); ?>>Islamic Society of North America (ISNA)</option> 
				<option value="4" <?php echo ($WPT_SchoolDropDown != "" && $WPT_SchoolDropDown == 4 ? 'selected="selected"' : ''); ?>>Umm al-Qura, Makkah</option>
				<option value="5" <?php echo ($WPT_SchoolDropDown != "" && $WPT_SchoolDropDown == 5 ? 'selected="selected"' : ''); ?>>Egyptian General Authority of Survey</option> 
				<option value="6" <?php echo ($WPT_SchoolDropDown != "" && $WPT_SchoolDropDown == 6 ? 'selected="selected"' : ''); ?>>Institute of Geophysics, University of Tehran</option>
			</select>
		</td></tr>
	<tr><td>Block Title : </td><td><input type="text" name="WPT_Specifc_PrayerTime_Title" id="WPT_Specifc_PrayerTime_Title" valid="true" error="Please add block title" placeholder="Block Title" value="<?php echo (!empty($WPT_Specifc_PrayerTime_Title) ? $WPT_Specifc_PrayerTime_Title : ''); ?>"></td></tr>
	<tr><td></td><td><input type="submit" name="WPT_Specifc_PrayerTime_Submit" onclick="return FormValidator('#WPT_Namaz');"  value="Save Setting"></td></tr>
	</table>
</form>

<script type="text/javascript">
	jQuery(function(){

		if(jQuery("#WPT_Specifc_PrayerTime").is(':checked'))
		{
			jQuery('.WPT_Specifc_PrayerTime_div').show();
		}else{
			jQuery('.WPT_Specifc_PrayerTime_div').hide();
		}
		jQuery("#WPT_Specifc_PrayerTime").change(function () {
			if(this.checked)
			{
				jQuery('.WPT_Specifc_PrayerTime_div').show();
			}else{
				jQuery('.WPT_Specifc_PrayerTime_div').hide();
			}	
		});
	});
	function FormValidator(FormID)
	{
		jQuery(FormID).find('input[type="text"],input[type="email"],input[type="date"],input[type="password"],input[type="tel"], select, textarea').each(function(){
			if(jQuery(this).attr('valid') == 'true' && jQuery(this).val() == "")
			{
				jQuery(this).addClass('error');
				jQuery(this).parents().find('#error').addClass('DisplayBlock');
				jQuery(this).parents().find('#error').html(jQuery(this).attr('error')+'<br>').show();
				return false;
			}else{
				jQuery(this).removeClass('error');
				if(!jQuery('.error').length)
				{
					jQuery(this).parents().find('#error').removeClass('DisplayBlock');
				}
			}
		});

		jQuery(FormID).find('.checkbox').each(function(){

			if(jQuery(this).attr('valid') == 'true' && jQuery(this).find('input[type="checkbox"]').is(':checked') == false)
			{
				jQuery(this).addClass('error');
				jQuery(this).parents().find('#error').addClass('DisplayBlock');
				jQuery(this).parents().find('#error').html(jQuery(this).attr('error')+'<br>');
			}else{
				jQuery(this).removeClass('error');
				if(!jQuery('.error').length)
				{
					jQuery(this).parents().find('#error').removeClass('DisplayBlock');
				}
			}
		});

		jQuery(FormID).find('.radio').each(function(){

			if(jQuery(this).attr('valid') == 'true' &&  jQuery(this).find('input[type="radio"]').is(':checked') == false)
			{
				jQuery(this).addClass('error');
				jQuery(this).parents().find('#error').addClass('DisplayBlock');
				jQuery(this).parents().find('#error').html(jQuery(this).attr('error')+'<br>');
			}else{
				jQuery(this).removeClass('error');
				if(!jQuery('.error').length)
				{
					jQuery(this).parents().find('#error').removeClass('DisplayBlock');
				}
			}
		});
		
		if(jQuery('#error').length && jQuery('.error').length)
		{
			return false;
		}else{
			return;
			//jQuery(FormID).submit();
		}
	}
</script>


<?php 
function WPT_Prayer_getCity($Country, $City)
{
	$JsonCities = file_get_contents("https://raw.githubusercontent.com/David-Haim/CountriesToCitiesJSON/master/countriesToCities.json");
	$JsonCities = json_decode($JsonCities);
	$CityDropDown = '<select name="WPT_CityDropDown" id="WPT_CityDropDown">';
	$CityDropDown .= '<option value="">Please Select</option>';
	if($JsonCities->$Country)
	{
		foreach ($JsonCities->$Country as $key => $Cities) {
			if($Cities == $City)
			{
				$CityDropDown .=  '<option value="'.trim($Cities).'" selected="selected">'.trim($Cities).'</option>';
			}else{
				$CityDropDown .=  '<option value="'.trim($Cities).'" >'.trim($Cities).'</option>';	
			}
		}
	}else{
		die('Current '.$Country.' are not supported');
	}
	$CityDropDown .= '</select>';
	return $CityDropDown;
}


function WPT_Prayer_GetCapitalCity($Country)
{
	$Country = str_replace(' ', '%20', $Country);
	$Url = 'https://restcountries.eu/rest/v1/name/'.$Country;
	$JsonCapitalCity = file_get_contents($Url);
	$JsonCapital = json_decode($JsonCapitalCity);
	if($Country == "India")
	{
		return "Delhi";
	}else if(empty($JsonCapital[0]->capital)){
		return explode(',' , $JsonCapital[1]->capital)[0];
	}else{
		return $JsonCapital[0]->capital;
	}
}

?>