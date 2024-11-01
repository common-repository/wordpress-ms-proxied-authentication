=== WordPress MS Proxied Authentication ===
Contributors: ryanlee
Donate link: http://voccs.com/donate/
Tags: mu, cookies, login, widget, proxy, integration
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 1.0

Allows remote login of a proxied MS site.

== Description ==

This plugin is for WordPress MS.  WPMS Proxied Authentication allows a site administrator to incorporate the last mile of proxying their site from a different domain by adding to the cookies sent back for a successful login to take the proxying domain and path into account.

If you intend to serve several different domains and possibly subdirectories of those domains from one WPMS installation beyond a simple DNS pointer, you will need to use proxying, a commonly used server configuration that can mask the origin of a site from the browser.  Out of the box, WPMS will not accept this setup for security purposes; login authentication is achieved through cookies that are specifically set for the domain WPMS runs on itself, and so logging in to the proxied site will always fail.  WPMS Proxied Authentication adds cookies for the proxied site.

For example, if you have WPMS installed at `http://wpms.example.org/` with a subdomain at `http://subs.wpms.example.org/`, but you want to display and interact with that site at `http://subs.example.net/site/`, WPMS Proxied Authentication will help you do so.

The plugin was developed with WordPress MS set up for subdomains, not subdirectories, but it should work with either mode.  Feedback is welcome.

== Installation ==

1. Upload this directory or just the `cookie-monster.php` file to the `/wp-content/plugins/` directory
1. Network Activate the plugin through the Super Admin 'Plugins' menu in WordPress
1. For any given site that needs this feature added, go to Proxied Authentication under Settings and add the Proxied Domain and Proxied Path settings.  The Proxied Domain can start with `.` in case it's accessed from alternate subdomains (`www.` or not), the Proxied Path can be left blank if it's at the site root (`/`)
1. Under the site's advanced settings, set the `siteurl` and `home` URLs for a site to the proxying URL, the one the world will access the site from; do not change the Domain or Path values, they are required for internal functioning
1. Given your proxying site is set up, try logging in from the proxying URL

== Changelog ==

= 1.0 =
* Initial release.
