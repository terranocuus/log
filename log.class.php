<?php

/*

	if(!$mail->Send()) {
		$errors[] = $mail->ErrorInfo;
		$log = new log;
		$log->file_name = "mail_errors";
		$log->subfolder = "errors/";
		$log->start_log($errors);
		}
	
	$log = new log;
	if (isset($_POST["log_file_name"]) && ($_POST["log_file_name"] != "")) {
		$log->file_name = $_POST["log_file_name"];
		}
	$log->start_log($form["logged"]);
	
	//NEW METHOD 5/1/2013
	$log = new log('filename');
	$log->store($array);

*/

class log {
	var $file_name		= "";
	var $logfolder		= "./logs/";
	var $subfolder		= "forms/";
	var $parsed_url		= array();
	
	var $log_path		= '';
	var $handler		= null;
	
	var $log_array		= array();
	var $log_keys		= array();
	var $log_type		= "create"; //update
	
	var $loaded_file	= "";
	var $loaded_keys	= array();
	
	
	function __construct($file_name="") {
		//default values
		$this->log_array = array(
			'Date'		=> date("Y-m-d"),
			'Day'		=> date("D"),
			'Time'		=> date("g:i:s a"),
			'Timestamp'		=> date('U'),
			'IP Address'	=> $_SERVER["REMOTE_ADDR"],
			);
		
		$this->parsed_url = parse_url($_SERVER["HTTP_REFERER"]);
			
		$this->prep_filename($file_name);
		
		if (file_exists($this->log_path)) {
			$this->log_type = 'update';
			$this->load_log();
			}
		}
	
	private function prep_filename($file_name="") {
		$this->file_name = $file_name;
		
		if ($this->file_name == "") {
			$this->file_name = str_replace("/", "-", trim($this->parsed_url['path'], '/'));
			}
		
		$this->log_path = $this->logfolder.$this->subfolder.$this->file_name.".csv";
		
		if (!file_exists(dirname($this->log_path))) {
			mkdir(dirname($this->log_path), 0755, true);
			}
		}
		
	private function load_log() {
		$this->handler		= fopen($this->log_path, 'r');
		$this->loaded_keys	= fgetcsv($this->handler, 0);
		fclose($this->handler);
		}
	
	public function store($array = array()) {
		if ($this->log_type == 'create') {
			$this->create();
			
		} else { //update
			$this->log_array = array_merge($this->log_array, $array);
			$this->log_keys = array_keys($this->log_array);
				
			$check_keys = array_udiff($this->log_keys, $this->loaded_keys, "strcasecmp");
			
			if (count($check_keys) > 0) {
				$this->rewrite($check_keys);
				}
				
			$this->append();
			}
			
		return true;
		}
	
	private function create() {
		$this->handler = fopen($this->log_path, 'w+'); // or die("can't open ".$this->log_path." to write");
		fputcsv($this->handler, array_keys($this->log_array));
		fputcsv($this->handler, $this->log_array);
		fclose($this->handler);
		
		return true;
		}
	
	private function append() {
		//rebuilds log_array with loaded_keys (in loaded_keys order)
		$this->log_array = array_replace(array_fill_keys($this->loaded_keys, ''), $this->log_array);
		
		$this->handler = fopen($this->log_path, 'a'); //or die("can't open ".$this->log_path." to write");
		fputcsv($this->handler, $this->log_array);
		fclose($this->handler);
		
		return true;
		}
		
	private function rewrite($new_keys=array()) {
		$this->loaded_keys = array_merge($this->loaded_keys, $new_keys);
		$this->loaded_file = file($this->log_path);
			unset($this->loaded_file[0]); //old keys
		
		$kw = fopen($this->log_path, 'c'); // or die("can't open ".$this->log_path." to write.");
		fputcsv($kw, $this->loaded_keys); 
		foreach ($this->loaded_file as $string) {
			fwrite($kw, $string);
			}
		fclose($kw);
		
		return true;
		}
	}
	
	
?>