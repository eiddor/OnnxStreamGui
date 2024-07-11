<?php

class Project{
	//making them public for simplicity...
	public $name;
	public $posprompt;
	public $negprompt;
	public $steps;
	public $seed;
	public $picture;
	public $sdxl;
	
	function load(){
		$data = explode("
", file_get_contents("project.txt"));
		
		if (strlen($data[0]) > 2){
			$this->name = trim($data[0]);
			$this->posprompt = trim($data[1]);
			$this->negprompt = trim($data[2]);
			$this->steps = trim($data[3]);
			$this->seed = trim($data[4]);
			$this->picture = trim($data[5]);
			$this->sdxl = intval(trim($data[6]));
		}
		else{
			$this->name = "";
			$this->posprompt = "";
			$this->negprompt = "";
			$this->steps = "";
			$this->seed = "";
			$this->picture = "";
			$this->sdxl = 0;
		}
	}
	
	function save($name, $posprompt, $negprompt, $steps, $seed, $force_new_image = false, $sdxl = false){
		//keep the existing or creat if none...
		if ($force_new_image){
			$this->picture = "";
		}
		
		if ($this->picture == ""){
			$this->picture = md5(time());
			$this->picture .= ".png";
		}
		
		$this->steps = intval($steps);
		$this->seed = intval($seed);

		$this->name = substr(trim(strip_tags($name)), 0, 256);
		
		if (strlen($this->name) < 1){
			return false;
		}

		$this->posprompt = str_replace(array("
", "\r", "\n"), ", ", trim($posprompt));

		if (strlen($this->posprompt) < 1){
			return false;
		}

		$this->negprompt = str_replace(array("
", "\r", "\n"), ", ", trim($negprompt));

		file_put_contents("project.txt", $this->name . "
" . $this->posprompt . "
" . $this->negprompt . "
" . $this->steps . "
" . $this->seed . "
" . $this->picture . "
" . intval($this->sdxl));

		return true;
	}
}
