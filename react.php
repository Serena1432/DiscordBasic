<?php
function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}
error_reporting(0);
ini_set('display_errors', 0);
if (!isset($_COOKIE["authorization_token"])) header("Location: /");
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
	echo 'An error has occurred. Please try again.<br><a href="/">Back to Home</a>';
	die();
}
else {
	$user = $json;
    if (isset($_GET["emoji_id"]) && $_GET["emoji_id"] != "" && isset($_GET["emoji_name"]) && $_GET["emoji_name"] != "") {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_URL => 'https://discord.com/api/v9/channels/' . $_GET["channel_id"] . '/messages/' . $_GET["id"] . '/reactions/' . $_GET["emoji_name"] . '%3A' . $_GET["emoji_id"] . '/%40me',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $_COOKIE["authorization_token"],
                'Host: discord.com',              
                'Content-Type: application/json',
                'Content-Length: 0'
            )
        ));
        $resp = curl_exec($curl);
        $json = json_decode($resp);
        if(curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) != 204) {
            if ($json->message) echo 'Cannot react the message: ' . $json->message . '<br>Please try again.<br><a href="/messages.php?id=' . $_GET["channel_id"] . '">Back to Messages</a>';
            else echo 'Cannot react the message: ' . curl_error($curl) . '<br>Please try again.<br><a href="/messages.php?id=' . $_GET["channel_id"] . '">Back to Messages</a>';
        }
        else header("Location: /messages.php?id=" . $_GET["channel_id"]);
        die();
    }
}
?>
<link rel="shortcut icon" href="./favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DiscordBasic</title>
<a href="/"><h2>DiscordBasic</h2></a>
<?php
if (isset($_COOKIE["authorization_token"])) {
	echo '<hr>';
	if ($user) {
		echo '<p>Welcome to DiscordBasic, ' . $user->username . '#' . $user->discriminator . ' | <a href="/login.php?type=logout">Logout</a></p><hr>';
		if (isset($_GET["channel_id"]) && $_GET["channel_id"] != "") {
            if (isset($_GET["id"]) && $_GET["id"] != "") {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_URL => 'https://discord.com/api/channels/' . $_GET["channel_id"],
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $_COOKIE["authorization_token"],
                        'Accept: application/json',
                        'Host: discord.com'
                    )
                ));
                $resp = curl_exec($curl);
                $channel = json_decode($resp);
                if ($channel->id) {
                    if ((!isset($_GET["emoji_id"]) || $_GET["emoji_id"] == "") && (!isset($_GET["emoji_name"]) || $_GET["emoji_name"] == "")) {
                        curl_setopt_array($curl, array(
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                            CURLOPT_URL => 'https://discord.com/api/guilds/' . $channel->guild_id . '/emojis',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: ' . $_COOKIE["authorization_token"],
                                'Accept: application/json',
                                'Host: discord.com'
                            )
                        ));
                        $resp = curl_exec($curl);
                        $emojis = json_decode($resp);
                        if ($emojis) {
                            echo '<p>Animated, external and Discord\'s emojis are not supported yet.</p><p>Choose an emoji to react to this message:</p><p><b>' . $_GET["message_username"] . '</b><br>' . $_GET["message_content"] . '</p><ul>';
                            foreach ($emojis as $emoji) {
                                if (!$emoji->animated) echo '<p><a href="/react.php?id=' . $_GET["id"] . '&channel_id=' . $_GET["channel_id"] . '&emoji_id=' . $emoji->id . '&emoji_name=' . $emoji->name . '"><img src="https://cdn.discordapp.com/emojis/' . $emoji->id . '.jpg?size=16" /> :' . $emoji->name . ':</a></p>';
                            }
                            echo '</ul>';
                        }
                        else echo '<p>Cannot get emoji information. Please try again.</p><p><a href="/messages.php?id=' . $_GET["channel_id"] . '">Back to Messages</a></p>';
                    }
                }
                else echo '<p>Cannot get channel information. Please try again.</p><p><a href="/messages.php?id=' . $_GET["channel_id"] . '">Back to Messages</a></p>';
                
            }
            else echo '<p>Please specify a message ID.</p><p><a href="/messages.php?id=' . $_GET["channel_id"] . '">Back to Messages</a></p>';
		}
		else echo '<p>Please specify a channel ID.</p><p><a href="/">Back to Home</a></p>';
	}
	curl_close($curl);
}
?>
<hr>
<p>Website made by Nico Levianth.</p>