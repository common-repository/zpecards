<?php

/**
 * admin menu page for plugin configuration.
 * uses ecards_settings table for storage
 */

global $wpdb;
$tbl_settings = $wpdb->prefix . "ecards_settings";

/**
 * update ecards_settings table upon Save submit.
 */
if (isset($_POST['save_settings'])) {
    //$embedcode = $_POST['embedcode'];
    $width = $_POST['width'];
    $height = $_POST['height'];
    $feed = $_POST['feed'];
    $ecard = $_POST['ecard'];
    $confirm = $_POST['confirm'];
    $message = $_POST['message'];
    $recipients = $_POST['recipients'];
    $requirefrom = $_POST['requirefrom'];
    $validatefrom = $_POST['validatefrom'];
    $from = $_POST['from'];
    $domain = $_POST['domain'];
    $subject = $_POST['subject'];

    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $width .  "' WHERE name = 'width'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $height .  "' WHERE name = 'height'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $feed .  "' WHERE name = 'feed'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $ecard .  "' WHERE name = 'ecard'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $confirm .  "' WHERE name = 'confirm'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $message .  "' WHERE name = 'message'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $recipients .  "' WHERE name = 'recipients'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $requirefrom .  "' WHERE name = 'requirefrom'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $validatefrom .  "' WHERE name = 'validatefrom'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $from .  "' WHERE name = 'from'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $domain .  "' WHERE name = 'domain'" );
    $results = $wpdb->query( "UPDATE " . $tbl_settings . " SET value='" .  $subject .  "' WHERE name = 'subject'" );
    ?>
<div class="updated"><p>ECards settings saved.</p></div> 
<?php
}

/**
* load stored values from table and fills the form with these values.
*/
$width = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'width'");
$height = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'height'");
$feed = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'feed'");
$ecard = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'ecard'");
$confirm = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'confirm'");
$message = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'message'");
$recipients = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'recipients'");
$requirefrom = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'requirefrom'");
$validatefrom = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'validatefrom'");
$from = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'from'");
$domain = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'domain'");
$subject = $wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'subject'");
?>

<div class="wrap zpwrap">
<h2>ZetaPrints ECards Configuration</h2>

<p>This plugin uses <a href="http://www.zetaprints.com" target="_blank" title="About ZetaPrints">ZetaPrints</a> templates. Templates are processed by <a href="http://www.zetaprints.com/help/dynamic-image-generation-api/" target="_blank" title="Image generation API">ZetaPrints back end</a>. You only need to copy and paste data feeds for <a href="http://zetaprints.com" target="_blank" title="Find card templates">templates you like</a> in the text box below. You can <a href="http://www.zetaprints.com/help/" target="_blank">create and upload</a> your own e-card templates to use with this plugin.</p>
<p><b>Insert shotcode [zp-e-cards] into a post where you want the plugin to appear.</b></p>

<div id="defaultSettings">
<h2>Default Settings</h2>
<form name="manage" method="post">
<fieldset class="options">
<table>
<tr>
    <td class="tdwh">
        <table>
            <tbody>
                <tr>
                    <td>
                        <strong>Width</strong>
                    </td>
                    <td>
                        <input value="<?php echo $width;?>" size="8" id="width" name="width"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Height</strong>
                    </td>
                    <td>
                        <input value="<?php echo $height;?>" size="8" id="height" name="height"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>

    <td style="border: 1px solid rgb(238, 238, 238);">
        <table>
            <tbody>
                <tr>
                    <td style="padding: 5px; text-align: right;">
                        <strong>Feed</strong>
                        <br/>
                        <a> </a>
                    </td>
                    <td style="padding: 5px; text-align: right;">
                        <input value="<?php echo $feed;?>" size="80" id="feed" name="feed"/>
                        <br/>
                        <a href="javascript:findTemplate();">Find Templates</a>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <div id="menuSearch">
                            <div id="search">
                                <div>
                                    <span>
                                        <input type="text" value="" class="text" name="Search" id="Search" title="Find by keywords or reference"/>
                                        <input type="button" onclick="searchTemplate('')" value="Search" class="submits"/>
                                    </span>
                                </div>
                                <div style="float:right;margin:10px">Sort by:
                                    <span>
                                        <input type="radio" checked="checked" value="0" name="SortBy" id="SortBy0"/>
                                        <label for="OrderByDate">date</label>
                                    </span>
                                    <span>
                                        <input type="radio" value="1" name="SortBy" id="SortBy1"/>
                                        <label for="OrderByRank">popularity</label>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td id="templatespane" colspan="2">
        <div id="loading1" style="display: none;">Loading templates ...
            <div id="loading">
            </div>
        </div>
        <div id="contentTemplates">
        </div>
    </td>
