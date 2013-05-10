<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 *
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 * @title       UPLOADS HELPER
 *
 * Use: In the top of controller call: $this->load_helper('uploads');
 * Example: $upload =  $this->load_helper('uploads');
 *          $this->upload->COMPANY_IMAGES_UPLOAD(params);
 * Returns Bool
 * For errors implode $this->upload->return_error;
 */
		
class uploads{
	
	public $return_images = array();//Will hold the images names being uploaded
	public $return_error = array();//Will hold the error to be returned

	
	/*
	 *---------------------------------------------------------------
	 * USERS IMAGES UPLOAD
	 *---------------------------------------------------------------
	 *
	 * This is how we will upload images for users
	 * @var images Will hold the array of tmp images that are uploaded
	 * This will hold 288K users uploading at least 1 picture, there is a 32K folder limit on unix systems
	 * I am using the first number of the user id to seperate the users files, spread them out over 9 folders to beat the 32K Limit
	 */		
		function USER_IMAGES_UPLOAD($images = false, $userid = false){
		//Check and see if anything is blank or false, if so return false as e dont know where to put the image
		if(!$images OR $images == '' OR !$userid OR $userid == '') return false;
		//remove all the blank image fields
		$images = array_filter($images);
		//Get the first number in the userid, this will be where the images is stored
		$dir = $userid[0];
		//Set the Users actual directory to something non-predictable, keep rippers/hotlinkers on thier toes
		$user_dir = substr(sha1($userid),0,10);
		//Check if we need to create directories, if so create the directory
		if(!is_dir(USERS_IMAGE_PATH . $dir . '/')) mkdir(USERS_IMAGE_PATH . $dir . '/');
		if(!is_dir(USERS_IMAGE_PATH . $dir . '/' . $user_dir . '/')) mkdir(USERS_IMAGE_PATH . $dir . '/' . $user_dir . '/');
			//Loop through the images and set the data to a file to be called later
			for($i = 0; $i < count($images);$i++){
				//Get the file contents
				$imagescontents = file_get_contents($images[$i]);
				$this->return_images['images'.$i] = 'image_'.$user_dir.'_'.$i;
				//Open the file
				$myFile = USERS_IMAGE_PATH . $dir . '/' . $user_dir . '/image_'.$user_dir.'_'.$i;
				//Check if we can open the file
				if(!$fh = fopen($myFile, 'w')){
					//If we cant open the file, log it and return false
					$return_error = array('error','File Not Uploaded : '.$myFile);
					//return false
					return false;
				}				
				//Write the contents to the file
				$stringData = $imagescontents;
				fwrite($fh, $stringData);
				//Close the file
				fclose($fh);	
					
			}
		//We did it! return true!	
		return true;	
	}
	
}