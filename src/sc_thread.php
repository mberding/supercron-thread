<?php

class sc_thread {
	
	var $startTime;
	var $max_run_seconds;
	var $api_key;
	var $api_secret;
	var $external_id;
	var $force_abort = true;
	var $json;
	var $grace_padding = 5;
	var $id_thread;
	
	
	function __construct($params=array()) {
		$this->startTime = microtime(true);
		$this->external_id = $params['external_id'];
		$this->max_run_seconds = $params['max_run_seconds'];
		$this->force_abort = $params['force_abort'];
		$this->grace_padding = $params['grace_padding'];
		$this->api_key = $params['api_key'];
		$this->api_secret = $params['api_secret'];
	}
	
	function baseURL() {
		$url = 'https://supercron.me/api/threads.php?';
		$url .= '&api_key='.$this->api_key;
		$url .= '&api_secret='.$this->api_secret;
		return $url;
	}
	
	function start($params=array()) {
		
		$url = $this->baseURL();
		$url .= '&command=start';
		$url .= '&max_run_seconds='.$this->max_run_seconds;
		$url .= '&external_id='.rawurlencode($this->external_id);
		$result = $this->getPage($url);
		
		if($result!='') {
			$json = json_decode($result,true);
			if($json) {
				$this->json = $json;
				if($json['run_allowed']==true) {
					$this->id_thread = $json['id_thread'];
					if($this->max_run_seconds>0) {
						set_time_limit($this->max_run_seconds);
						ignore_user_abort(true);
					}
					return true;
				} else {
					$exit_text = '';
					if(@$json['error']!='') {
						$exit_text = $json['error'];
					}
					if($this->force_abort===true) {
						if($exit_text=='') {
							$exit_text = 'thread already running. hold expires in '.$json['seconds_to_expiration'].' seconds';
						}
						exit($exit_text);
					}
					return false;
				}
			} else {
				if($this->force_abort===true) {
					exit('error parsing json');
				}
			}
		}
		if($this->force_abort===true) {
			exit('error fetching thread manager');
		}
		return false;
	}
	
	function stop($params=array()) {
		
		$url = $this->baseURL();
		$url .= '&command=stop';
		$url .= '&external_id='.rawurlencode($this->external_id);
		$url .= '&id_thread='.rawurlencode($this->id_thread);
		$result = $this->getPage($url);
		
		if($result!='') {
			$json = json_decode($result,true);
			if($json) {
				$this->json = $json;
				return $json;
			}
		}
		if($force_abort===true) {
			exit('error fetching thread manager');
		}
		return false;
	}
	
	function keep_running($params=array()) {
		if( ($this->grace_padding + $this->getRunTimeSeconds()) < $this->max_run_seconds ) {
			return true;
		} else {
			return false;
		}
	}
	
	function getRunTimeSeconds() {
		$now = microtime(true);
		$run = $now - $this->startTime;
		return $run;
	}
	
	
	function getPage($target_url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$target_url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        return $result;
    }
}