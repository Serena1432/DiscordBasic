<!--
	DiscordBasic v1.0 by Nico Levianth - The Homepage part
	All API rights belong to Discord, Inc.
-->
<link rel="shortcut icon" href="./favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DiscordBasic</title>
<a href="/"><h2>DiscordBasic</h2></a>
<?php
error_reporting(0);
ini_set('display_errors', 0);
if (isset($_COOKIE["authorization_token"])) {
	echo '<hr>';
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
	if (!$json->id) {
		setcookie("authorization_token", "", time() - 200);
		echo 'Error while getting user ID. Please refresh the page and try again.<br><a href="/login.php?type=logout">Refresh</a>';
	}
	else if ($json->id) {
		$user = $json;
		echo '<p>Welcome to DiscordBasic, ' . $user->username . '#' . $user->discriminator . ' | <a href="/login.php?type=logout">Logout</a></p><hr>';
		if (isset($_GET["server"])) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_URL => 'https://discord.com/api/guilds/' . $_GET["server"],
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_COOKIE["authorization_token"],
					'Accept: application/json',
					'Host: discord.com'
				)
			));
			$resp = curl_exec($curl);
			$server = json_decode($resp);
			echo '<h3>' . $server->name . '</h3><p><b>Channel List</b> | <a href="/server_settings.php?id=' . $_GET["server"] . '">Server Settings</a></p><p>Select a text channel to get messages:</p><ul>';
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_URL => 'https://discord.com/api/guilds/' . $_GET["server"] . '/channels',
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_COOKIE["authorization_token"],
					'Accept: application/json',
					'Host: discord.com'
				)
			));
			$resp = curl_exec($curl);
			$channels = json_decode($resp);
			foreach ($channels as $channel) {
				if ($channel->type == 0) echo '<li><a href="/messages.php?id=' . $channel->id . '">#' . $channel->name . '</a></li>';
			}
			echo '</ul>';
		}
		else if (!isset($_GET["type"]) || $_GET["type"] == "servers") {
			echo '<p><b>Server List</b> | <a href="/index.php?type=friends">Friends List</a></p><ul>';
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_URL => 'https://discord.com/api/users/@me/guilds',
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_COOKIE["authorization_token"],
					'Accept: application/json',
					'Host: discord.com'
				)
			));
			$resp = curl_exec($curl);
			$servers = json_decode($resp);
			foreach ($servers as $server) {
				echo '<li><a href="/index.php?server=' . $server->id . '">' . $server->name . '</a></li>';
			}
		}
		else if ($_GET["type"] == "friends") {
			echo '<p><a href="/index.php?type=servers">Server List</a> | <b>Friends List</b></p><ul>';
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_URL => 'https://discord.com/api/users/@me/relationships',
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_COOKIE["authorization_token"],
					'Accept: application/json',
					'Host: discord.com'
				)
			));
			$resp = curl_exec($curl);
			$friends = json_decode($resp);
			foreach ($friends as $friend) {
				echo '<li><a href="/create_dms.php?id=' . $friend->user->id . '">' . $friend->user->username . '#' . $friend->user->discriminator . '</a></li>';
			}
		}
		echo '</ul>';
	}
	curl_close($curl);
}
else {
	echo '<p>Welcome to DiscordBasic, the Discord for very low-end devices!</p>
	<p>This website is a fan-made Discord without any CSS or JavaScript for very low-end devices with minimum resources usage.</p>
	<p>I am going to add more features as soon as possible.</p>
	<p>All of the information you gave to this website is totally stored on your browser\'s cookies and all requests will be sent directly to the Discord\'s official API so we don\'t collect any of your information.</p>
	<hr>
	<p>To continue using DiscordBasic, please type your authorization token:</p>
	<form method="POST" action="/login.php">
		<input type="text" name="authorization_token" />
		<input type="submit" name="submit" value="Login" />
	</form>
	<p>Don\'t know how to get the token? <a href="/how_to_get_token.php">Click here</a></p>';
}
?>
<hr>
<p>Website made by Nico Levianth.</p>