=== WHMCS Bridge ===
Contributors: zingiri
Donate link: http://www.zingiri.net/donations
Tags: WHMCS, hosting, support, billing, integration
Requires at least: 2.1.7
Tested up to: 3.2.1
Stable tag: 1.3.2


WHMCS Bridge is a plugin that integrates the powerfull WHMCS support and billing software with Wordpress.
== Description ==

The WHMCS Bridge plugin integrates your WHMCS support and billing software into Wordpress providing a seamless and consistent user experience to your customers.

Thanks to the theme inheritance feature, you don't need to style your WHMCS installation anymore, the integration ensures that your WHMCS installation looks and feels like your Wordpress site. 

Thanks to the single sign-on feature (WHMCS Bridge SSO plugin), your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.

== Installation ==

1. Upload the `whmcs-bridge` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the WHMCS Bridge settings menu and configure the plugin options.
4. Install the plugin by clicking the Install button. This will create a new page titled WHMCS through which you can access the client portal of WHMCS.
5. Activate the sidebar widget if you want the sidebar navigation

Please visit the [Zingiri](http://forums.zingiri.net "Zingiri Support Forum") for more information and support.

== Frequently Asked Questions ==

Please visit the [Zingiri](http://forums.zingiri.net/forumdisplay.php?fid=56 "Zingiri Support Forum") for more information and support.

== Screenshots ==

No screenshots here but have a look at [our site](http://www.zingiri.net/portal/ "our site") to see it in action.

== Upgrade Notice ==

Simply go to the Wordpress Settings page for the plugins and click the Upgrade button.

== Changelog ==

= 1.3.2 =
* Added support for when WHMCS is in Maintenance Mode, the maintenance message defined in WHMCS is displayed on the WP site.
* Fixed issue when in the shopping cart, the Continue Shopping button does not work.
* Fixed issue with Order Summary not being displayed for certain cart templates.
* Fixed issue with display of knowledge base articles when entering a new support ticket.
* Updated Support Us page and donations link

= 1.3.1 =
* Added trailing slash to pretty URL's
* Updated http class to version 0.8
* Removed passing variable 'ccce' to WHMCS cURL call as it's not required
* Fixed issue with registration page redirecting to WHMCS site
* Fixed issue with navigation links at top of page redirecting to WHMCS site
* Use remote logo instead of local one

= 1.3.0 =
* Removed superfluous variables from cURL connection strings as they caused some issues on some installations

= 1.2.1 =
* Changed name of cookies storage
* Fixed issue with head tag being included twice
* Fixed issue invoice not being displayed at checkout
* Don't load WHMCS invoicestyle.css stylesheet

= 1.2.0 =
* Fixed issue with captchas not working when submitting tickets
* Fixed issues with ajax cart templates not working
* Fixed issue with wrong definition of CC_WHMCS_BRIDGE_URL
* Fixed issue with delete of cc_mybb_version option
* Improved handling of cURL connection to WHMCS, avoiding use of cache files
* Disabling the loading of WHMCS styles now only disables the load of the main stylesheet, the cart templates are still being loaded
* Changed handling of redirects within cURL calls

= 1.1.0 =
* Split main sidebar widget in 5 separate widgets
* Added option to disable loading the WHMCS jQuery library if the Wordpress already uses this library
* Added support for permalinks
* Added option to add custom styles in control panel
* Revamped settings page
* Added option to disable loading the default WHMCS styles (to avoid conflicts with Wordpress)
* Added top menu widget
* Added welcome box widget
* Icon (gif) import (embedded as local)
* Fixed issue with checkout button taking you to cart.php
* Removed <p align="center" class="cartheading">Browse Products &amp; Services</p>
* Replace html head base

= 1.0.7 =
* Removed trailing debugging display

= 1.0.6 =
* Added support for SSL (https)

= 1.0.5 =
* Fixed issue with ticket submission
* Fixed conflict issue with ccForum plugin

= 1.0.4 =
* Fixed parsing issue with xviewticketx

= 1.0.3 =
* Auto format WHMCS URL removing trailing slash if necessary
* Added a warning that SSL isn't supported in case the WHMCS URL is a https URL
* Added debugging option, generating a debug log if activated

= 1.0.2 =
* Fixed parsing error causing the ordering form not to work
* Fixed 404 error occuring on certain installations
* Verified compatibility with Wordpress 3.1

= 1.0.1 =
* Added link to rate the plugin on Wordpress
* Added SSO license key field in control panel

= 1.0.0 =
* Hide password in plugin control panel
* Fixed div break in control panel
* Added Twitter, Facebook and donation links
* Verified compatibility with Wordpress v3.0.5

= 0.9.4 =
* Fixed issue with page titles being duplicated
* Fixed login redirect issue

= 0.9.3 =
* Replaced get_settings (deprecated) by get_option
* Replaced register_sidebar_widget (deprecated) by wp_register_sidebar_widget
* Fixed issue with tests on $_REQUEST variables throwing warnings

= 0.9.2 =
* Removed obsolete files
* Fixed issue with install throwing an error

= 0.9.1 =
* Corrected plugin description
* Protected cache directory
* Protected plugin directory
* Cleaned up code

= 0.9.0 =
* First release