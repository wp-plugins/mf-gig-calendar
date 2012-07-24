<?php
/*
Plugin Name: MF Gig Calendar
Description: A simple event calendar created for musicians but useful for anyone. Supports multi-day events, styled text, links, images, and more.
Version: 0.9.3
Author: Matthew Fries
Plugin URI: http://www.matthewfries.com/mf-gig-calendar
Author URI: http://www.matthewfries.com


Copyright (C) 2012 Matthew Fries

MF Gig Calendar is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

MF Gig Calendar is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/


// Stylesheet for display

function mfgigcal_load_stylesheet() {
    $url = plugins_url('mf_gig_calendar.css', __FILE__);
    wp_register_style('mfgigcal_css', $url);
    wp_enqueue_style( 'mfgigcal_css');
}
add_action('wp_print_styles', 'mfgigcal_load_stylesheet');




// [mfgigcal] - SHORT CODE functions

function mfgigcal_func( $atts ){
	return mfgigcal_getrows();
}
add_shortcode( 'mfgigcal', 'mfgigcal_func' );

function mfgigcal_getrows() {
	$mfgigcal_settings = get_option('mfgigcal_settings');
	global $wpdb;
	$mfgigcal_table = $wpdb->prefix . "mfgigcal";
	
	// get the dates
	$today = date("Y-m-d");
	
	$sql = "SELECT * FROM $mfgigcal_table ";
			
	if ($_GET[ytd]) {
		$sql .= "WHERE (end_date >= '$_GET[ytd]-01-01' AND start_date <= '$_GET[ytd]-12-31') ";
	}
	else if ($_GET[event_id]) {
		$sql .= "WHERE id = '$_GET[event_id]' ";
	}
	else {
		$sql .= "WHERE end_date >= '$today' ";
	}	
	$sql .= "ORDER BY start_date ASC";
	//$mfgigcal_events = GetArray($sql);
	
	$mfgigcal_events = $wpdb->get_results($sql);
	
	$mfgigcal_data = "";
	
	if ($mfgigcal_settings['rss']) {
		(get_option('permalink_structure')) ? $feed_link = "/feed/events" : $feed_link = "/?feed=events";
		$mfgigcal_data .= "<a href=\"$feed_link\" class=\"rss-link\">RSS</a>"; 
	}
	
	if (get_option('permalink_structure')) {
		$query_prefix = "?";
	}
	else {
		$existing = "?";
		foreach ($_GET as  $k => $v) {
			if ($k != "ytd" && $k != "event_id") $existing .= $k . "=" . $v . "&";
		}
		$query_prefix = $existing;
	}
	
	$mfgigcal_data .= mfgigcal_CalendarNav();
	
	if (empty($mfgigcal_events) && $mfgigcal_settings['no-events'] == "text") {
		$mfgigcal_data .= "<p>" . $mfgigcal_settings['message'] . "</p>";
		return $mfgigcal_data;
	}
	else if (empty($mfgigcal_events)) {
		$this_year = date("Y");
		// show the current year
		$sql = "SELECT * FROM $mfgigcal_table WHERE (end_date >= '$this_year-01-01' AND start_date <= '$this_year-12-31') ORDER BY start_date ASC";
		$mfgigcal_events = $wpdb->get_results($sql);
		if (empty($mfgigcal_events)) { 
			$mfgigcal_data .= "<p>" . $mfgigcal_settings['message'] . "</p>";
			return $mfgigcal_data;
		}
	}
	
	$mfgigcal_data .= "<ul id=\"cal\">\n";
	
	foreach ($mfgigcal_events as $mfgigcal_event) { 
	
		$mfgigcal_data .= "<li class=\"event\"><div class=\"date\">\n";
		$mfgigcal_data .= mfgigcal_FormatDate($mfgigcal_event->start_date, $mfgigcal_event->end_date);
		$mfgigcal_data .= "</div>\n";
			
		$mfgigcal_data .= "<div class=\"info_block\"><h3>";
		if (!$_GET[event_id]) {
			$mfgigcal_data .= "<a href=\"" . $query_prefix . "event_id=$mfgigcal_event->id\">" . $mfgigcal_event->title . "</a>";
		}	
		else {
			$mfgigcal_data .= $mfgigcal_event->title;
		}
		$mfgigcal_data .= "</h3>";
		$mfgigcal_data .= "<span class=\"time\">" . $mfgigcal_event->time . "</span>";
		$mfgigcal_data .= $mfgigcal_event->location;
		$mfgigcal_data .= $mfgigcal_event->details;
		
		$mfgigcal_data .= "</div></li>\n";
	
	}
	$mfgigcal_data .= "</ul>";
	

	return $mfgigcal_data;
	
}

function mfgigcal_FormatDate($start_date, $end_date) { // FUNCTION ///////////

	$startArray = explode("-", $start_date);
	$start_date = mktime(0,0,0,$startArray[1],$startArray[2],$startArray[0]);
	
	$endArray = explode("-", $end_date);
	$end_date = mktime(0,0,0,$endArray[1],$endArray[2],$endArray[0]);
	
	if ($start_date == $end_date) { 
		//print date("M j, Y", $start_date); // one day event
		$mfgigcal_date = "<div class=\"end-date\">";
			$mfgigcal_date .= "<div class=\"weekday\">" . date("D", $start_date) . "</div>";
			$mfgigcal_date .= "<div class=\"day\">" . date("d", $start_date) . "</div>";
			$mfgigcal_date .= "<div class=\"month\">" . date ("M", $start_date) . "</div>";
			$mfgigcal_date .= "<div class=\"year\">" . date ("Y", $start_date) . "</div>";
		$mfgigcal_date .= "</div>";
		return $mfgigcal_date;
	}
	else {
		//print date("M j, Y", $start_date); // multi-day event
		$mfgigcal_date = "<div class=\"start-date\">";
			$mfgigcal_date .= "<div class=\"weekday\">" . date("D", $start_date) . "</div>";
			$mfgigcal_date .= "<div class=\"day\">" . date("d", $start_date) . "</div>";
			$mfgigcal_date .= "<div class=\"month\">" . date ("M", $start_date) . "</div>";
			$mfgigcal_date .= "<div class=\"year\">" . date ("Y", $start_date) . "</div>";
		$mfgigcal_date .= "</div>";
		
		$mfgigcal_date .= "<div class=\"end-date\">";
			$mfgigcal_date .= "<div class=\"weekday\">" . date("D", $end_date) . "</div>";
			$mfgigcal_date .= "<div class=\"day\">" . date("d", $end_date) . "</div>";
			$mfgigcal_date .= "<div class=\"month\">" . date ("M", $end_date) . "</div>";
			$mfgigcal_date .= "<div class=\"year\">" . date ("Y", $end_date) . "</div>";
		$mfgigcal_date .= "</div>";
		return $mfgigcal_date;
	}
	
} // END OF FORMAT DATE FUNCTION!! ///////////////////////////



// ADMIN ==============================================

add_action('admin_menu', 'mfgigcal_admin_menu');
function mfgigcal_admin_menu() {
	$page = add_menu_page('Event Calendar', 'Event Calendar', 'manage_options', 'mf_gig_calendar', 'mfgigcal_admin');
	add_action('admin_head-' . $page, 'mfgigcal_admin_register_head');
	
	$page = add_submenu_page( 'mf_gig_calendar', 'Event Calendar Settings', 'Settings', 'manage_options', 'mf_gig_calendar_settings', 'mfgigcal_settings_page');
	add_action('admin_head-' . $page, 'mfgigcal_admin_register_head');
	
	$page = add_submenu_page( 'mf_gig_calendar', 'About MF Gig Calendar', 'About', 'manage_options', 'mf_gig_calendar_about', 'mfgigcal_about_page');
	add_action('admin_head-' . $page, 'mfgigcal_admin_register_head');
	
	//call register settings function
	add_action( 'admin_init', 'mfgigcal_register_settings' );
	
}

// Stylesheet for admin

function mfgigcal_admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/mf_gig_calendar_admin.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	echo '<script type="text/javascript" src="' . $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/mf_gig_calendar_admin.js"></script>';
	echo '<link rel="stylesheet" type="text/css" href="' . $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/datepicker-4.0.2/jquery.datepick.css" />';
	echo '<script type="text/javascript" src="' . $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/datepicker-4.0.2/jquery.datepick.js"></script>';
	echo '';
}


// Settings Page

function mfgigcal_register_settings() {
	//register our settings
	register_setting( 'mfgigcal_settings', 'mfgigcal_settings', 'mfgigcal_settings_validate' );
	

	add_settings_section('mfgigcal_settings_display', 'Oh Make My Events Behave', 'mfgigcal_settings_display_text', 'mfgigcal');
	
	
	add_settings_field('upcoming_title', 'Calendar Title', 'mfgigcal_settings_display_upcoming_field', 'mfgigcal', 'mfgigcal_settings_display');
	add_settings_field('display', 'Empty Upcoming Calendar', 'mfgigcal_settings_display_display_field', 'mfgigcal', 'mfgigcal_settings_display');
	add_settings_field('calendar_url', 'Calendar URL', 'mfgigcal_settings_display_url_field', 'mfgigcal', 'mfgigcal_settings_display');
	add_settings_field('rss', 'RSS Feed', 'mfgigcal_settings_display_rss_field', 'mfgigcal', 'mfgigcal_settings_display');
}

function mfgigcal_settings_validate($input) {
	return $input;
}



function mfgigcal_settings_display_text() {
	echo "<p>MF Gig Calendar provides you with some basic settings to control and personalize how you want your event calendar to appear in your Wordpress site.</p>";
}

function mfgigcal_settings_display_rss_field() {
	$options = get_option('mfgigcal_settings');
	?>
	<p><input id="rss" name="mfgigcal_settings[rss]" type="checkbox" value="1" <?php checked( '1', $options['rss'] ); ?> />
	Include an RSS feed link on your event page?</p>
	
	<?php
}

function mfgigcal_settings_display_display_field() {
    $siteurl = get_option('siteurl');
	$options = get_option('mfgigcal_settings');
	?>
	<p>It happens. Sometimes there just isn't much going on (or there's too much going on and you forgot to update your calendar on your website). 
	What do you want your event calendar to do in those rare
	moments when you don't have any upcoming events to display in your calendar?</p>
	<p><label><input type="radio" name="mfgigcal_settings[no-events]" value="archive" checked> Display the archive for the current year. <i>(default)</i></label><br />
	<label><input type="radio" name="mfgigcal_settings[no-events]" value="text" <?php checked( 'text', $options['no-events'] ); ?>> Display the following message:</label></p>
	<p><textarea id="message" name="mfgigcal_settings[message]" style="width:300px;"><?=$options['message']?></textarea></p>
	
	<?php
}

function mfgigcal_settings_display_url_field() {
    $siteurl = get_option('siteurl');
	$options = get_option('mfgigcal_settings');
	?>
	<p>You can put your event calendar on any Page or Post on your site, so you need to let MF Gig Calendar know where it is! Enter the URL for where you are displaying your calendar - the one that you want to use as the main link in the widget and the optional RSS feed</p>
	<p><input type="text" id="calendar_url" name="mfgigcal_settings[calendar_url]" style="width:300px;" value="<?=$options['calendar_url']?>"> <i>Example: <?=$siteurl?>/events</i></p>
	
	<?php
}

function mfgigcal_settings_display_upcoming_field() {
    $siteurl = get_option('siteurl');
	$options = get_option('mfgigcal_settings');
	($options['upcoming_title'] == "") ? $upcoming_title = "Upcoming Events" : $upcoming_title = $options['upcoming_title']
	?>
	<p>What title do you want to use in your event calendar when it is displaying upcoming events? Feel free to be creative!</p>
	<p><input type="text" id="upcoming_title" name="mfgigcal_settings[upcoming_title]" style="width:300px;" value="<?=$upcoming_title?>"></p>
	
	<?php
}

function mfgigcal_settings_page () {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	if( isset($_GET['settings-updated']) ) { ?>
		<div id="message" class="updated">
			<p><strong><?php _e('Settings saved.') ?></strong></p>
		</div>
	<?php } ?>
	
	<div class="wrap">
	<h2>Calendar Settings</h2>
	
	<form method="post" action="options.php">    
	<?php settings_fields( 'mfgigcal_settings' ); ?>
    <?php do_settings_sections('mfgigcal'); ?>

	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
	</div>
	
	<?php
}

function mfgigcal_about_page() {

    $siteurl = get_option('siteurl');
    
	echo '<div class="wrap">';
	echo '<h2>About MF Gig Calendar</h2>';
	
	?>
	
	<h3>Instructions</h3>
	<p>Easy! Just place this short code on any Page or Post where you want the event calendar to appear: </p>
	
	<pre>[mfgigcal]</pre>
	
	<p>That's it! Well, that and <a href="admin.php?page=mf_gig_calendar">adding some events</a> of course...</p>
	
	<p>The installation includes a handy widget you can place in your sidebar to show upcoming events. Be sure to also check out the 
	<a href="admin.php?page=mf_gig_calendar_settings">settings page</a> to get MF Gig Calendar behaving just the way you want.</p>
	
	<h3>Note from the Author</h3>
	
	<p>Hey there!</p>
	
	<p>Thanks for trying my MF Gig Calendar event plugin. I hope it helps you!</p>
	
	<p>I'm <strong>Matthew Fries</strong> (that's where the MF comes from) and I'm a NYC jazz pianist and part-time web developer. I developed this plugin because 
	I wanted a flexible and easy to use performance calendar for <a href="http://www.matthewfries.com">my own music website</a>. In the process I tried to create 
	something that would work for more than just musicians. 
	I've added a few features since it started - beginning and end dates for multiple-day events, a duplicate function to make it easier if you 
	have a repeating event that you don't want to re-enter over and over, an RSS feed, a widget to put upcoming events in the sidebar - 
	but as a general rule I've tried to keep this as 
	simple as possible. I think anyone who needs to display a list of events on their Wordpress site would find this useful.</p>
	
	<p>If you're a musician and you want really fancy - ticketing info, mapping, tour grouping, etc - you should check out 
	"<a href="http://wordpress.org/extend/plugins/gigs-calendar/">Gigs Calendar</a>" by <b>Dan Coulter</b>  over at 
	<a href="http://blogsforbands.com/" target="_blank">blogsforbands.com</a>. 
	He has a great plugin that I've also used quite a few times that has all kinds of cool features specific to musicians and fans. Really, it's great.</p>
	
	<p>
	This plugin is free for you to use, and if you find it useful I hope you'll take a moment and also <a href="http://www.matthewfries.com" target="_blank">check out my music</a>! I love playing piano and composing and think you'll probably 
	find <i>something</i> to like about what I play (unless you're one of those people who just hate everything - which would just be a sad, sad existence...).</p>

	<p><a href="http://www.matthewfries.com" target="_blank">www.matthewfries.com</a>&nbsp;&nbsp;&nbsp;<-- lots of jazz music to hear here...</p>
	<p>
	
	<a href="http://www.matthewfries.com/music/tri-fi-3/"><img src="<?=$siteurl?>/wp-content/plugins/mf-gig-calendar/images/cd_3.png" alt="TRI-FI 3" width="75" height="67" border="0" style="margin:5px;" /></a>
	<a href="http://www.matthewfries.com/music/a-tri-fi-christmas/"><img src="<?=$siteurl?>/wp-content/plugins/mf-gig-calendar/images/cd_TRI-FI-Christmas.png" alt="A TRI-FI Christmas" width="75" height="67" border="0" style="margin:5px;" /></a>
	<a href="http://www.matthewfries.com/music/postcards/"><img src="<?=$siteurl?>/wp-content/plugins/mf-gig-calendar/images/cd_Postcards.png" alt="TRI-FI Postcards" width="75" height="67" border="0" style="margin:5px;" /></a>
	<a href="http://www.matthewfries.com/music/tri-fi/"><img src="<?=$siteurl?>/wp-content/plugins/mf-gig-calendar/images/cd_TRI-FI.png" alt="TRI-FI" width="75" height="67" border="0" style="margin:5px;" /></a>
	<a href="http://www.matthewfries.com/music/"><img src="<?=$siteurl?>/wp-content/plugins/mf-gig-calendar/images/cd_Song-for-Today.png" alt="Song for Today" width="75" height="67" border="0" style="margin:5px;" /></a>
</p>

	<p>Please let me know what you think (of the plugin and the music!) - and if you have any suggestions or find any problems. While I do my best and this plugin is working great for me 
	and a lot of other people, I obviously can't guarantee your particular installation of Wordpress won't have problems.</p>
	
	<p>Have fun!<br />
	Matthew</p>
	<?php
	
	echo '</div>';
	
}

function mfgigcal_admin() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	// is there POST data to deal with?
	if ($_POST) {
		mfgigcal_save_record();
	}

	switch ($_GET[action]) {

		case "edit" :
			echo '<div class="wrap">';
			echo mfgigcal_edit_event();
			echo '</div>';
			break;
			
		case "delete" :
			mfgigcal_delete_event();
			echo '<div class="wrap">';
			echo mfgigcal_list_events();
			echo '</div>';
			break;
			
		case "copy" :
			echo '<div class="wrap">';
			echo mfgigcal_edit_event();
			echo '</div>';
			break;
			
		default :
			echo '<div class="wrap">';
			echo mfgigcal_list_events();
			echo '</div>';
	}

}

function mfgigcal_delete_event() {
	global $wpdb;
	$mfgigcal_table = $wpdb->prefix . "mfgigcal";
	$wpdb->query(
		"
		DELETE FROM $mfgigcal_table 
		WHERE id = '$_GET[id]'
		"
	);
}

function mfgigcal_save_record() {
	global $wpdb;
	$mfgigcal_table = $wpdb->prefix . "mfgigcal";
	
	remove_wp_magic_quotes();
	
	if ($_POST[id]) {  // update record
		$wpdb->update( 
			$mfgigcal_table, 
			array( 
				'start_date' => $_POST[start_date], 
				'end_date' => $_POST[end_date],
				'pub_date' => date("Y-m-d H:i:s"),
				'time' => $_POST[time],
				'title' => $_POST[title],
				'location' => $_POST[location],
				'details' => $_POST[details]
			), 
			array ( 'id' => $_POST[id]),
			array( 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s' 
			) 
		);
	}
	else { // new record
		$wpdb->insert( 
			$mfgigcal_table, 
			array( 
				'start_date' => $_POST[start_date], 
				'end_date' => $_POST[end_date],
				'pub_date' => date("Y-m-d H:i:s"),
				'time' => $_POST[time],
				'title' => $_POST[title],
				'location' => $_POST[location],
				'details' => $_POST[details]
			), 
			array( 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s' 
			) 
		);
	}
}

function mfgigcal_edit_event() {
	global $wpdb;
	$mfgigcal_table = $wpdb->prefix . "mfgigcal";
	
	$sql = "SELECT *
			FROM $mfgigcal_table
			WHERE id = $_GET[id]
			LIMIT 1";
			
	$mfgigcal_event = $wpdb->get_row($sql);
	
	if ($_GET[action] == "copy") {
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-d');
	}
	else {
		$start_date = $mfgigcal_event->start_date;
		$end_date = $mfgigcal_event->end_date;
	}
	
	echo "<h2>Edit Event</h2>";
	
	echo "<form method=\"POST\" action=\"?page=mf_gig_calendar\">";
	if ($_GET[action] == "edit") echo "<input type=\"hidden\" name=\"id\" value=\"$_GET[id]\" />";
		echo "<table class=\"form-table\"><tr>";
			echo "<th><label>Start Date (required)</label></th>";
			echo "<td><input type=\"text\" class=\"text datepicker form-required\" name=\"start_date\" id=\"start_date\" value=\"$start_date\" /> <label><input type=\"checkbox\" id=\"multi\" /> Multiple Day Event</label></td>";
		echo "</tr>";
		echo "<tr id=\"end_date_row\">";
			echo "<th><label>End Date</label></th>";
			echo "<td><input type=\"text\" class=\"text datepicker\" name=\"end_date\" id=\"end_date\" value=\"$end_date\" /></td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<th><label>Event Title (required)</label></th>";
			echo "<td><input type=\"text\" class=\"text form-required\" style=\"width:350px;\" name=\"title\" id=\"title\" value=\"" . str_replace('"', "&quot;", $mfgigcal_event->title) . "\" /></td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<th><label>Event Time</label></th>";
			echo "<td><input type=\"text\" class=\"text\" name=\"time\" id=\"time\" value=\"" . str_replace('"', "&quot;", $mfgigcal_event->time) . "\" /></td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<th><label>Event Location</label></th>";
			echo "<td>";
			
			$settings = array(
				'media_buttons' => false,
				'wpautop' => false,
				'tinymce' => array(
					'theme_advanced_buttons1' => 'bold,italic,underline,|,undo,redo,|,link,unlink,|,fullscreen',
					'theme_advanced_buttons2' => '',
					'theme_advanced_buttons3' => '',
					'theme_advanced_buttons4' => '',
					'height' => '200',
					'force_br_newlines' => false,
					'force_p_newlines' 	=> true,
					'convert_newlines_to_brs' => false
				),
				'quicktags' => true
			);
			wp_editor($mfgigcal_event->location, "location", $settings);
			echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
			echo "<th><label>Event Details</label></th>";
			echo "<td>";
			
			$settings = array(
				'media_buttons' => true,
				'wpautop' => false,
				'tinymce' => array(
					'height' => '400',
					'force_br_newlines' => false,
					'force_p_newlines' 	=> true,
					'convert_newlines_to_brs' => false
				),
				'quicktags' => true
			);
			wp_editor($mfgigcal_event->details, "details", $settings);
			echo "</td>";
			echo "<p style=\"margin:2px;\"><i>NOTE: In the text editor, use RETURN to start a new paragraph - use SHIFT-RETURN to start a new line.</p></td>";
		echo "</tr>";
		
	echo "</table>";
    
    echo '<p class="submit"><input type="submit" class="button-primary" name="save" value="Save Event" id="submitbutton"> <a href="?page=mf_gig_calendar" class="button-secondary">Cancel</a></p></form>';
   
}

function mfgigcal_list_events() {
	global $wpdb;
	$mfgigcal_table = $wpdb->prefix . "mfgigcal";

	// get the dates
	$today = date("Y-m-d");
	
	$sql = "SELECT * FROM $mfgigcal_table ";
			
	if ($_GET[ytd]) {
		$sql .= "WHERE (end_date >= '$_GET[ytd]-01-01' AND start_date <= '$_GET[ytd]-12-31') ";
	}
	else {
		$sql .= "WHERE end_date >= '$today' ";
	}	
	$sql .= "ORDER BY start_date";
	
	$mfgigcal_events = $wpdb->get_results($sql);
	
	if (!empty($mfgigcal_events)) {
	
		$mfgigcal_data = mfgigcal_CalendarNav();
		$mfgigcal_data .= '<a href="?page=mf_gig_calendar&action=edit" class="button-primary" style="float:right;">New Event</a>';
		$mfgigcal_data .= "<table class=\"widefat\" style=\"margin-top:10px;\">";
		$mfgigcal_data .= "<thead>";
		$mfgigcal_data .= "<tr><th class=\"event_date\">Date</th><th class=\"event_location\">Event</th><th class=\"event_details\" colspan=\"2\">Event Details</th></tr>";
		$mfgigcal_data .= "</thead>";
	
		foreach ($mfgigcal_events as $mfgigcal_event) { 
		
			$mfgigcal_data .= "<tr><td class=\"event_date\">";
			$mfgigcal_data .= mfgigcal_admin_FormatDate($mfgigcal_event->start_date, $mfgigcal_event->end_date) . "<br />";
			$mfgigcal_data .= $mfgigcal_event->time;
			$mfgigcal_data .= "</td>";
			$mfgigcal_data .= "<td class=\"event_location\"><div class=\"event_title\">" . $mfgigcal_event->title . "</div>" . mfgigcal_admin_PrintTruncated(80, $mfgigcal_event->location) . "</td>";
			$mfgigcal_data .= "<td class=\"event_details\">" . mfgigcal_admin_PrintTruncated(100, $mfgigcal_event->details) . "</td>";
			
			$mfgigcal_data .= "<td class=\"buttons\" style=\"white-space:nowrap;\">";
			$mfgigcal_data .= "<a href=\"?page=mf_gig_calendar&id=$mfgigcal_event->id&action=edit\" class=\"button-secondary\" title=\"Edit this event\">Edit</a> ";
			$mfgigcal_data .= "<a href=\"?page=mf_gig_calendar&id=$mfgigcal_event->id&action=copy\" class=\"button-secondary\" title=\"Create a new event based on this event\">Duplicate</a> ";
			$mfgigcal_data .= "<a href=\"#\" onClick=\"mfgigcal_DeleteEvent($mfgigcal_event->id);return false;\" class=\"button-secondary\" title=\"Delete this event\">Delete</a>";
			$mfgigcal_data .= "</td></tr>";

		}
	}
	
	else {	
		$mfgigcal_data = mfgigcal_CalendarNav();
		$mfgigcal_data .= '<a href="?page=mf_gig_calendar&action=edit" class="button-primary" style="float:right;">New Event</a>';
		$mfgigcal_data .= "<table class=\"widefat\" style=\"margin-top:10px;\">";
		$mfgigcal_data .= "<thead>";
		$mfgigcal_data .= "<tr><th class=\"event_date\">Date</th><th class=\"event_location\">Event</th><th class=\"event_details\" colspan=\"2\">Event Details</th></tr>";
		$mfgigcal_data .= "</thead>";
		$mfgigcal_data .=  "<tr>
				<td colspan=\"10\" style=\"text-align:center;\">No events found in this range.</td>
			</tr>";
	}
	
	$mfgigcal_data .= "</table>";
	return $mfgigcal_data;
}

function mfgigcal_admin_FormatDate($start_date, $end_date) {

	$startArray = explode("-", $start_date);
	$start_date = mktime(0,0,0,$startArray[1],$startArray[2],$startArray[0]);
	
	$endArray = explode("-", $end_date);
	$end_date = mktime(0,0,0,$endArray[1],$endArray[2],$endArray[0]);
	
	$mfgigcal_date;
	
	if ($start_date == $end_date) {
		if ($startArray[2] == "00") {
			$start_date = mktime(0,0,0,$startArray[1],15,$startArray[0]);			
			$mfgigcal_date .= '<span style="white-space:nowrap;">' . date("F, Y", $start_date) . "</span>";
			return $mfgigcal_date;
		}
		$mfgigcal_date .= '<span style="white-space:nowrap;">' . date("M j, Y", $start_date) . "</span>";
		return $mfgigcal_date;
	}
	
	if ($startArray[0] == $endArray[0]) {
		if ($startArray[1] == $endArray[1]) {
			$mfgigcal_date .= '<span style="white-space:nowrap;">' . date("M j", $start_date) . "-" . date("j, Y", $end_date) . "</span>";
			return $mfgigcal_date;
		}
		$mfgigcal_date .= '<span style="white-space:nowrap;">' . date("M j", $start_date) . "-" . date("M j, Y", $end_date) . "</span>";
		return $mfgigcal_date;
	
	}
	
	$mfgigcal_date .= '<span style="white-space:nowrap;">' . date("M j, Y", $start_date) . "-" . date("M j, Y", $end_date) . "</span>";
	return $mfgigcal_date;
}


// FEED
include_once('mf_gig_calendar_feed.php');


// WIDGET
include_once('mf_gig_calendar_widget.php');



// UTILITIES 

function remove_wp_magic_quotes() {
	$_GET    = stripslashes_deep($_GET);
	$_POST   = stripslashes_deep($_POST);
	$_COOKIE = stripslashes_deep($_COOKIE);
	$_REQUEST = stripslashes_deep($_REQUEST);
}

function mfgigcal_ExtractDate($date, $format) {
	
	if ($date == "0000-00-00" || !$date) return false;
	
	$dateArray = explode("-", $date);
	$date = mktime(0,0,0,$dateArray[1],$dateArray[2],$dateArray[0]);
	
	// special for day set to "00"
	if ($dateArray[2] == "00") {
		if ($format == "d") {
			return "00";
		}
		else {
			$date = mktime(0,0,0,$dateArray[1],15,$dateArray[0]);
		}
	}
	
	return date($format, $date);
}

function mfgigcal_CalendarNav() {
	global $wpdb;
	$mfgigcal_table = $wpdb->prefix . "mfgigcal";
	
	$sql = "SELECT DISTINCT *
			FROM $mfgigcal_table 
			WHERE start_date != '0000-00-00'
			ORDER BY start_date
			ASC
			LIMIT 1";
	$first_year = $wpdb->get_results($sql, ARRAY_A);
	
	(!empty($first_year)) ? $first_year = mfgigcal_ExtractDate($first_year[0][start_date],'Y') : $first_year = date("Y");
	
	$sql = "SELECT DISTINCT *
			FROM $mfgigcal_table 
			WHERE end_date != '0000-00-00'
			ORDER BY end_date
			DESC
			LIMIT 1";
	$last_year = $wpdb->get_results($sql, ARRAY_A);
	
	(!empty($last_year)) ? $last_year = mfgigcal_ExtractDate($last_year[0][end_date],'Y') : $last_year = date("Y");
	
	if ( is_admin() ) {
		$query_prefix = "?page=mf_gig_calendar&";
	}
	else if (get_option('permalink_structure')) {
		$query_prefix = "?";
	}
	else {
		$existing = "?";
		foreach ($_GET as  $k => $v) {
			if ($k != "ytd" && $k != "event_id") $existing .= $k . "=" . $v . "&";
		}
		$query_prefix = $existing;
	}
	
	if ($_GET[ytd]) {
		$mfgigcal_nav = "<h2>$_GET[ytd] Events</h2>";
		$mfgigcal_nav .= "<div id=\"cal_nav\"><a href=\"" . $query_prefix . "\">Upcoming</a> | Archive: ";
	}
	else if ($_GET[event_id]) {
		$mfgigcal_nav = "<h2>Event Information</h2>";
		$mfgigcal_nav .= "<div id=\"cal_nav\"><a href=\"" . $query_prefix . "\">Upcoming</a> | Archive: ";
	}
	else {
		$mfgigcal_settings = get_option('mfgigcal_settings');
		($mfgigcal_settings['upcoming_title'] == "") ? $upcoming_title = "Upcoming Events" : $upcoming_title = $mfgigcal_settings['upcoming_title'];
		$mfgigcal_nav = "<h2>$upcoming_title</h2>";
		$mfgigcal_nav .= "<div id=\"cal_nav\"><strong>Upcoming</strong> | Archive: ";
	}
	
	
	for ($i=$last_year;$i>=$first_year;$i--) {
		($i == $_GET[ytd]) ? $mfgigcal_nav .= "<strong>$i</strong> " : $mfgigcal_nav .= "<a href=\"" . $query_prefix . "ytd=$i\">$i</a> ";
	}
	$mfgigcal_nav .= "</div>";
	return $mfgigcal_nav;

}

// a function I found on StackOverflow to truncate HTML...

function mfgigcal_admin_PrintTruncated($maxLength, $html) {
    $printedLength = 0;
    $position = 0;
    $tags = array();
	
	$mfgigcal_html;

    while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position)) {
        list($tag, $tagPosition) = $match[0];

        // Print text leading up to the tag.
        $str = substr($html, $position, $tagPosition - $position);
        if ($printedLength + strlen($str) > $maxLength) {
            $mfgigcal_html .= substr($str, 0, $maxLength - $printedLength);
            $printedLength = $maxLength;
            break;
        }

        $mfgigcal_html .= $str;
        $printedLength += strlen($str);

        if ($tag[0] == '&') {
            // Handle the entity.
            $mfgigcal_html .= $tag;
            $printedLength++;
        }
        else {
            // Handle the tag.
            $tagName = $match[1][0];
            if ($tag[1] == '/')
            {
                // This is a closing tag.

                $openingTag = array_pop($tags);
                assert($openingTag == $tagName); // check that tags are properly nested.

                $mfgigcal_html .= $tag;
            }
            else if ($tag[strlen($tag) - 2] == '/') {
                // Self-closing tag.
                $mfgigcal_html .= $tag;
            }
            else {
                // Opening tag.
                $mfgigcal_html .= $tag;
                $tags[] = $tagName;
            }
        }

        // Continue after the tag.
        $position = $tagPosition + strlen($tag);
    }

    // Print any remaining text.
    if ($printedLength < $maxLength && $position < strlen($html)) {
        $mfgigcal_html .= substr($html, $position, $maxLength - $printedLength);
    }
    
    if ($maxLength < strlen($html)) { 
    	$mfgigcal_html .= "...";
    }

    // Close any open tags.
    while (!empty($tags))
        $mfgigcal_html .= "</" . array_pop($tags) . ">";
        
    return $mfgigcal_html;
}










// ACTIVATION - create the database table to store the information

global $mfgigcal_db_version;
$mfgigcal_db_version = "1.1";

function mfgigcal_install() {
	global $wpdb;
	global $mfgigcal_db_version;

	$table_name = $wpdb->prefix . "mfgigcal";
      
	$sql = "CREATE TABLE " . $table_name . " (
		id int(11) NOT NULL AUTO_INCREMENT,
		pub_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		start_date date NOT NULL DEFAULT '0000-00-00',
		end_date date DEFAULT NULL,
		time text,
		title text NOT NULL,
		location text,
		details text,
		PRIMARY KEY  (id)
	);";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
 
	add_option("mfgigcal_db_version", $mfgigcal_db_version);
	
}

register_activation_hook(__FILE__,'mfgigcal_install');


// UPDATE DB 
function mfgigcal_update_db_check() {
    global $mfgigcal_db_version;
    if (get_site_option('mfgigcal_db_version') != $mfgigcal_db_version) {
        mfgigcal_install();
		update_option( "mfgigcal_db_version", $mfgigcal_db_version);
    }
}
add_action('plugins_loaded', 'mfgigcal_update_db_check');

?>