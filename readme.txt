=== WHMCS Bridge ===
Contributors: zingiri, globalprogramming
Donate link: http://i-plugins.com/
Tags: WHMCS, hosting, support, billing, integration
Requires at least: 2.1.7
Tested up to: 3.9
Stable tag: 3.1.1

WHMCS Bridge is a plugin that integrates the powerful WHMCS support and billing software with Wordpress.

== Description ==

The WHMCS Bridge plugin integrates your WHMCS support and billing software into Wordpress providing a seamless and consistent user experience to your customers.

Thanks to the theme inheritance feature, you don't need to style your WHMCS installation anymore, the integration ensures that your WHMCS installation looks and feels like your Wordpress site.

Our Pro version additionaly offers:
* **Enhanced visual integration**: using cross-domain messaging for a smoother visual integration
* **Shortcodes**: use Wordpress shortcodes on your pages to integrate WHMCS pages
* **Single sign on**: thanks to the single sign-on feature, your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.
* **Multi-lingual WHMCS support**: fully integrated with qtranslate & WPML
* **IP address resolution 'patch'**: shows your customer's IP address instead of your server's IP address during sign up
* **Choose your WHMCS portal**:fully compatible with the WHMCS v5 'default' template
* **Pretty permalinks**: display links like http://www.mysite.com/clientarea/ rather than http://www.mysite.com/?ccce=clientarea. Also supports knowledgebase, announcement and download links.
 

Thanks to the single sign-on feature (WHMCS Bridge Pro plugin), your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.

== Installation ==

1. Upload the `whmcs-bridge` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the WHMCS Bridge settings menu and configure the plugin options.
4. Install the plugin by clicking the Install button. This will create a new page titled WHMCS through which you can access the client portal of WHMCS.
5. Activate the sidebar widget if you want the sidebar navigation

