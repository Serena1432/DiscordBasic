<?php
function contains($needle, $haystack)
{
    return strpos($haystack, $needle) !== false;
}
error_reporting(0);
ini_set('display_errors', 0);
if (!isset($_COOKIE["authorization_token"])) header("Location: /");
if (isset($_GET["id"]) && isset($_GET["delete"]) && $_GET["confirm"] == "1") {
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_CUSTOMREQUEST => "DELETE",
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
		CURLOPT_URL => 'https://discord.com/api/channels/' . $_GET["id"] . "/messages/" . $_GET["delete"],
		CURLOPT_HTTPHEADER => array(
			'Authorization: ' . $_COOKIE["authorization_token"],
			'Host: discord.com'
		)
	));
	$resp = curl_exec($curl);
	$json = json_decode($resp);
	if(curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) != 204) echo 'Cannot delete the message.<br>Please try again.<br><a href="/messages.php?id=' . $_GET["id"] . '">Back to Messages</a>';
	else header("Location: /messages.php?id=" . $_GET["id"]);
	die();
}
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
	if (!$json->id) {
		echo 'An error has occurred. Please try again.<br><a href="/">Back to Home</a>';
		die();
	}
	else {
		$user = $json;
		if (isset($_POST["submit"])) {
			if (!isset($_POST["content"]) || $_POST["content"] == "") {
				echo 'Please type the message content.<br><a href="/messages.php?id=' . $_GET["id"] . '">Back to Messages</a>';
				die();
			}
			else {
				$curl = curl_init();
				if (isset($_GET["reply"]) && $_GET["reply"] != "") {
					$reference->channel_id = $_GET["id"];
					if (isset($_GET["guild_id"]) && $_GET["guild_id"] != "") $reference->guild_id = $_GET["guild_id"];
					$reference->message_id = $_GET["reply"];
				}
				if (!$reference) $data = array("content" => $_POST["content"]);
				else $data = array("content" => $_POST["content"], "message_reference" => $reference);
				if ($reference && !isset($_POST["author_ping"])) {
					$data->allowed_mentions->parse = ["users", "roles", "everyone"];
					$data->allowed_mentions->replied_user = false;
				}
				$data_string = json_encode($data);
				curl_setopt_array($curl, array(
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
					CURLOPT_POSTFIELDS => $data_string,
					CURLOPT_URL => 'https://discord.com/api/channels/' . $_GET["id"] . "/messages",
					CURLOPT_HTTPHEADER => array(
						'Authorization: ' . $_COOKIE["authorization_token"],
						'Accept: application/json',
						'Host: discord.com',
						'Content-Type: application/json',
						'Content-Length: ' . strlen($data_string)
					)
				));
				$resp = curl_exec($curl);
				$json = json_decode($resp);
				if ($json->id) header("Location: /messages.php?id=" . $_GET["id"] . "#message0");
				else {
					echo 'Cannot send the message. Please try again.<br><a href="/messages.php?id=' . $_GET["id"] . '">Back to Messages</a>';
					die();
				}
			}
		}
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
		if (isset($_GET["id"]) && isset($_GET["guild_id"]) && isset($_GET["reply"]) && isset($_GET["reply_username"]) && isset($_GET["reply_content"])) {
			echo '<p><b>Replying ' . $_GET["reply_username"] . ':</b><br>' . $_GET["reply_content"] . '</p><form method="POST"><textarea name="content"></textarea><br><input type="checkbox" name="author_ping" checked="checked" value="true"> Ping the message author<br><input type="submit" name="submit" value="Send" /></form>';
		}
		else if (isset($_GET["id"]) && isset($_GET["delete"])) {
			if ($_GET["confirm"] != "1") echo '<p><b>Delete this message?</b><br><br><b>' . $_GET["message_username"] . '</b><br>' . $_GET["message_content"] . '</p><a href="/messages.php?id=' . $_GET["id"] . '&delete=' . $_GET["delete"] . '&confirm=1"><button>Yes</button></a> <a href="/messages.php?id=' . $_GET["id"] . '"><button>No</button></a>';
		}
		else if (isset($_GET["id"])) {
			$before_text = "";
			if (isset($_GET["before"]) && $_GET["before"] != "") $before_text = "&before=" . $_GET["before"];
			else if (isset($_GET["after"]) && $_GET["after"] != "") $before_text = "&after=" . $_GET["after"];
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_URL => 'https://discord.com/api/channels/' . $_GET["id"],
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_COOKIE["authorization_token"],
					'Accept: application/json',
					'Host: discord.com'
				)
			));
			$resp = curl_exec($curl);
			$channel = json_decode($resp);
			echo '<h3>' . $channel->name . '</h3><p>' . $channel->topic . '</p>';
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
				CURLOPT_URL => 'https://discord.com/api/channels/' . $_GET["id"] . '/messages?limit=25' . $before_text,
				CURLOPT_HTTPHEADER => array(
					'Authorization: ' . $_COOKIE["authorization_token"],
					'Accept: application/json',
					'Host: discord.com'
				)
			));
			$resp = curl_exec($curl);
			$messages = json_decode($resp);
			echo '<p><a href="/index.php?server=' . $channel->guild_id . '">< Back to Channel Selection</a></p>';
			echo '<p><a href="/messages.php?id=' . $_GET["id"] . '&before=' . $messages[count($messages) - 1]->id . '">View older messages</a></p>';
			for ($i = count($messages) - 1; $i >= 0; $i--) {
				$message = $messages[$i];
				$duration = intval(time() - strtotime($message->timestamp));
				if ($duration / 604800 >= 1) $time_text = strval(intval($duration / 604800)) . " week(s)";
				else if ($duration / 86400 >= 1) $time_text = strval(intval($duration / 86400)) . " day(s)";
				else if ($duration / 3600 >= 1) $time_text = strval(intval($duration / 3600)) . " hour(s)";
				else if ($duration / 60 >= 1) $time_text = strval(intval($duration / 60)) . " minute(s)";
				else $time_text = "Just now";
				echo '<p id="message' . $i . '"><b>' . $message->author->username . '</b>';
				if ($message->referenced_message) echo '<br><i>(Replied ' . $message->referenced_message->author->username . '#' . $message->referenced_message->author->discriminator . ': ' . $message->referenced_message->content . ')</i>';
				$spaces = explode(" ", $message->content);
				for ($j = 0; $j < count($spaces); $j++) {
					$space = $spaces[$j];
					if (strpos($space, "@") == false && strpos($space, "<") == 0 && strpos($space, ":") == 1 && strpos($space, ">") != false) {
						$emoji_id = substr(explode(":", $space)[2], 0, strlen(explode(":", $space)[2]) - 1);
						$spaces[$j] = '<img src="https://cdn.discordapp.com/emojis/' . $emoji_id . '.jpg?size=16" />';
					}
					if (strpos($space, "@") != false && strpos($space, "<") == 0 && strpos($space, ">") != false) {
						$user_id = "";
						if (strpos($space, "!") == 2) $user_id = substr($space, 3, 18);
						else if (strpos($space, "&") == 2) $spaces[$j] = "<i>(Role mentioning is not supported yet.)</i>";
						else $user_id = substr($space, 2, 18);
						if ($user_id) {
							curl_setopt_array($curl, array(
								CURLOPT_RETURNTRANSFER => 1,
								CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
								CURLOPT_URL => 'https://discord.com/api/users/' . $user_id,
								CURLOPT_HTTPHEADER => array(
									'Authorization: ' . $_COOKIE["authorization_token"],
									'Accept: application/json',
									'Host: discord.com'
								)
							));
							$resp = curl_exec($curl);
							$mentioned_user = json_decode($resp);
							$spaces[$j] = "@" . $mentioned_user->username;
						}
					}
				}
				$message_content = implode(" ", $spaces);
				echo '<br>' . $message_content;
				if (count($message->attachments)) {
					foreach($message->attachments as $attachment) {
						echo '<br><i><a href="' . $attachment->url . '">Download attachment: ' . $attachment->filename . '</a></i>';
					}
				}
				echo '<br>' . $time_text . '<br>';
				if (count($message->reactions)) {
					foreach ($message->reactions as $reaction) {
						if ($reaction->emoji->id) echo '(' . $reaction->count . ' <img src="https://cdn.discordapp.com/emojis/' . $reaction->emoji->id . '.jpg?size=16" alt=":' . $reaction->emoji->name . ':"/>) ';
						else echo '(' . $reaction->count . ' ' . $reaction->emoji->name . ') ';
					}
					echo '<br>';
				}
				echo '<a href="/messages.php?guild_id=' . $channel->guild_id . '&id=' . $_GET["id"] . '&reply=' . $message->id . '&reply_username=' . urlencode($message->author->username . '#' . $message->author->discriminator) . '&reply_content=' . urlencode($message->content) . '">Reply</a> | <a href="/react.php?id=' . $message->id . '&channel_id=' . $_GET["id"] . '&message_username=' . urlencode($message->author->username . '#' . $message->author->discriminator) . '&message_content=' . urlencode($message->content) . '">React</a> | <a href="/messages.php?id=' . $_GET["id"] . '&mention=' . $message->author->id . '">Mention</a>';
				if ($message->author->id != $json->id && $channel->type != 1) echo ' | <a href="/create_dms.php?id=' . $message->author->id . '">Message in DM</a>';
				echo ' | <a href="/messages.php?id=' . $_GET["id"] . '&delete=' . $message->id . '&message_username=' . urlencode($message->author->username . '#' . $message->author->discriminator) . '&message_content=' . urlencode($message->content) . '">Delete</a></p>';
			}
			if ($messages[0]->id != $channel->last_message_id) echo '<p><a href="/messages.php?id=' . $_GET["id"] . '&after=' . $messages[0]->id . '">View newer messages</a></p>';
			echo '<form method="POST"><textarea name="content">';
			if (isset($_GET["mention"])) echo '<@' . $_GET["mention"] . '>';
			echo '</textarea><br><input type="submit" name="submit" value="Send" /></form>';
		}
		else echo '<p>Please specify a channel ID.</p><p><a href="/">Back to Home</a></p>';
	}
	curl_close($curl);
}
?>
<hr>
<p>Website made by Nico Levianth.</p>