<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Lets us upload a file
class PostedFile{
	//If a file is posted we will store the binary it here
	public $file;
	//Store the name of the file
	public $name;
	//Store the file MIME type
	public $type;
	//Store the file size in bytes
	public $size;
	//Store the temp path of the file
	public $path;
	//Store if we have a problem uploading the file
	public $error;

	//Construct the class by getting the file and its parts
	function PostedFile(&$f){
		$this->file = &$f;
		$this->name = $f['name'];
		$this->type = $f['type'];
		$this->size = $f['size'];
		$this->path = $f['tmp_name'];
		$this->error = $f['error'];
	}

	//Did we have an error when uploading the file?
	function hasError(){
		return $this->isUploaded() && $this->error != UPLOAD_ERR_OK;
	}

	//Did we upload the file to its new home with no problem?
	function isUploaded(){
		return $this->error != UPLOAD_ERR_NO_FILE;
	}

	//Did the file system accept the new file?
	function save($path){
		return @move_uploaded_file($this->path, $path);
	}
}

//Lets us handel _POST and _GET requests
class request{
	
	
	//Class construct load everything from the system identifiers
	function &Request($method){
		if(GET_METHOD & $method)
			foreach($_GET as $n=>$v)
				$this->$n = $v;
		if(POST_METHOD & $method){
			foreach($_POST as $n=>$v)
				$this->$n = $v;
			foreach($_FILES as $n=>$v)
				$this->$n = new PostedFile($v);
		}
		return $this;
	}

	//Lets us check if there was a file upload with the _POST data
	function isFile($name){
		return is_a($this->get($name), 'PostedFile');
	}

	//Lets us know if a specific param was in _POST or _GET
	function has($name){
		if(is_array($name)){
			foreach($name as $n)
				if(!isset($this->$n))
					return false;
			return true;
		}
		else
			return isset($this->$name);
	}

	//Returns the data of the param for the name given
	function get($name, $sterialize = false, $default = null){
		if($this->has($name)){
			if($sterialize)
				return addslashes($this->$name);
			else
				return $this->$name;
		}else{
			return $default;
		}
	}

	//Lets us know if a _POST just happened
	function isPosted(){
		return $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'];
	}
}

