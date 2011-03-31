=== WHMCS Bridge ===
Contributors: EBO
Donate link: http://www.choppedcode.com/
Tags: WHMCS, hosting, support, billing, integration
Requires at least: 2.1.7
Tested up to: 3.1
Stable tag: 1.0.3

WHMCS Bridge is a plugin that integrates the powerfull WHMCS support and billing software with Wordpress.
== Description ==

The WHMCS Bridge plugin integrates your WHMCS support and billing software into Wordpress providing a seamless and consistent user experience to your customers.

Thanks to the theme inheritance feature, you don't need to style your WHMCS installation anymore, the integration ensures that your WHMCS installation looks and feels like your Wordpress site. 

Thanks to the single sign-on feature (WHMCS Bridge SSO plugin), your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.

== Installation ==

1. Upload the `whmcs-bridge` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Ensure the directory /whmcs-bridge/cache is writable (chmod 777)
4. Go to the WHMCS Bridge settings menu and configure the plugin options.
5. Install the plugin by clicking the Install button. This will create a new page titled WHMCS through which you can access the client portal of WHMCS.
6. Activate the sidebar widget if you want the sidebar navigation

Please visit the [ChoppedCode](http://forums.choppedcode.com "ChoppedCode Support Forum") for more information and support.

== Frequently Asked Questions ==

Please visit the [ChoppedCode](http://forums.choppedcode.com/forumdisplay.php?fid=56 "ChoppedCode Support Forum") for more information and support.

== Screenshots ==

None available ... yet.

== Upgrade Notice ==

Simply go to the Wordpress Settings page for the plugins and click the Upgrade button.

== Changelog ==

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