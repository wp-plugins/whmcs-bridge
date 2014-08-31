<?php
//v2.02.06
if (!function_exists('zing_support_us')) {
    function zing_support_us($shareName,$wpPluginName,$adminLink,$version,$donations=true,$pluginUrl=false) {
        if (!$pluginUrl) $pluginUrl=plugins_url().'/'.$wpPluginName.'/';
        if (get_option('cc_whmcs_bridge_sso_license_key')) $donations=false;
        ?>
        <div style="text-align:center;margin-bottom:15px">
            <a href="http://i-plugins.com" target="_blank"><img width="150px" src="<?php echo $pluginUrl?>images/logo.png" /></a>
        </div>

        <div class="cc-support-us">
            <h3>Get Help!</h3>
            Read the <a href="<?php echo CC_WHMCS_BRIDGE_URL;?>doc/WHMCS-Bridge.pdf" target="_blank"><h4 style="color:blue;">documentation</h4></a><br /><br />
            Check out our <a href="http://wordpress.org/support/plugin/whmcs-bridge" target="_blank"><h4 style="color:blue;">forums</h4></a><br /><br />
            View our <a href="http://i-plugins.com/whmcs/knowledgebase.php?action=displaycat&catid=1021" target="_blank"><h4 style="color:blue;">FAQ</h4></a><br /><br />
            Pro users can open a <a href="http://i-plugins.com/whmcs-bridge/?ccce=submitticket" target="_blank"><h4 style="color:blue;">support ticket</h4></a>
        </div>
        <?php if (!get_option('cc_whmcs_bridge_sso_license_key')) {?>
            <div class="cc-support-us">
                <h3>Discover WHMCS Bridge Pro</h3>
                <h4>Single sign on: </h4><p>thanks to the single sign-on feature, your customers can sign in once on your site and comment on your blog postings, share information with their peers, order hosting plans and pay their bills.</p><br /><br />
                <h4>Multi-lingual WHMCS support: </h4><p>fully integrated with qtranslate.</p><br /><br />
                <h4>IP address resolution 'patch': </h4><p>shows your customer's IP address instead of your server's IP address during sign up.</p><br /><br />
                <h4>Choose your WHMCS portal: </h4><p>fully compatible with the WHMCS v5 'default' template</p><br /><br />
                <h4>Pretty permalinks: </h4><p>display links like http://www.mysite.com/clientarea/ rather than http://www.mysite.com/?ccce=clientarea. Also supports knowledgebase, announcement and download links.</p><br /><br />
                <a href="http://i-plugins.com/whmcs-bridge-wordpress-plugin/" target="_blank"><img src="<?php echo plugins_url().'/whmcs-bridge/images/buy_now.png'?>" /></a>
            </div>
        <?php }?>
        <div class="cc-support-us">
            <h3>Support us by rating our plugin on Wordpress</h3>
            <a href="http://wordpress.org/extend/plugins/<?php echo $wpPluginName;?>" alt="Rate our plugin">
                <img src="<?php echo $pluginUrl?>images/5-stars-125pxw.png" />
            </a>
            <?php
            $option=$wpPluginName.'-support-us';
            if (get_option($option) == '') {
                update_option($option,time());
            } elseif (isset($_REQUEST['support-us']) && ($_REQUEST['support-us'] == 'hide')) {
                update_option($option,time()+7776000);
            } else {
                if ((time() - get_option($option)) > 1209600) { //14 days
                    if ($donations) echo "<div id='zing-warning' style='background-color:red;color:white;font-size:large;margin:20px;padding:10px;'>Looks like you've been using this plugin for quite a while now. Have you thought about showing your appreciation through a small donation?<br /><br /><a href='http://i-plugins.com/donations'><img src='https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif' /></a><br /><br />If you already made a donation, you can <a href='?page=".$adminLink."&support-us=hide'>hide</a> this message.</div>";
                }
            }
            ?>
        </div>
    <?php
    }
}
?>