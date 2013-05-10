<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require_once COREDIR.'/thirdparty/mshell_mail'.EXT;

class email extends mshell_mail{
	
	public $email_from = '';
	public $message = '';

	function email(){
		parent::mshell_mail();
		$this->email_from = SYSTEM_EMAIL;
		return true;
	}
	
	
	function newuser($email){
		//Set the message to send
		$this->htmltext("Welcome to The Site");
		//Send the message
		$this->sendmail($email, "Welcome to The Site");
		$this->clear_bodytext();
		//return true
		return true;
	}
	
	
	
}