</tr>


<tr>
<td><p><strong>Message</strong><p><p><small>Optional. A text message to appear before the image in the email.</small></p></td>
<td><textarea name="message" id="message" rows="6" cols="78"><?php echo $message;?></textarea><em></em></p></td>
</tr>
<tr>
<td><p><strong>Recipients</strong><p><small>Enter predefined list of recipients, one per line<br />as NAME, NAME@EXAMPLE.COM.<br />Only NAME will be visible to users.<br />Leave blank for senders to enter any email address.</small></p></td>
<td><textarea name="recipients" id="recipients" rows="6" cols="78"><?php echo $recipients;?></textarea><em></em></p></td>
</tr>
<tr>
    <td><p><strong>Require From address:</strong></td>
    <?php
    if ($requirefrom == "on") {
        echo '<td><input onChange="requireChange(this.checked)" checked name="requirefrom" type="checkbox" id="requirefrom" /> <em>&nbsp;&nbsp;&nbsp;&nbsp;a sender must enter a valid `From` address</em></p></td>';
    } else {
        echo '<td><input onChange="requireChange(this.checked)" name="requirefrom" type="checkbox" id="requirefrom" /> <em>&nbsp;&nbsp;&nbsp;&nbsp;a sender must enter a valid `From` address</em></p></td>';
    }
    ?>
</tr>
<tr>
    <td><p><strong>Validate From address:</strong></td>
    <?php
    $validatefromcomment = '&nbsp;&nbsp;&nbsp;&nbsp;require a confirmation for user From address?</em></p></td>';
    if ($validatefrom == "on") {
        echo '<td><input checked name="validatefrom" type="checkbox" id="validatefrom" /> <em>' . $validatefromcomment . '</em></p></td>';
    } else {
        echo '<td><input name="validatefrom" type="checkbox" id="validatefrom" /> <em>' . $validatefromcomment . '</em></p></td>';
    }
    ?>
</tr>
<tr>
<td><p><strong>Default From address:</strong></td>
<td><input name="from" type="text" id="from" value="<?php echo $from;?>" size="32" /> <small>used if no email is required for senders and for confirmation emails</small></p></td>
</tr>
<tr>
<td><p><strong>Email subject:</strong></td>
<td><input name="subject" type="text" id="subject" value="<?php echo $subject;?>" size="78" /> <em></em></p></td>
</tr>
<tr>
<td><p><strong>Domain for images:</strong></td>
<td><input name="domain" type="text" id="domain" value="<?php echo $domain;?>" size="50" /> <small>use http://zetaprints.com if in doubt</small></p></td>
</tr>
</table>
</fieldset>
<p><div class="submit">
    <input type="submit" name="save_settings" value="<?php _e('Save Settings', 'save_cache_settings')?>" style="font-weight:bold;" /></div>
</p>
</form>
</div>
</div>
<script type="text/javascript">
    <!--
    /**
     * disable 'validatefrom' checkbox if 'requirefrom' is unchecked.
     * @param bool x
     * @return void
     */
    function requireChange(x) {
        //alert(x);
        if (x) {
            document.getElementById('validatefrom').disabled = false;
        } else {
            document.getElementById('validatefrom').checked = false;
            document.getElementById('validatefrom').disabled = true;
        }
    }
    requireChange('<?php echo $requirefrom; ?>') ;

    //-->
</script>

