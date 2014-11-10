<h2>The Bridge Page</h2>
<p>A WHMCS front end page has been created on your WordPress site.
    This page is the main interaction page between Wordpress and WHMCS.</p>
<p>The full url is:
    <a href="<?php echo cc_whmcs_bridge_home($home,$pid);?>">
        <code>
            <?php echo cc_whmcs_bridge_home($home,$pid);?>
        </code>
    </a>.
    You can edit the page link by editing the page and changing the permalink.</p>
<p style="color:red"><strong>Do not delete this page!</strong></p>

<?php
if (defined("CC_WHMCS_BRIDGE_SSO_PLUGIN") && file_exists(dirname(__FILE__).'/../../whmcs-bridge-sso/pages/help.php')):
    require(dirname(__FILE__).'/../../whmcs-bridge-sso/pages/help.php');
else:
    ?>
    <h2>We Recommend</h2>

    <p><a target="_blank" href="https://billing.stablehost.com/aff.php?aff=3284">
        <img src="http://www.stablehost.com/images/banner40-3.gif" border="0"></a>
    <br/>Use the coupon code <strong>IPLUGINS</strong> and get 40% off your order!</p>

<?php endif ?>