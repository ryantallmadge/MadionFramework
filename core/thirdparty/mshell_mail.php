<?php




class mshell_mail {
  var $errstr;
  var $headers;
  var $textbody;
  var $htmlbody;
  var $attachments;
  var $boundary;
 
  // Default constructor, sets up default header and boundary.
  function mshell_mail() {
    $this->attachments = array();
    $this->boundary = '_mshell_mail_boundary_';
    $this->headers = array(
         'From' => 'The Local Experience <'.SYSTEM_EMAIL.'>',
         'MIME-Version' => '1.0',
         'Content-Type' => "multipart/mixed; boundary=\"$this->boundary\""
    );
 
    $this->bodytext("Default Mail Message.");
  }
 
  // For debugging purposes you can display the body you are about
  // to send.
  function get_body() {
    $retval = $textbody;
    $retval .= $htmlbody;
    foreach($this->attachments as $tblck)
      $retval .= $tblck;
 
    return $retval;
  }
 
  // Convert the values in the header array into the correct format.
  function get_header() {
    $retval = "";
    foreach($this->headers as $key => $value)
      $retval .= "$key: $value\n";
 
    return $retval;
  }
 
  // Add your own header entry or modify a header.
  function set_header($name, $value) {
    $this->headers[$name] = $value;
  }
 
  // Attach a file to the message.
  function attachfile($file, $type = "application/octetstream")  {
    if(!($fd = fopen($file, "r"))) {
      $this->errstr = "Error opening $file for reading.";
      return 0;
    }
    $_buf = fread($fd, filesize($file));
    fclose($fd);
 
    $fname = $file;
    for($x = strlen($file); $x > 0; $x--)
      if($file[$x] == "/")
        $fname = substr($file, $x, strlen($file) - $x);
 
    // Convert to base64 becuase mail attachments are not binary safe.
    $_buf = chunk_split(base64_encode($_buf));
 
    $this->attachments[$file] = "\n--" . $this->boundary . "\n";
    $this->attachments[$file] .= "Content-Type: $type; name=\"$fname\"\n";
    $this->attachments[$file] .= "Content-Transfer-Encoding: base64\n";
    $this->attachments[$file] .= "Content-Disposition: attachment; " .
                                     "filename=\"$fname\"\n\n";
    $this->attachments[$file] .= $_buf;
 
    return 1;
  }
 
  function bodytext($text) {
    // Set the content type to text/plain for the text message.
    // 7bit encoding is simple ASCII characters, this is default.
    $this->textbody = "\n--" . $this->boundary . "\n";
    $this->textbody .= "Content-Type: text/plain\n";
    $this->textbody .= "Content-Transfer-Encoding: 7bit\n\n";
    $this->textbody .= $text;
  }
 
  function htmltext($text) {
    // Set the content type to text/html for the html message.
    // Also uses 7bit encoding.
    $this->htmlbody = "\n--" . $this->boundary . "\n";
    $this->htmlbody .= "Content-Type: text/html\n";
    $this->htmlbody .= "Content-Transfer-Encoding: 7bit\n\n";
    $this->htmlbody .= $text;
  }
 
  function clear_bodytext() { $this->textbody = ""; }
  function clear_htmltext() { $this->htmlbody = ""; }
  function get_error() { return $this->errstr; }
 
  // Send the headers and body using php's built in mail.
  function sendmail($to = "root@localhost", $subject = "Default Subject") {
    if(isset($this->textbody)) $_body .= $this->textbody;
    if(isset($this->htmlbody)) $_body .= $this->htmlbody;
 
    foreach($this->attachments as $tblck)
      $_body .= $tblck;
 
    $_body .= "\n--$this->boundary--";
 
    mail($to, $subject, $_body, $this->get_header());
  }
}
