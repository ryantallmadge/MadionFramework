<?php

//Session class for handeling sessions in the database

class session
{
  //session id - @var string
  private $id = '';
  //Keep session alive - @var bool
  private $alive = true;
  //Used to hold the database data
  private $dbc = NULL;
 
  //Lets start the session
  //using __construct here because we need to include a __destruct to clean up
  function __construct()
  {
	//Grab and overwrite the session handelers  
    session_set_save_handler(
      array(&$this, 'open'),
      array(&$this, 'close'),
      array(&$this, 'read'),
      array(&$this, 'write'),
      array(&$this, 'destroy'),
      array(&$this, 'clean'));
 	//Start the session
    session_start();
  }
 
 //Clean up the session data
  function __destruct()
  {
	  
	$this->clean(strtotime("-1 week"));
	  
    if($this->alive)
    {
      session_write_close();
      $this->alive = false;
    }
		
	
  }
 
 //remove the session item from the database and from the user
  function delete()
  {
    if(ini_get('session.use_cookies'))
    {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
      );
    }
 
    session_destroy();
 
    $this->alive = false;
  }
 
 
 //start the databse connection
  private function open()
  {    
    $this->dbc = new MYSQLi(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME)
      OR die('Could not connect to database.');
 
    return true;
  }
 
 //close the database connection
  private function close()
  {
    return $this->dbc->close();
  }
 
 //Get the session data from the database using the session id
 //@var sid int (id for the session)
  private function read($sid)
  {
    $this->id = $sid;
 
    $q = "SELECT `data` FROM `sessions` WHERE `id` = '".$this->dbc->real_escape_string($sid)."' LIMIT 1";
    $r = $this->dbc->query($q);
 
    if($r->num_rows == 1)
    {
      $fields = $r->fetch_assoc();
 
      return $fields['data'];
    }
    else
    {
      return '';
    }
  }
 
 //Insert the session data to the database using the session id and the data
 //@var sid int (id for the session)
 //@var data string (data to be stored)
  private function write($sid, $data)
  {
    $q = "REPLACE INTO `sessions` (`id`, `data`) VALUES ('".$this->dbc->real_escape_string($sid)."', '".$this->dbc->real_escape_string($data)."')";
    $this->dbc->query($q);
 
    return $this->dbc->affected_rows;
  }
  
 //Remove the complete session data
 //@var sid int (id for the session)
  private function destroy($sid)
  {
    $q = "DELETE FROM `sessions` WHERE `id` = '".$this->dbc->real_escape_string($sid)."'"; 
    $this->dbc->query($q);
 
    $_SESSION = array();
 
    return $this->dbc->affected_rows;
  }
 
 //Remove old data from the database - counting backwards from $expire
 //@var expire int (time from when you want to clean from)
  private function clean($expire)
  {
    $q = "DELETE FROM `sessions` WHERE DATE_ADD(`sesh_last_accessed`, INTERVAL ".(int) $expire." SECOND) < NOW()"; 
    $this->dbc->query($q);
 
    return $this->dbc->affected_rows;
  }
}