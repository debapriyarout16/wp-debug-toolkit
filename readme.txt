=== WP Debug Toolkit ===
Contributors: debapriyarout16
Tags: debug, site info, plugins, email, SMTP
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 0.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple WordPress plugin to monitor site health, debug plugin conflicts, and test email sending.

== Description ==
WP Debug Toolkit is a lightweight plugin that allows WordPress administrators to:
* View site information (WordPress version, PHP version, active theme, active plugins)
* Check WP_DEBUG status
* Test sending emails via WordPress's wp_mail() function
* Configure optional SMTP settings for real email delivery
* Display warnings for local development environments (MailHog, LocalWP)

== Installation ==
1. Upload the `wp-debug-toolkit` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to "Debug Toolkit" in the admin menu.
4. (Optional) Configure SMTP settings under "SMTP Settings" for sending real emails.

== Frequently Asked Questions ==

= Can I send emails while running locally? =
Emails may be captured by tools like MailHog when running on LocalWP or localhost. For real delivery, configure SMTP settings.

= What PHP version do I need? =
PHP 7.4 or higher.

== Changelog ==

= 0.4 =
* Added custom recipient email field for test emails
* Added local environment warning
* Optional SMTP configuration for real email delivery
* Polished dashboard and active plugins list

= 0.2 =
* Initial version with site info, WP_DEBUG status, and basic email test tool

== Upgrade Notice ==
No special upgrade steps required.
