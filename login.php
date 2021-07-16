<?php
error_reporting(0);
ini_set('display_errors', 0);
if (isset($_COOKIE["authorization_token"])) header("Location: /");
$post_auth_token = $_POST["authorization_token"];
if ($_POST["submit"] && !$post_auth_token) echo 'Please input your authorization token.<br><a href="/">Back to Home</a>';
else if ($_GET["type"] == "logout") {
	setcookie("authorization_token", "", time() - 200);
	header("Location: /");
}
else {
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
		CURLOPT_URL => 'https://discord.com/api/users/@me',
		CURLOPT_HTTPHEADER => array(
			'Authorization: ' . $post_auth_token,
			'Accept: application/json',
			'Host: discord.com'
		)
	));
	$resp = curl_exec($curl);
	$json = json_decode($resp);
	if(curl_errno($curl)) echo 'Error: ' . curl_error($curl) . '<br>Please try again.<br><a href="/">Back to Home</a>';
	else if (!$resp) echo 'Cannot connect to Discord server. Please try again.<br><a href="/">Back to Home</a>';
	else if (!$json) echo 'Invalid authorization token. Please try again.<br><a href="/">Back to Home</a>';
	else if (!$json->id) echo 'Cannot get user ID. Please try again.<br><a href="/">Back to Home</a>';
	else if ($json->id) {
		setcookie("authorization_token", $post_auth_token, time() + 2592000);
		header("Location: /");
	}
	curl_close($curl);
}
?>