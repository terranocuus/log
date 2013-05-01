<?php

require 'log.class.php'; 

if (array_key_exists('action', $_POST) && $_POST['action'] == 'save') {

	//$list = $_POST;

	//$fp = fopen('file.csv', 'a');
	//fputcsv($fp, $list, ',', '"');
	//fclose($fp);
	
	echo '<pre>'; print_r($_POST); echo '</pre>';
	
	$log = new log('test');
	$log->store($_POST);
	}
	
?>

<html>
	<body>
		<form method="post">
			<input name="First Name" value="First Name" /><br />
			<input name="Last Name" value="Last Name" /><br />
			<input name="Email" value="Email" /><br /><br />
			<input name="action" value="save" type="submit" />
		</form>
	</body>
</form>
