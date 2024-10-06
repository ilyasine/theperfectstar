=== WooCommerce Shipping ===
Contributors: woocommerce, automattic, harriswong, waclawjacek, samnajian, kloon, ferdev, kallehauge, samirthemerchant, dustinparkerwebdev
Tags: woocommerce, shipping, usps, dhl, labels
Requires Plugins: woocommerce
Requires PHP: 7.4
Requires at least: 6.4
Tested up to: 6.6
WC requires at least: 8.9
WC tested up to: 9.1
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A free shipping plugin for US merchants to print discounted shipping labels and compare live label rates directly from your WooCommerce dashboard.

== Description ==

Save time and money with WooCommerce Shipping. This dedicated shipping plugin allows you to print discounted shipping labels and compare live label rates with just a few clicks. There is no need to spend time setting up individual carrier accounts as everything is done directly from your WooCommerce dashboard.

With WooCommerce Shipping, critical services are hosted on Automattic’s best-in-class infrastructure, rather than relying on your store’s hosting. That means your store will be more stable and faster.

To start shipping, simply install this free plugin, create a WooCommerce account, and start saving time and money on your packages.

= Print USPS and DHL shipping labels and get heavily discounted rates =
Ship domestically and internationally right from your WooCommerce dashboard. Print USPS and DHL labels and instantly save up to 90%. All shipments are 100% carbon-neutral. More carriers are coming soon.

= Compare live shipping label rates =
Compare live rates across carriers to make sure you get the best price without guesswork or complex math.

= Split shipments =
Send orders in multiple shipments as products become ready.

= Optimized tracking =
Our built-in Shipment Tracking feature makes it easier for you and your customers to manage tracking numbers by automatically adding tracking IDs to “Order Complete” emails.

== Installation ==

This section describes how to install the plugin and get it working.

1. Install and activate WooCommerce if you haven't already done so
1. Upload the plugin files to the `/wp-content/plugins/woocommerce-shipping` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Connect to your WordPress.com account if you haven't already done so
1. Want to buy shipping labels? First, add your credit card to https://wordpress.com/me/purchases/billing and then print labels for orders right from the Edit Order page

== Frequently Asked Questions ==

= What external services does this plugin rely on? =

This plugin relies on the following external services:

1. WordPress.com connection:
	- Description: The plugin makes requests to our own endpoints at WordPress.com (proxied via https://api.woocommerce.com) to fetch shipping rates, verify addresses, and purchase shipping labels.
	- Website: https://wordpress.com/
	- Terms of Service: https://wordpress.com/tos/
	- Privacy Policy: https://automattic.com/privacy/
2. WooCommerce Usage Tracking:
	- Description: The plugin will send usage statistics, provided the user has opted into WooCommerce Usage Tracking.
	- Script: https://pixel.wp.com/t.gif
	- Terms of Service: https://wordpress.com/tos/
	- Privacy Policy: https://automattic.com/privacy/
3. Sentry.io:
	- Description: The plugin catches critical errors in the user interface and sends a summary of the technical issue to Sentry for debugging purposes.
	- Website: https://sentry.io/
	- Terms of Service: https://sentry.io/terms/
	- Privacy Policy: https://sentry.io/privacy/
4. Sift.com:
	- Description: The plugin utilizes Sift (a fraud prevention and risk management platform) to calculate fraud scores for shipping label purchases made through the WordPress admin interface.
	- Website: https://sift.com/
	- Script: https://cdn.sift.com/s.js
	- Terms of Service: https://sift.com/legal-and-compliance/tos/
	- Privacy Policy: https://sift.com/legal-and-compliance/service-privacy-notice

= Do I need to use WooCommerce Tax or Jetpack? =

There’s no need to have Jetpack or WooCommerce Tax installed on your site — the new experience connects directly through your WordPress.com account for speed and simplicity.

= Why is a WordPress.com account connection required? =

We connect to your WordPress.com account to authenticate your site and user account so we can securely charge the payment method on file for any labels purchased.

= What shipping carriers are currently supported? =

* USPS
* DHL

With more carrier support in the works.

= Can I buy and print shipping labels for US domestic and international packages? =

Yes! You can buy and print USPS shipping labels for domestic destinations and USPS and DHL shipping labels for international destinations. Shipments need to originate from the U.S.

= This works with WooCommerce, right? =

Yep! We follow the L-2 policy, meaning if the latest version of WooCommerce is 8.7, we support back to WooCommerce version 8.5.

= Are there Terms of Service? =

Absolutely! You can read our Terms of Service [here](https://wordpress.com/tos).

== Screenshots ==
1. WooCommerce Shipping label purchase screen.
2. WooCommerce Shipping split shipment screen.
3. WooCommerce Shipping multiple origin address selection.
4. WooCommerce Shipping print label screen.

== Changelog ==

= 1.1.1 - 2024-09-06 =
* Fix    - Get rates button doesn't get active after correcting customs information.
* Fix    - Accessing products from old labels when migrating shipments causes the process to stall.

= 1.1.0 - 2024-09-03 =
* New    - Support for migrating WooCommerce Shipping & Tax labels and settings.
* Add    - Tooltip to explain disabled delete button on default origin address.
* Add    - Necessary endpoints to load the plugin dynamically in WooCommerce.
* Add    - Allow the WooCommerce mobile app to access API.
* Tweak  - Move shipment tracking metabox to upper position.
* Fix    - Browser always ask to exit the settings screen after settings has been saved.
* Fix    - Force shipments with a purchased label to be locked.
* Fix    - Loading plugin version in Loader class.

= 1.0.5 - 2024-08-21 =
* Add   - Show error in Onboarding Connection component.
* Fix   - Conflict with Jetpack connection class.
* Tweak - Change to sender checkbox text on the customs form.
* Tweak - Added new "source" parameter to the /wpcom-connection endpoint.

= 1.0.4 - 2024-08-13 =
* Add   - New Connect component on the shipping settings page.
* Add   - Upload sourcemaps to sentry.
* Add   - Hook into WPCOM Connection dependency list to communicate we share logic with e.g. WooCommerce.
* Tweak - Make composer package versions specific.
* Tweak - Show confirmation banner after accepting Terms of Service.
* Tweak - Hide connect banners if store currency is not supported by WooCommerce Shipping.
* Tweak - Hide connect banners on the WooCommerce Shipping settings page.

= 1.0.3 - 2024-08-02 =
* Fix - Error accessing the continents API endpoint.

= 1.0.2 - 2024-07-30 =
* Tweak - WordPress 6.6 Compatibility.
* Add   - Display the NUX banner on HPOS Order pages.

= 1.0.1 - 2024-06-24 =
* Tweak - Adhering to the plugin review standards.

= 1.0.0 - 2024-04-18 =
* Initial release.

== Upgrade Notice ==

= 1.1.0 =
This release includes an automated migration routine for all your existing WooCommerce Shipping & Tax labels and settings.
