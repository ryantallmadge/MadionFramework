<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 *
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * SET FLASH MESSAGE HELPER
 * Use: From anywhere in the script:
 *	setFlash();//Set the Flash message
 *	getFlash();//Get the Flash message
 *	hasFlash();//Bool to see if there is a falsh message
 */

  function setFlash($name, $value)
  {
   	$msg = serialize($value);
  	$_SESSION['flash_message'][$name] = $msg;
  }
  
  
  function getFlash($name, $default = null)
  {
    $msg = unserialize($_SESSION['flash_message'][$name]);
    if ($msg == "")
      return null;
    unset($_SESSION['flash_message'][$name]); // remove the session after being retrieve  
    return $msg;  
  }
  
  
  function hasFlash($name)
  {
    if (!isset($_SESSION['flash_message'][$name]))
    {
      return false;
    }
    return true;
  }