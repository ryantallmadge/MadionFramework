<?php
/*
This PHP class is free software: you can redistribute it and/or modify
the code under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version. 

However, the license header, copyright and author credits 
must not be modified in any form and always be displayed.

This class is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

@author geoPlugin (gp_support@geoplugin.com)
@copyright Copyright geoPlugin (gp_support@geoplugin.com)
$version 1.01


This PHP class uses the PHP Webservice of http://www.geoplugin.com/ to geolocate IP addresses

Geographical location of the IP address (visitor) and locate currency (symbol, code and exchange rate) are returned.

See http://www.geoplugin.com/webservices/php for more specific details of this free service

*/
include_once(COREDIR . '/db_class.php');


class geoplugin {
	
	//the geoPlugin server
	var $host = 'http://www.geoplugin.net/php.gp?ip={IP}&base_currency={CURRENCY}';
		
	//the default base currency
	var $currency = 'USD';
	
	//initiate the geoPlugin vars
	var $ip = null;
	var $city = null;
	var $region = null;
	var $areaCode = null;
	var $dmaCode = null;
	var $countryCode = null;
	var $countryName = null;
	var $continentCode = null;
	var $latitute = null;
	var $longitude = null;
	var $currencyCode = null;
	var $currencySymbol = null;
	var $currencyConverter = null;
	var $zipcode = null;
	var $db;	
	function geoPlugin() {
		$this->db = new db_class();
	}
	
	function locate($ip = null) {
		
		global $_SERVER;
		
		if ( is_null( $ip ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$host = str_replace( '{IP}', $ip, $this->host );
		$host = str_replace( '{CURRENCY}', $this->currency, $host );
		
		$data = array();
		
		
		$response = $this->fetch($host);
		
		$data = unserialize($response);
		
		//set the geoPlugin vars
		$_SESSION['GEOLOCATE']['ip']                = $this->ip                = $ip;
		$_SESSION['GEOLOCATE']['city']              = $this->city              = $data['geoplugin_city'];
		$_SESSION['GEOLOCATE']['region']            = $this->region            = $data['geoplugin_region'];
		$_SESSION['GEOLOCATE']['areaCode']          = $this->areaCode          = $data['geoplugin_areaCode'];
		$_SESSION['GEOLOCATE']['dmaCode']           = $this->dmaCode           = $data['geoplugin_dmaCode'];
		$_SESSION['GEOLOCATE']['countryCode']       = $this->countryCode       = $data['geoplugin_countryCode'];
		$_SESSION['GEOLOCATE']['countryName']       = $this->countryName       = $data['geoplugin_countryName'];
		$_SESSION['GEOLOCATE']['continentCode']     = $this->continentCode     = $data['geoplugin_continentCode'];
		$_SESSION['GEOLOCATE']['latitude']          = $this->latitude          = $data['geoplugin_latitude'];
		$_SESSION['GEOLOCATE']['longitude']         = $this->longitude         = $data['geoplugin_longitude'];
		$_SESSION['GEOLOCATE']['currencyCode']      = $this->currencyCode      = $data['geoplugin_currencyCode'];
		$_SESSION['GEOLOCATE']['currencySymbol']    = $this->currencySymbol    = $data['geoplugin_currencySymbol'];
		$_SESSION['GEOLOCATE']['currencyConverter'] = $this->currencyConverter = $data['geoplugin_currencyConverter'];
		
		$response = $this->fetch('http://where.yahooapis.com/geocode?q='.$this->latitude.','.$this->longitude.'&gflags=R&appid=71102973f962cb811c9b6ca2e3239dbc5083eb18');
		
		$xml = new SimpleXMLElement($response);
		$this->zipcode = (string) $xml->Result[0]->postal;
		$_SESSION['GEOLOCATE']['zipcode'] = $this->zipcode;
		$_SESSION['GEOLOCATE']['zipcode_radius']  = $this->inradius($this->zipcode);
	}
	
	function changeGeoLocate($city,$state){
		
		$response = $this->fetch('http://where.yahooapis.com/geocode?q='.urlencode($city).','.urlencode($state).'&gflags=R&appid=71102973f962cb811c9b6ca2e3239dbc5083eb18');
		$xml = new SimpleXMLElement($response);
		$_SESSION['GEOLOCATE']['city']              = $this->city              = (string) $xml->Result[0]->city;
		$_SESSION['GEOLOCATE']['region']            = $this->region            = (string) $xml->Result[0]->statecode;
		$_SESSION['GEOLOCATE']['latitude']          = $this->latitude          = (string) $xml->Result[0]->latitude;
		$_SESSION['GEOLOCATE']['longitude']         = $this->longitude         = (string) $xml->Result[0]->longitude;				
		$_SESSION['GEOLOCATE']['zipcode']           = $this->zipcode           = (string) $xml->Result[0]->postal;
		$_SESSION['GEOLOCATE']['zipcode_radius']    = $this->inradius($this->zipcode);
		
	}
	
	function fetch($host) {

		if ( function_exists('curl_init') ) {
						
			//use cURL to fetch data
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $host);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
			$response = curl_exec($ch);
			curl_close ($ch);
			
		} else if ( ini_get('allow_url_fopen') ) {
			
			//fall back to fopen()
			$response = file_get_contents($host, 'r');
			
		} else {

			trigger_error ('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
			return;
		
		}
		
		return $response;
	}
	
	function convert($amount, $float=2, $symbol=true) {
		
		//easily convert amounts to geolocated currency.
		if ( !is_numeric($this->currencyConverter) || $this->currencyConverter == 0 ) {
			trigger_error('geoPlugin class Notice: currencyConverter has no value.', E_USER_NOTICE);
			return $amount;
		}
		if ( !is_numeric($amount) ) {
			trigger_error ('geoPlugin class Warning: The amount passed to geoPlugin::convert is not numeric.', E_USER_WARNING);
			return $amount;
		}
		if ( $symbol === true ) {
			return $this->currencySymbol . round( ($amount * $this->currencyConverter), $float );
		} else {
			return round( ($amount * $this->currencyConverter), $float );
		}
	}
	
	//@TODO update to only return the zipcodes of businesses in DB
	//@TODO make recursive to spread out to a max of 100 miles
	function inradius($zip,$radius = 20){
			$query="SELECT * FROM zipdata WHERE zipcode='$zip'";
			$r = $this->db->select($query);
			if($this->db->row_count > 0) {
				$row = $this->db->get_row($r);
				$lat=$row['lat'];
				$lon=$row['lon'];
				$query="SELECT zipcode FROM zipdata WHERE (POW((69.1*(lon-\"$lon\")*cos($lat/57.3)),\"2\")+POW((69.1*(lat-\"$lat\")),\"2\"))<($radius*$radius) ";
				$r = $this->db->select($query);
				if($this->db->row_count > 0) {
					while($row = $this->db->get_row($r)) {
						$zipArray[]=$row["zipcode"];
					}
				}
			}else{
				return "Zip Code not found";
			}
		 return $zipArray;
		} // end func

	
}

?>