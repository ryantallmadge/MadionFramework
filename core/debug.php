<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Timer{
	
private $elapsedTime;

	// start timer
	public function start(){
		if(!$this->elapsedTime=$this->getMicrotime()){
			throw new Exception('Error obtaining start time!');
		}
	}

	// stop timer
	public function stop(){
			if(!$this->elapsedTime=round($this->getMicrotime()-$this->elapsedTime,5)){
				throw new Exception('Error obtaining stop time!');
			}
		return $this->elapsedTime;
	}
	
	//define private 'getMicrotime()' method
	private function getMicrotime(){
		list($useg,$seg)=explode(' ',microtime());
		return ((float)$useg+(float)$seg);
	}
	
	
} 