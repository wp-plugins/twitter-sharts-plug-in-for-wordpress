<?php
/*
Plugin Name: Twitter Sharts
Description: 'Shart' your twitter status anywhere within your wordpress blog posts or pages! <a href="options-general.php?page=twitter sharts.php">Configure Twitter Sharts</a>
Author: bboyredcel
Version: 0.3.2
Plugin URI: http://www.wuntwo.com/downloads/twittersharts.zip
Author URI: http://www.wuntwo.com/
Update Server: http://www.wuntwo.com/downloads/twittersharts.zip
Min WP Version: 2.1
*/

// Twitter.com always has issues, lets supress errors
//error_reporting(0);

// Data
$shart = '[tweet]';

// Default username
$twitterDefaultUsername = "wuntwo";

if(get_option("twitterUserName")) {
	$twitterUsername = get_option("twitterUserName");
} else {
	// Do Nothing
}

function getTwitterStatus($twitterUsername) {
	$twitterXML = 'http://www.twitter.com/status/user_timeline/' . $twitterUsername . ".xml";
	if (!function_exists("simplexml_load_file")) {
		return "Your PHP Version does not support this plugin, please upgrade PHP to 5.x";
	}
	if(!@$twitter_xml_loaded = simplexml_load_file($twitterXML)) {
		return "Twitter.com is down ... AGAIN, OMFG";
	} else {
		return $twitter_xml_loaded->status->text;
	}
}

$status = getTwitterStatus($twitterUsername);

// Search for tweets, if found -- then replace
function wp_display_filter($content) {
	global $shart, $status, $twitterUsername;
	$the_content = str_replace($shart, $status, $content);
	$the_content = str_replace("[tweet-demo]", "[tweet]", $the_content);
	return $the_content;
}

// Setup the filters
add_filter('the_content', 'wp_display_filter');

// Options submenu
add_action('admin_menu', 'shart_options');

// Title of page, Name of option in menu bar, Which function prints out the html
function shart_options() {
	add_options_page(__('Twitter Sharts Options'), __('Twitter Sharts'), 5, basename(__FILE__), 'shart_options_page');
}

// HTML Options Page
function shart_options_page() {

	// Default username if none is specified
	global $twitterDefaultUsername;

	// did the user enter a new/changed location?
	if (isset($_POST['twitterUsername'])) {
		$twitterUsername = $_POST['twitterUsername'];
		update_option('twitterUsername', $twitterUsername);
		// and remember to note the update to user
		$updated = true;
	}

	// Grab the latest value for the users Twitter Username
	if(get_option('twitterUsername')) {
		$twitterUsername = get_option('twitterUsername');
	} else {
		add_option('twitterUsername', $twitterDefaultUsername, "My Twitter Username", "yes");
	}

	if ($updated) {
		echo '<div class="updated"><p><strong>Options saved.</strong></p></div>';
	}

	// Print the Options Page w/ form
	?>
	<div class="wrap">
		<h2>Twitter Sharts Username</h2>
		<form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<fieldset class="options">
                    <input id="twitterUsername" name="twitterUsername" value="<? echo get_option('twitterUsername'); ?>" />
			</fieldset>
			<p class="submit">
				<input type="submit" name="update_twitter_sharts" value="Update Options &raquo;" />
			</p>
	  	</form>
  	</div>

<?php

}

?>
