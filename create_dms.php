<?php
if (!isset($_COOKIE["authorization_token"])) header("Location: /");
else {
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
		CURLOPT_URL => 'https://discord.com/api/users/@me',
		CURLOPT_HTTPHEADER => array(
			'Authorization: ' . $_COOKIE["authorization_token"],
			'Accept: application/json',
			'Host: discord.com'
		)
	));
	$resp = curl_exec($curl);
	$json = json_decode($resp);
	if (!$json->id) echo 'An error has occurred. Please try again.<br><a href="/">Back to Home</a>';
	else {
		if (isset($_GET["id"])) {
			$data = array("recipient_id" => $_GET["id"]);
			$data_string = json_encode($data);
			curl_setopt_array($curl, array(
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_POSTFIELDS => $data_string,
				CURLOPT_URL => 'https://discord.com/api/users/@me/channels',
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_COOKIE["authorization_token"],
					'Accept: application/json',
					'Host: discord.com',
					'Content-Type: application/json',
					'Content-Length: ' . strlen($data_string)
				)
			));
			$resp = curl_exec($curl);
			$channel = json_decode($resp);
			if ($channel->id) header("Location: /messages.php?id=" . $channel->id);
			else echo 'An error has occured. Please try again.<br><a href="/">Back to Home</a>';
		}
		else echo 'Please type a recipient ID.<br><a href="/">Back to Home</a>';
	}
}
?>