=== MF Gig Calendar ===
Contributors: brewermfnyc
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=matthew@matthewfries.com
Tags: calendar, event, gig, musician, Matthew Fries, brewermfnyc
Requires at least: 3.3
Tested up to: 3.4.1
Stable tag: 0.9.4.1
Plugin URI: http://www.matthewfries.com/mf-gig-calendar
Author URI: http://www.matthewfries.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Short Description ==

A simple event calendar created for musicians but useful for anyone. Supports multi-day events, styled text, links, images, and more.


== Description ==

I'm Matthew Fries (that's where the MF in MF Gig Calendar comes from) and I'm a NYC jazz pianist and part-time web developer. I developed this plugin because I wanted a flexible and easy to use performance calendar for [my own music website](http://www.matthewfries.com). In the process I tried to create something that would work for more than just musicians. I've added a few features since it started:

= Current Features =
* beginning and end dates for multiple-day events
* Wordpress's WYSIWYG editor for the event description so you can include styled text, links, images and other media in your event list
* a duplicate function to make it easier if you have a repeating event that you don't want to re-enter over and over
* an RSS feed
* a widget to list a few upcoming events in the sidebar
* an archive to view past events by year

The calendar can be placed in any **PAGE** or **POST** on your Wordpress site - even in more than one place. Just include the following short code where you want the calendar to appear: 

‘[mfgigcal]’

Requires WordPress 3.3 or newer because of the changes to the Wordpress WYSIWYG editor. Personally tested up to 3.4.1.

= Future Plans = 
* a template in the options so you can control the output of the calendar info so it appears exactly the way you want?
* (Other ideas? Please tell me!)

Want to keep in touch? Here are a few options...

* [www.matthewfries.com](http://www.matthewfries.com)
* [facebook](http://www.facebook.com/matthewfriesmusic)
* [twitter](http://www.twitter.com/mfjazz)

As a general rule I've tried to keep this as simple and flexible as possible. If you're a musician and you want really fancy - ticketing info, mapping, tour grouping, and all the bells and whistles you can stand - you should check out *"Gigs Calendar"* by Dan Coulter over at [blogsforbands.com](http://www.blogsforbands.com). It's a great plugin that I've also used quite a few times on other sites that has all kinds of cool features specific to musicians and fans. Really, it's great.


== Installation ==

1. Upload the folder 'mf-gig-calendar' to the '/wp-content/plugins/' directory,
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Insert the shortcode '[mfgigcal]' wherever you want the calendar to appear.
4. Start entering events under the 'Event Calendar' menu!


== Frequently Asked Questions ==

= How do I get a calendar to show up in a Page or Post on my site? =
Easy! Use the MF Gig Calendar shortcode: [mfgigcal]

Just put that code on any Page or Post in the spot where you want the calendar to appear. Wordpress does the rest and any settings are optional.

= How do I use styled text and images in my event descriptions? =
MF Gig Calendar uses the built-in Wordpress WYSIWYG editor. It's exactly the same process you use when creating Posts or Pages.

= Does MF Gig Calendar put a bunch of junk in my Wordpress database? =
One database table is installed to store your events. That's it!

= What happens if I add more than one event on a particular day? =
The output is only sorted by date, so if you enter multiple events on the same day they will be displayed on most servers in the order you created them. I don't sort by time because I don't require you to enter a time for your event and you can use whatever term you want (2pm, afternoon, all day, 20.30, etc).

= Can I change the look of the calendar on my site? =
A very basic stylesheet is included in the file '/wp-content/plugins/mf-gig-calendar/mf_gig_calendar.css'. The stylesheet contains an outline/example of the basic output to help you style the calendar as you like. Got your own interesting layout you'd like to share for me to include in a future update? Let me know!

= Why do you ask me to enter a URL for my calendar in the settings? Shouldn't that just happen automatically? =
The MF Gig Calendar widget and the RSS feed use the URL you enter in Settings to link to the calendar on your site. You can put the calendar in any Page or Post on your site - even in more than one place - so this URL is the place you want people to go to see what's going on. It's completely optional. The plugin will work fine without it.

= Why doesn't MF Gig Calendar...(insert your cool idea here)...? =
I tried to keep the basics of this plugin pretty simple so that it would be useful to a broader range of people. If you have a suggestion for a really useful feature please let me know and I'll consider adding it in a future update!

== Screenshots ==
1. Admin screen - list of events

2. Admin screen - event editor page

3. Front end - example of website display

4. Front end - example of website display

5. Front end - example of website display

== Changelog ==

**Version 0.9.4.1**

*Really just a re-upload of version 0.9.4. Still trying to get my brain around subversion.

**Version 0.9.4**

* Updated the calendar layout slightly to get the event description to appear next to the date instead of below it by default in some themes.
* Updated the Settings and About pages to (hopefully) be easier to understand.
* A few other minor fixes.

**Version 0.9.3**

* Fixed a bug where the links to individual events didn't work unless permalinks was turned on.

**Version 0.9.2**

* Fixed the installer!! When I added the options page in version 0.9 it caused a problem with setting up the plugin for the first time. There was a PHP timeout you may have been experiencing. This should be fixed now! Sorry...

**Version 0.9.1**

* Fixed a typo and a couple broken links in the documentation.

**Version 0.9**

* I skipped straight to version 0.9 because I feel like I'm *almost* 100% happy with this thing and I'm ready to put it in the Wordpress repository. There were just so many enhancements in this round that I couldn't just go up one tenth. Plus I really like the number 0.9 - it kind of looks like an emoticon for a person sitting at a computer screen (use your imagination).
* Separate RSS feed.
* Options page to customize output.
* About page with installation instructions.
* Upgrades to the sidebar widget.
* Replaced table-based display with more stylable output layout.

**Version 0.4**

* Adds display of individual events from the calendar. I can share individual events on Facebook now.

**Version 0.3**

* Now requires Wordpress 3.3+ and the fully functional WYSIWYG editor works great thanks to this Wordpress upgrade!

**Version 0.2**

* Added widget displaying a few upcoming events in the sidebar. That's nice.

**Version 0.1**

* First working version. Just mucking around and learning how the heck to create a Wordpress plugin! :-P


== Upgrade Notice == 

Version 0.9.4 makes a few improvements in calendar display and updates some of the documentation.