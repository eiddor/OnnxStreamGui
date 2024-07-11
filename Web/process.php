<?php

include "project.php";
include "config.php";

$proj = new Project();

$proj->load();

$negPrompt = isset($_POST['neg-prompt']) ? $_POST['neg-prompt'] : '';

// Check if 'sdxl' exists in the POST request, otherwise set a default value
$proj->sdxl = isset($_POST['sdxl']) ? $_POST['sdxl'] : 0;

if (! $proj->save($_POST['project'], $_POST['pos-prompt'], $_POST['neg-prompt'], $_POST['steps'], time(), false, $_POST['sdxl'])){
	print "nothing to process";
	die;		
}

if ($_POST['action'] == "SAVE PROJECT"){
	header("Content-Length: " . filesize("project.txt"));
	header('Content-Disposition: attachment; filename="' . $proj->name . '.txt"');
	
	readfile("project.txt");

	die;	
}

if ($_POST['action'] == "REFINE PROJECT"){
	if (! $proj->save($_POST['project'], $_POST['pos-prompt'], $_POST['neg-prompt'], $_POST['steps'], time(), true, $_POST['sdxl'])){
		print "nothing to process";
		die;		
	}	
}

if ($_POST['action'] == "REFINE PROJECT DROP IMAGE"){
	delete($proj->picture);
	
	if (! $proj->save($_POST['project'], $_POST['pos-prompt'], $_POST['neg-prompt'], $_POST['steps'], time(), true, $_POST['sdxl'])){
		print "nothing to process";
		die;		
	}	
}

if (substr_count($_POST['action'], "DELETE:") > 0){
	if (substr_count($_POST['action'], "snail") or 
		substr_count($_POST['action'], "rocket")){
		die;
	}
	
	//file_put_contents("test.txt", trim(str_replace("DELETE:", "", $_POST['action'])));
	
	delete(trim(str_replace("DELETE:", "", $_POST['action'])));
	die;
}




?>


<!DOCTYPE html>

<?php

if (! $_POST['pos-prompt']){
	print "nothing to process";
	die;
}

// DEBUG Log initial values
// file_put_contents('debug.log', "Initial values - sdxl: " . $proj->sdxl . ", sd_path: " . $sd_path . ", model_path: " . $model_path . ", additional_parameter: " . $additional_parameter . PHP_EOL, FILE_APPEND);
// END DEBUG

// Initial values based on the default SD model
$sd_path = $sd;
$model_path = $sdmodel;
$additional_parameter = ""; //none

if ($proj->sdxl == 1){
	$model_path = $sdxlmodel;
    $additional_parameter = "--xl";
	// DEBUG Log values
	// file_put_contents('debug.log', "Set for XL - sdxl: " . $proj->sdxl . ", sd_path: " . $sd_path . ", model_path: " . $model_path . ", additional_parameter: " . $additional_parameter . PHP_EOL, FILE_APPEND);
	// END DEBUG
} elseif ($proj->sdxl == 2){
	$model_path = $sdturbomodel;
    $additional_parameter = "--turbo";
	//DEBUG Log values
	//file_put_contents('debug.log', "Set for Turbo - sdxl: " . $proj->sdxl . ", sd_path: " . $sd_path . ", model_path: " . $model_path . ", additional_parameter: " . $additional_parameter . PHP_EOL, FILE_APPEND);
    //END DEBUG
} 
// DEBUG if default SD model
// else {
// file_put_contents('debug.log', "Default case - sdxl: " . $proj->sdxl . PHP_EOL, FILE_APPEND);
// }
// END DEBUG

// DEBUG script execution
//
//$command = sprintf('%s', $sd_shellscript . ' "' . $proj->picture . '" "' . $proj->posprompt . '" "' . $proj->negprompt . '" ' . $proj->steps . ' "' . $sd_path . '" "' . $model_path . '" ' . $additional_parameter);
//file_put_contents('debug.log', "Command: " . $command . PHP_EOL, FILE_APPEND);

//$output = shell_exec($command);
//file_put_contents('debug.log', "Output: " . $output . PHP_EOL, FILE_APPEND);

// Log final values
//file_put_contents('debug.log', "Final values - sdxl: " . $proj->sdxl . ", sd_path: " . $sd_path . ", model_path: " . $model_path . ", additional_parameter: " . $additional_parameter . PHP_EOL, FILE_APPEND);

// More debug
//$command = sprintf('%s', $sd_shellscript . ' "' . $proj->picture . '" "' . $proj->posprompt . '" "' . $proj->negprompt . '" ' . $proj->steps . ' "' . $sd_path . '" ' . $model_path . ' ' . $additional_parameter);
//exec($command, $output, $return_var);
//file_put_contents('debug.log', $command . PHP_EOL, FILE_APPEND);
//file_put_contents('debug.log', print_r($output, true) . PHP_EOL, FILE_APPEND);
//file_put_contents('debug.log', "Return var: $return_var" . PHP_EOL, FILE_APPEND);

// END DEBUG

// Let's run the actual command
// (only on linux... windows coming soon)
exec(sprintf('%s > /dev/null 2>&1 &', $sd_shellscript . ' "' . $proj->picture . '" "' . $proj->posprompt . '" "' . $proj->negprompt . '" ' . $proj->steps . ' "' . $sd_path . '" ' . $model_path . ' ' . $additional_parameter));

?>
