<?php 


if(!class_exists('WPT_Prayer_Times'))
{
	class WPT_Prayer_Times
	{
		
		function WPT_Prayer_Initialization($ChangeCity = null)
		{
			$GetOption         = $this->WPT_Prayer_GetOption();
			$Country           = $this->WPT_Prayer_getSchool($GetOption);
			$WPT_Prayer_SpecificLocation   = $this->WPT_Prayer_SpecificLocation($GetOption);
			$WPT_Prayer_SpecificLatitude  = $this->WPT_Prayer_SpecificLatitude($GetOption);
			$WPT_Prayer_SpecificLongitude = $this->WPT_Prayer_SpecificLongitude($GetOption);
			$Schools           = $this->WPT_Prayer_Schools($GetOption);
			$this->WPT_Prayer_GenerateBlock($Country, $WPT_Prayer_SpecificLocation, $WPT_Prayer_SpecificLatitude, $WPT_Prayer_SpecificLongitude, $Schools, $ChangeCity);
		}


		function WPT_Prayer_GenerateBlock($Country, $WPT_Prayer_SpecificLocation, $WPT_Prayer_SpecificLatitude, $WPT_Prayer_SpecificLongitude, $Schools, $ChangeCity)
		{
			$FetchResult  = $this->WPT_Prayer_FetchResult($ChangeCity, $Country, $Schools); 
			return $this->WPT_Prayer_RenderHtml($FetchResult);
		}

		function WPT_Prayer_getSchool($GetOption)
		{
			if(empty($GetOption['WPT_CountryDropDown']))
			{
				$Country = 'Saudi Arabia';
			}else{
				$Country = $GetOption['WPT_CountryDropDown'];
			}
			return $Country;
		}

		function WPT_Prayer_SpecificLocation($GetOption)
		{
			return $GetOption['WPT_Specifc_PrayerTime'];
		}

		function WPT_Prayer_SpecificLatitude($GetOption)
		{
			return $GetOption['WPT_Specifc_PrayerTime_Latitude'];
		}

		function WPT_Prayer_SpecificLongitude($GetOption)
		{
			return $GetOption['WPT_Specifc_PrayerTime_Longitude'];
		}

		function WPT_Prayer_Schools($GetOption)
		{
			if($GetOption['WPT_SchoolDropDown'] == "")
			{
				$School = 3;
			}else{
				$School = $GetOption['WPT_SchoolDropDown'];
			}
			return $School;
		}

		function WPT_Prayer_BlockTitle($GetOption)
		{
			if(empty($GetOption['WPT_Specifc_PrayerTime_Title']))
			{
				echo '<h1>Prayer Times</h1>';
			}else{
				echo '<h1>' . $GetOption['WPT_Specifc_PrayerTime_Title'] . '</h1>';
			}
		}

		function WPT_Prayer_GetOption()
		{
			$WPT_Get_Prayer_Times = get_option("WPT_Prayer_Times");
			return $WPT_Get_Prayer_Times;
		}


		function WPT_Prayer_getCity()
		{
			$filename       = plugin_dir_path( __FILE__ ) . "cities.txt";
			$JsonCities = file_get_contents($filename);
			return $JsonCities;
		}


		

		function WPT_Prayer_FetchResult($City, $Country, $Schools)
		{
			$Country = str_replace(' ', '%20', $Country);
			$City = str_replace(' ', '%20', $City);
			$Url = 'https://api.aladhan.com/timingsByCity?city='.$City.'&country='.$Country.'&method='.$Schools;
			$JsonTimings = file_get_contents($Url);
			$JsonTimings = json_decode($JsonTimings);
			return $JsonTimings->data->timings;
		}

		function WPT_Prayer_RenderHtml($FetchResult)
		{
			$Table = '<table class="refreshTR"><tr><td>Namaz</td><td>Timing</td></tr>';
			foreach ($FetchResult as $key => $value) {
				$Table .= '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
			}
			$Table .= '</table>';
			echo $Table;
		}
	}

}





?>