<link rel="shortcut icon" href="./favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DiscordBasic</title>
<a href="/"><h2>DiscordBasic</h2></a>
<hr>
<h3>How to get the Discord Access Token</h3>
<p><b>NOTICE: We don't collect any of your information you gave to this website. All of your information is totally stored in your browser's cookies and all of the requests will be sent directly to the Discord server. If you're suspicious about this website, just don't use it.<br>Don't share your Access Token to anyone.</b></p>
<p>Currently, this website only supports MFA Access Token (Login Token) because this is the only token that allow you to send messages.</p>
<h3>On computer devices:</h3>
<p><b>Step 1:</b> Go to the <a href="https://discord.com/app">discord.com/app</a> website and log into your account.</p>
<p><b>Step 2:</b> <b>Press Ctrl+Shift+I</b> (Windows) or <b>Command+Option+I</b> (Mac) to open Inspect Element window.</p>
<p><b>Step 3:</b> Go to the <b>Network</b> tab at the menu bar on the top.</p>
<img src="./network.png" />
<p><b>Step 4:</b> Try sending a message or doing something that makes a request to the Discord API. After that, click on that network request (for example, <b>discord.com/api/channels/xxxxxxxxxxxxxxxxxx/messages</b>).</p>
<p><b>Step 5:</b> Go to the <b>Request Headers</b> part and look at the Authorization header. The header content is the login token, copy it and paste to the token box in the main page of this website.</p>
<img src="./authorization.png" />
<h3>On mobile devices:</h3>
<p>(coming soon)</p>
<hr>
<p>Website made by Nico Levianth.</p>