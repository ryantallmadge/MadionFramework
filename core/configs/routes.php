<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * START THE ROUTING TO THE CONTROLLERS
 * USAGE: we take the URL param 'domain.com/param/param' 
 * and map it to a controller and method.
 * There are different routes for different request (GET/POST/AJAX/XML)
 * Adding the |L to the end of the route tells that you need to be loged in to access route
 * Adding the |P to the end of the route will push the user to the account or admin area
 */
 /*!!!!!!!!!!!!!!!!!!!WARNING THIS IS RUNNING PROCEDURALLY, THE ORDER MATTERS!!!!!!!!!!!!!!!!!*/
 
 
/*****DEFAULT*******/
//This is where we go when we have no route the URI = '/'
$route ['default']                 ['GET'] = 'frontpage/index';

/*****GET*******/
	//Company//////
	$route ['addcompany']              ['GET']  = 'companys/addcompany';
	$route ['companylogin']            ['GET']  = 'companys/companylogin';
	$route ['qrcode/:any']             ['GET']  = 'companys/qrcodecompanysignup';
	
	//Users//////
	$route ['login']                   ['GET']  = 'users/login|P';
	$route ['facebook']                ['GET']  = 'users/facebook_login|P';
	$route ['location/:any/:any']      ['GET']  = 'users/changecity';
	$route ['logout']                  ['GET']  = 'users/logout';
	$route ['verifyemail/:any']        ['GET']  = 'users/verifyemail';

/****LOGED IN ROUTES*****/
	//Users//////
	$route ['account']                 ['GET']  = 'users/account|UL';

/*****POST*******/
	//Company//////
	$route ['addcompany']              ['POST'] = 'companys/addcompany';
	$route ['companylogin']            ['POST'] = 'companys/companylogin';

	//Users//////
	$route ['createuser']              ['POST'] = 'users/createuser';
	$route ['login']                   ['POST'] = 'users/login|P';
	$route ['qrcode/:any']             ['POST'] = 'users/createqruser';

/*****AJAX*******/
	//Users//////
	$route ['login']                   ['AJAX'] = 'users/login';