Please visit the [Forum](http://wordpress.org/support/plugin/whmcs-bridge "Support Forum") for more information and support.

== Frequently Asked Questions ==

Please checkout our Knowledgebase Articles (https://i-plugins.com/whmcs-bridge/knowledgebase/?action=displaycat&catid=1021) for more information and support.

== Screenshots ==

No screenshots here but have a look at [our site](http://i-plugins.com "our site") to see it in action.

== Upgrade Notice ==

Simply go to the Wordpress Settings page for the plugins and click the Upgrade button.

== Changelog ==

= 3.1.1 =
* Assigned ID of "bridge_iframe" to the iframe page to allow for style adjustments of the WHMCS Bridge IFRAME (#bridge_iframe)
* Updated help URL references in readme
* Set SSL version in http.class.php to stop CURL issues on certain installations

= 3.1.0 =
* Changed references from zingiri.com to i-plugins.com

= 3.0.3 =
* Fixed one parameter too many in stristr() function in http.class.php
* Updated help

= 3.0.2 =
* Avoid re-appending variables to URL that are already in the URL in case of redirect
* Fix issue with contacts not being able to login via the bridge (cookies issue)

= 3.0.1 =
* Minor code clean up
* Removed "WHMCS Bottom Page" Widget Area
* Fixed issue with cart/cart.php doubling up
* Verified compatibility with WP 3.7.1

= 3.0.0 =
* Verified compatibility with Wordpress 3.6

= 2.4.3 =
* Verified compatibility with Wordpress 3.5.2

= 2.4.2 =
* Fixed issue with control panel tabs not displaying properly

= 2.4.1 =
* Added more info regarding log page

= 2.4.0 =
* New look for control panel

= 2.3.0 =
* Improved readme.txt and documentation

= 2.2.8 =
* Changed references from zingiri.net to zingiri.com
* Improved debug log

= 2.2.4 =
* Fixed pagination issue when viewing domains, invoices, etc

= 2.2.3 =
* Fixed issue when working in SSL
* Moved message warnings to bridge settings page

= 2.2.2 =
* Fixed issue with tick boxes for loading WHMCS styles and invoice styles being reversed
* Added support for sending customer to invoice on checkout
* Fixed issue with Ideal payment redirecting back to cart instead of to invoice

= 2.2.1 =
* Fixed issue with changing payment gateway when viewing invoice resulting in blank page
* Verified compatibility with WP 3.5.1

= 2.2.0 =
* Added syntax verification of URL

= 2.1.5 =
* Restricted length of debug log to 100 entries

= 2.1.4 =
* Fixed issue with login introduced in version 2.1.2

= 2.1.3 =
* Updated instructions regarding WHMCS bridge page created
* Create default page with title and permalink WHMCS-bridge instead of WHMCS to avoid possible conflict with WHMCS installation residing in whmcs sub-folder of Wordpress website
* Verified compatibility with Wordpress 3.5

= 2.1.2 =
* Added support for images in WHMCS modules folder

= 2.1.1 =
* Added support for Gantry framework
* Added links to documentation and support
* Fixed issue with links when not using WP permalinks (introduced in version 2.1.0)

= 2.1.0 =
* Fixed issue with 'type of sync' option displaying 'None' when option 'Copy WHMCS users to Wordpress' is selected
* Fixed issue with display of URL in case of an error in CURL calls
* Added more info in debug log
* Fixed issue with removing items from cart
* Fixed issue with some URL's containing &#038; instead of &

= 2.0.4 =
* Added new type of option field 'info'

= 2.0.2 =
* Fixed issue with captcha not appearing when not using permalinks in Wordpress
* Fixed issue with some links not being parsed correctly when not using permalinks in Wordpress

= 2.0.1 =
* Fixed parsing issue with pretty permalinks and secure site combination

= 2.0.0 =
* Store remote PHP session id local session to avoid overwrite in case the session id is not returned
* Fixed issue with payment gateways redirects

= 1.9.2 =
* Removed debug output

= 1.9.1 =
* Added support for SSL client area configuration
* Fixed issue with sign on to WHMCS via WP pages

= 1.9.0 =
* Verified compatibility with WHMCS 5.1.2

= 1.8.5 =
* Detect if WHMCS page is SSL and if so use appropriate URL

= 1.8.4 =
* Display notices on WHMCS settings page only

= 1.8.3 =
* Verified compatibility with WP 3.4.1
* Improved integration of WHMCS portal template
* Added possibility to choose to integrate invoice style WHMCS style sheet
* Fixed redirect issue occuring in certain cases

= 1.8.2 =
* Replaced remote image buy_now.png with local version

= 1.8.1 =
* Fixed issue with bridge content appearing on other posts on page

= 1.8.0 =
* Added support for pretty permalinks (WHMCS Bridge Pro)
* Added loading of jQuery UI button to support Comparison cart template

= 1.7.6 =
* Fixed issue with passing GET array variables in curl calls
 
= 1.7.5 =
* Verified working of Boxes, Cart, Slider, Verticalsteps, Web20cart order templates
* Fixed issues with Ajaxcart, Comparison and Modern order templates
* Fixed issue with remove link in cart not working
* Added link to WHMCS page in control panel
* Added load of Wordpress jQuery UI libraries if the Wordpress jQuery library is selected
* Default jQuery library set to Wordpress
* Removed loading of Zingiri news
* Fixed issue with loading jQuery ui
* Added option to disable the footer
* Fixed issue when a customer tries to supply CC info and they click the "where can i find this" link about the CVV code, it takes them to the wrong link

= 1.7.4 =
* Fixed issue with popup window showing email content in v5 Default template not working
* Added redirect to home page when using affiliate links

= 1.7.3 =
* Fixed issue with invoices not being formatted correctly (Default template)
* Fixed issue with product group selector not working in vertical steps cart template when not using permalinks
* Updated http class to latest version

= 1.7.2 =
* Added additional log info

= 1.7.1 =
* Improvided loading performance by only connecting to WHMCS on Wordpress pages and widgets where it is displayed

= 1.7.0 =
* Aligned version number with WHMCS Bridge Pro
* Fixed issue with OS detection

= 1.6.8 =
* Fixed detection of sessions setup
* Perform silent session activation
* Verified compatibility with Wordpress 3.3

= 1.6.7 =
* Removed use of own session name as it causes side effects on other installations

= 1.6.6 =
* Fixed issue with sessions getting lost on certain installations by using own session name

= 1.6.5 =
* Fixed issue with Paypal return URL's not being parsed properly
* Fixed issue with affiliate URL's not working correctly
* Added option to select jQuery library to use

= 1.6.4 =
* Fixed activation issue

= 1.6.3 =
* Fixed issues with undefined constants

= 1.6.1 =
* Load Wordpress jQuery instead of WHMCS
* Fixed issue with double >> breaking the output on some themes
 
= 1.6.0 =
* Fixed issue with formatting of top bar navigation
* Added more hooks to Pro version

= 1.5.1 =
* Fixed issue with captcha not showing as well as ajax issues on themes using nest buffers
* Disable footer if Pro version active

= 1.5.0 =
* Added a new sidebar navigation widget for logged in clients
* Fixed issue with email popup windows displaying directly WHMCS pages
* Removed WHMCS admin user and password fields from control panel (only necessary for single sign-on in Pro plugin)
* Load simple_html_dom extension only when required
* Fixed issue with drop down menu to change product groups not working in cart template 'Verticalsteps'
* Removed base tag
* Removed duplicate title tag
* Removed meta tags from WHMCS html header
* Renamed function cc_footers to avoid duplicates with other plugins

= 1.4.4 =
* Replaced usage of constant __DIR__ (not available in versions below PHP 5.3)

= 1.4.3 =
* Fixed issue with 404 errors when trying to login in case WHMCS installed in a subdirectory of the WP site
* Added widget areas at top and bottom of WHMCS page
* Added verification that PHP sessions are properly configured 
* Upgraded bridgeHttpRequest class to version v1.09.15
* Changed footer text and set default location to bottom of Page rather than site wide

= 1.4.2 =
* Fixed issue with display of Twitter feeds
* Updated Support Us page

= 1.4.1 =
* Turned off error reporting
* Added 'WHCMCS Bridge' to warning and error messages

= 1.4.0 =
* Forced WHMCS template to 'portal' as this is currently the only one working the standard version
* Aligned version number with WHMCS Bridge Pro
* Updated Support Us page
* Fixed issue with 'Continue Shopping' button in Cart template not working
* Fixed issue with invoice downloads links returning to blank page
* Fixed issue with file downloads not working
* Changed method of logging system messages

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