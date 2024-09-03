=== Swell Social Share ===
Contributors: tboggia, andrescruz
Tags: social media, marketing
Requires at least: 8.0
Tested up to: 8.0
Requires PHP: 8
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Swell, Inc. plugin to add social share functionality to all websites.


== Description ==


Creates a `[swellsocial]` shortcode and meta fields in 'post' post type.

Built off of this command: `wp scaffold plugin swell-social-share --plugin_name=Swell_Social_Share --plugin_description=Nothing --plugin_author=tboggia --plugin_author_uri=https://swellinc.co `.

Tests were deleted because they are currently unused. To bring bring them back, run the command above again and copy the missing files.


== Installation ==

1. Upload the `swell-social-share` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `[swellsocial]` in your templates where you want the social share items to show up.

== Frequently Asked Questions ==

= What social media apps are allowed? =
* X
* Facebook
* Linkedin
* Email
* Whatsapp
* Reddit

= What can I customize? =
1. You can override the icon systems through the SwellSocialGetFAIcon($slug) function. It returns the classes added to `<span>` element that contains the aria label for the icon.
2. You can add your own `swell-social-share-template.php` in the `/resources/` folder of your theme.


== Changelog ==
= 0.5 =
* Built the plugin.

== Upgrade Notice ==
None yet
[//]: # (= 0.5 =)
[//]: # (This version fixes a security related bug.  Upgrade immediately.)
