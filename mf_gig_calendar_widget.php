<?php

/*

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

class GigCalendarWidget extends WP_Widget {

	function GigCalendarWidget() {
		parent::WP_Widget( false, $name = 'MF Gig Calendar', array( 'description' => 'MF Gig Calendar Widget displays a list of upcoming events.' ) );
	}
	 
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$events_to_display = apply_filters( 'events_to_display', $instance['events_to_display'] );
		$calendar_link = apply_filters( 'calendar_link', $instance['calendar_link'] );
		$link_text = apply_filters( 'link_text', $instance['link_text'] );
			
		global $wpdb;
		$mfgigcal_table = $wpdb->prefix . "mfgigcal";
		// get the dates
		$today = date("Y-m-d");
		$sql = "SELECT * FROM $mfgigcal_table WHERE end_date >= '$today' ORDER BY start_date ASC LIMIT " . $events_to_display;
		$mfgigcal_events = $wpdb->get_results($sql);
		
		// here are the events
		
		if (!empty($mfgigcal_events)) {
		
			$mfgigcal_settings = get_option('mfgigcal_settings');
		
			echo $before_widget;
			
			if ($title) {
				echo $before_title . $title . $after_title;
			}
			echo "\n<ul>";
			foreach ($mfgigcal_events as $mfgigcal_event) {
				echo "\n<li>";
				$startArray = explode("-", $mfgigcal_event->start_date);
				$start_date = mktime(0,0,0,$startArray[1],$startArray[2],$startArray[0]);
				
				$endArray = explode("-", $mfgigcal_event->end_date);
				$end_date = mktime(0,0,0,$endArray[1],$endArray[2],$endArray[0]);
				
				if ($start_date == $end_date) { // single date format
					echo "<div class=\"date\">" . date("D j M", $start_date) . " <span class=\"time\">$mfgigcal_event->time</span></div>";
				}
				else { // multi-day format
					echo "<div class=\"date\">" . date("j M", $start_date) . "&ndash;" . date("j M", $end_date) . " <span class=\"time\">$mfgigcal_event->time</span></div>";
				}
			
				echo "\n<h4>$mfgigcal_event->title</h4>";
				echo $mfgigcal_event->location;
				echo "</li>";
			}
		
			echo "\n</ul>\n\n";
			if ($calendar_link != "" && $mfgigcal_settings['calendar_url']) {
				echo "<p><a href=\"" . $mfgigcal_settings['calendar_url'] . "\" class=\"calendar_url\">$link_text</a></p>";
			}
			echo $after_widget;
		}
		
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
	 
	function form( $instance ) {
		$title = esc_attr( $instance['title'] );
		$events_to_display = esc_attr( $instance['events_to_display']);
		$calendar_link = esc_attr( $instance['calendar_link']);
		$link_text = esc_attr( $instance['link_text']);
		if ($link_text == "") $link_text = "View My Event Calendar";
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget Title:
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		<p>
			<label>How many events to display? 
			<input style="width:30px;" id="<?php echo $this->get_field_id( 'events_to_display' ); ?>" name="<?php echo $this->get_field_name( 'events_to_display' ); ?>" type="text" value="<?php echo $events_to_display; ?>" />
			</label>
		</p>
		<p>
			<label><input class="link_detail_switch" id="<?php echo $this->get_field_id( 'calendar_link' ); ?>" name="<?php echo $this->get_field_name( 'calendar_link' ); ?>" type="checkbox" value="1" <?php checked( '1', $calendar_link ); ?> />
			Add a link to the URL for my event calendar that I specified in my <a href="admin.php?page=mf_gig_calendar_settings">MF Gig Calendar Settings page</a>.
			</label>
		</p>
		<div class="link_details">
		<p>
			<label>Link Text:
			<input class="widefat" id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" type="text" value="<?php echo $link_text; ?>" />
			</label>
		</p>
		<p>
			Note: If the "View My Calendar" link isn't displaying in your widget please make sure you have entered a URL for your calendar on the <a href="admin.php?page=mf_gig_calendar_settings">settings page</a>.
		</p>
		</div>
		
		<?php
	
	}
}
 
add_action( 'widgets_init', 'GigCalendarWidgetInit' );
function GigCalendarWidgetInit() {
	register_widget( 'GigCalendarWidget' );
}

?>