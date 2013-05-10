<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



//Load Databse config
include_once("db.php");

//Load Routes
include_once("routes.php");

//We are in the USA
setlocale(LC_MONETARY, 'en_US');

//Set the Domain Parts
$DOMAIN_PARTS = $_SERVER['HTTP_HOST'];//Set the domain name
	
//Set the Base URL's
define('BASE_URL', 'http://'.$DOMAIN_PARTS.'/');//Set the Base URL
define('IMAGES_URL', 'http://'.$DOMAIN_PARTS . '/images/');//Set the image URL

//Define the post and get settings
define('GET_METHOD', 1);
define('POST_METHOD', 2);

//set the system email to and from
define('SYSTEM_EMAIL', 'xxxxxx');

//set facebook vars
define('facebook_app_id', 'xxxxxx');
define('facebook_api_secret', 'xxxxxxx');


//set twitter vars
define('twitter_consumer_key', 'xxxxxx');
define('twitter_consumer_secret', 'xxxxxx');


define('COMPANY_IMAGE_PATH' , BASEPATH.'/user_images/companys/');
define('USERS_IMAGE_PATH' , BASEPATH.'/user_images/users/');
