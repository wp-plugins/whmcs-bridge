<?php
//v1.09.15
?>
<table class="optiontable" cellpadding="2" cellspacing="2">

    <?php if ($controlpanelOptions) foreach ($controlpanelOptions as $value) {

        if ($value['type'] == "text" || $value['type'] == "password") { ?>

            <tr align="left">
                <th scope="row"><?php echo $value['name']; ?></th>
                <td><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"
                           type="<?php echo $value['type']; ?>"
                           value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo isset($value['std']) ? $value['std'] : ''; } ?>"
                           size="40"
                        /></td>

            </tr>
            <tr>
                <td colspan="2" style="border-bottom:1px dashed #ccc;"><em><small style="color:#212121"><?php echo $value['desc']; ?></small></em></td>
            </tr>

        <?php }	elseif ($value['type'] == "info") { ?>

            <tr align="left">
                <th scope="row"><?php echo $value['name']; ?></th>
                <td colspan="2"><small><?php echo $value['desc']; ?> </small>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:1px dashed #ccc;">&nbsp;</td>
            </tr>

        <?php } elseif ($value['type'] == "checkbox") { ?>

            <tr align="left">
                <th scope="row"><?php echo $value['name']; ?></th>
                <td><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"
                           type="checkbox"
                           value="checked"
                        <?php if ( get_option( $value['id'] ) != "") { echo " checked"; } ?>
                        /></td>

            </tr>
            <tr>
                <td colspan="2" style="border-bottom:1px dashed #ccc;"><em><small style="color:#212121"><?php echo $value['desc']; ?></small></em></td>
            </tr>


        <?php } elseif ($value['type'] == "textarea") { ?>
            <tr align="left">
                <th scope="row"><?php echo $value['name']; ?></th>
                <td><textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="50"
                    rows="8"/><?php if ( get_option( $value['id'] ) != "") { echo stripslashes (get_option( $value['id'] )); }
                    else { echo isset($value['std']) ? $value['std'] : '';
                    } ?></textarea></td>

            </tr>
            <tr>
                <td colspan="2" style="border-bottom:1px dashed #ccc;"><em><small style="color:#212121"><?php echo $value['desc']; ?></small></em></td>
            </tr>

        <?php } elseif ($value['type'] == "select") { ?>

            <tr align="left">
                <th scope="top"><?php echo $value['name']; ?></th>
                <td><select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                        <?php foreach ($value['options'] as $option) { ?>
                            <option <?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; }?>><?php echo $option; ?></option>
                        <?php } ?>
                    </select></td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:1px dashed #ccc;"><em><small style="color:#212121"><?php echo $value['desc']; ?></small></em></td>
            </tr>

        <?php } elseif ($value['type'] == "selectwithkey") { ?>

            <tr align="left">
                <th scope="top"><?php echo $value['name']; ?></th>
                <td><select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                        <?php foreach ($value['options'] as $key => $option) { ?>
                            <option value="<?php echo $key;?>"
                                <?php
                                if ( get_option( $value['id'] ) == $key) { echo ' selected="selected"'; }
                                elseif ( !get_option($value['id']) && isset($value['std']) && $value['std'] == $key) { echo ' selected="selected"'; }
                                ?>
                                ><?php echo $option; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:1px dashed #ccc;"><em><small style="color:#212121"><?php echo $value['desc']; ?></small></em></td>
            </tr>

        <?php } elseif ($value['type'] == "heading") { ?>

            <tr valign="top">
                <td colspan="2" style="text-align: left;">
                    <h2 style="padding-top:5px;"><?php echo $value['name']; ?></h2>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center" style="border-bottom:1px dashed #ccc;">
                    <h4 style="color: darkblue; margin: 0 0; background: #f7f7f7; padding:4px;"><em><?php echo $value['desc']; ?></em></h4>
                </td>
            </tr>

        <?php }
    } //end foreach
    ?>
</table>