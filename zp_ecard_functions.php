<?php

/**
 * generates the flash embed code using parameters retrieved from database.
 * @param int $pid
 * @return string
 */
function default_render($pid, $content) {
	global $wpdb;
	$tbl_settings = $wpdb->prefix . "ecards_settings";
	$tbl_post_settings = $wpdb->prefix . "ecards_post_settings";	
	$serialsettings = $wpdb->get_var("SELECT settings FROM $tbl_post_settings WHERE pid = '$pid'");

	// if there are no post settings, use default values
	if ($serialsettings == null) {
		$width = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'width'");
		$height = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'height'");
		$feed = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'feed'");
	} else {
		$settings = unserialize($serialsettings);
		$width = $settings['width'];
		$height = $settings['height'];
		$feed = $settings['feed'];
	}

	if ( strpos(get_permalink(), '?') ) {
		$imageidlink = get_permalink() . "%26imageid=";
	} else {
		$imageidlink = get_permalink() . "?imageid=";
	}

	$embedcode = '<div id="zpePlayerOuter"><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="' . $width . '" height="' . $height . '" id="e-cards-plugin" align="middle">
    <param name="allowScriptAccess" value="sameDomain" />
    <param name="allowFullScreen" value="false" />
    <param name="movie" value="http://www.zetaprints.com/flash/e-cards-plugin.swf" />
    <param name="quality" value="high" />
    <param name="bgcolor" value="#666666" />
    <param name="FlashVars" value="params=' . $feed . '&email=' . $imageidlink . '" />
    <embed src="http://www.zetaprints.com/flash/e-cards-plugin.swf" quality="high" bgcolor="#666666" width="' . $width . '" height="' . $height . '" name="e-cards-plugin" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="params=' . $feed . '&email=' . $imageidlink . '" /></object><div  id="zpePlayerLink">Powered by ZetaPrints <a href="http://www.zetaprints.com/">Image Generator</a></div></div>';
	
    $content = str_replace( '[zp-e-cards]', $embedcode, $content);
    return $content;
}

/**
 * generates the form html code and displays image generated on the flash app, using parameters retrieved from database.
 * @param int $pid
 * @return string
 */
function image_render($pid) {
	$loc = get_permalink();
	$imageid = $_REQUEST['imageid']; 
	
	global $wpdb;
	$tbl_settings = $wpdb->prefix . "ecards_settings";
	$tbl_emails = $wpdb->prefix . "ecards_emails";
	$tbl_post_settings = $wpdb->prefix . "ecards_post_settings";	
	$serialsettings = $wpdb->get_var("SELECT settings FROM $tbl_post_settings WHERE pid = '$pid'");
	
	// if there are no post settings, use default values
	if ($serialsettings == null) {
		$width = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'width'");
		$height = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'height'");
		$feed = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'feed'");
		$domain = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'domain'");
		$requirefrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'requirefrom'");
		$validatefrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'validatefrom'");
		$subject = stripslashes(stripslashes($wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'subject'")));
		$message = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'message'")));
		$confirmmessage = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'confirmmessage'")));
		$defaultfrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'from'");
		$recipients = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'recipients'");
	} else {
		$settings = unserialize($serialsettings);
		$width = $settings['width'];
		$height = $settings['height'];
		$feed = $settings['feed'];
		$message = stripslashes(stripslashes($settings['message']));
		$confirmmessage = stripslashes(stripslashes($settings['confirmmessage']));
		$recipients = $settings['recipients'];
		$requirefrom = $settings['requirefrom'];
		$validatefrom = $settings['validatefrom'];
		$defaultfrom = $settings['from'];
		$domain = $settings['domain'];
		$subject = stripslashes(stripslashes($settings['subject']));
	}
	
	
	$url = $domain . 'preview/' . $imageid ;
	$thePage = getFile( $url, false ) ; // get the image from zetaprints server
	
	// save the image under the preview folder
	$fp = fopen('wp-content/plugins/zpecards/preview/' . $imageid, 'w');
	fwrite($fp, $thePage);
	fclose($fp);
	
	ob_start();
?>
<div class="zpm15"><center><h2>Your ECard</h2></center></div>
<div class="zpm15">   
<form action="javascript:zpSend();" name="sendEmailForm" id="sendEmailForm" method="post">
	<fieldset>
	<table>
		<tr>
			<td class="w30"><strong><label for="emailto">To:</label></strong></td>
			<td class="w70">
<?php
	if (strpos($recipients, ',') !== false) {
?>
			<select name="emailto" id="emailto" class="selector" onFocus="resetEmailto()" >
			<option value="">Select recipient ....</option>
<?php
		$recip = explode("\n" , $recipients);
		foreach($recip as $rec1) {
			$rec = explode("," , $rec1);
				echo '<option value="' . trim($rec[1]) . '" >' . trim($rec[0]) . '</option>';
		}
?>
			</select>
			</td>
		</tr>
<?php
	} else {
?>
			<input type="text" name="emailto" size=32 id="emailto" value="<?php echo $emailto; ?>" onFocus="resetEmailto()" >
			</td>
		</tr>
<?php
	}
?>
		<tr>
			<td></td>
			<td><span id="emailtochk"></span></td>
		</tr>
		<tr></tr>
<?php
	if ($requirefrom == "on") {
?>
		<tr>
			<td><strong><label for="emailfrom">From (email):</label></strong></td>
			<td><input name="emailfrom" size=32 id="emailfrom" value="<?php echo $emailfrom; ?>" onFocus="resetEmailfrom()" ></td>
		</tr>
<?php
	}
?>
		<tr>
			<td></td>
			<td><span id="emailfromchk"></span></td>
		</tr>
		<tr>
			<td></td>
			<td><span class="zpp10 zpbold zp3em" id="ajaxbox"/></span></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" id="sendemail" name="sendemail" value="Send Email" class="zpbold" /></td>
		</tr>
</table>
</fieldset>
	</form>
</div>
<div class="zpimgdiv">
<?php
echo '<center><a href="' . $domain . 'preview/' . $imageid . '"><img src="' . $domain . 'preview/' . $imageid . '"></a></center>';
?>
</div>
<script type="text/javascript">
/**
 * on form submit, validates user input data, generating error messages and displaying them through jQuery functions.
 * if no errors, send the email through an AJAX call to ajaxResponse function.
 * @return void
 */
function zpSend() {
	//alert (patt);
	if (document.sendEmailForm) {
		emailtoerror = false;
		emailfromerror = false;
		emailto = document.sendEmailForm.emailto.value;
		if ( emailto == ''  || !emailto.match(expr) ) {
			emailtoerror = true;
			jQuery('#emailto').addClass('zprb');
			jQuery('#emailtochk').text('This is not a valid email address');
			jQuery('#emailtochk').fadeIn();
		} 
		if (document.sendEmailForm.emailfrom) {
			emailfrom = document.sendEmailForm.emailfrom.value;
			if ( emailfrom == ''  || !emailfrom.match(expr) ) {
				emailfromerror = true;
				jQuery('#emailfrom').addClass('zprb');
				jQuery('#emailfromchk').text('This is not a valid email address');
				jQuery('#emailfromchk').fadeIn();
			} 
		} 
		//alert(emailtoerror || emailfromerror ) ;
		if (!(emailtoerror || emailfromerror )) {
			jQuery('#sendemail').attr('disabled','1');
			jQuery('#ajaxbox').removeClass('zptxtgreen');
			jQuery('#ajaxbox').addClass('zptxtred');
			jQuery('#ajaxbox').text('Sending the Email ...');
			jQuery('#ajaxbox').fadeIn(400, function () {jQuery('#ajaxbox').css('display','block')});
			//jQuery('#sendemail').fadeOut();
			//jQuery('#newcard').fadeOut();
			siteurl = "<?php echo get_bloginfo('wpurl'); ?>";
			plurl = "<?php echo plugins_url('zpecards/zpsend.php'); ?>";
			imageid = "<?php echo $imageid; ?>";
			loc = "<?php echo $loc; ?>";
			pid = "<?php global $post; echo $post->ID; ?>";
			//emailto = 'rscheink@gmail.com';
			emailto = document.sendEmailForm.emailto.value;
			//emailfrom = 'x@x.com';
			if (document.sendEmailForm.emailfrom) {
				emailfrom = document.sendEmailForm.emailfrom.value;
			} else {
				emailfrom = 'x';
			}
			jQuery.post(siteurl+"/wp-admin/admin-ajax.php", {action:"ecards_ajax", 'loc':loc , 'pid':pid , 'imageid':imageid , 'emailto':emailto , 'emailfrom':emailfrom },
 function(jsdata)
{
	if (jsdata == '10') {
		jQuery('#ajaxbox').text('The email has been sent');
		jQuery('#ajaxbox').addClass('zptxtgreen');
		jQuery('#ajaxbox').removeClass('zptxtred');
		jQuery('#ajaxbox').removeClass('zptxtyellow');
		jQuery('#ajaxbox').fadeIn();
	} else if (jsdata == '20') {
		jQuery('#ajaxbox').text('Check your Inbox or Junk folder for a confirmation email. The card will be emailed as soon as you click on the link inside the email.');
		jQuery('#ajaxbox').addClass('zptxtyellow');
		jQuery('#ajaxbox').removeClass('zptxtred');
		jQuery('#ajaxbox').removeClass('zptxtgreen');
		jQuery('#ajaxbox').fadeIn();
	} else {
		jQuery('#ajaxbox').text('An error has occurred. Please try again in a few minutes');
		jQuery('#ajaxbox').addClass('zptxtred');
		jQuery('#ajaxbox').removeClass('zptxtgreen');
		jQuery('#ajaxbox').removeClass('zptxtyellow');
		jQuery('#ajaxbox').fadeIn();
	}
	jQuery('#sendemail').removeAttr('disabled');
} );
		}
	}
}
/**
 * clear 'emailto' input on focus.
 * @return void
 */
function resetEmailto() {
	if (document.sendEmailForm.emailto) {
		jQuery('#ajaxbox').fadeOut();
		jQuery('#emailto').removeClass('zprb');
		jQuery('#emailtochk').fadeOut();
	}
}
/**
 * clear 'emailfrom' input on focus.
 * @return void
 */
function resetEmailfrom() {
	if (document.sendEmailForm.emailfrom) {
		jQuery('#ajaxbox').fadeOut();
		jQuery('#emailfrom').removeClass('zprb');
		jQuery('#emailfromchk').fadeOut();
	}
}
</script>
<?php  
	$x=  ob_get_clean();
	return $x;

} 

/**
 * sends the email using parameters retrieved from database.
 * @param int $pid
 * @return string
 */
function confirm_render($pid) {
	global $wpdb;
	$tbl_settings = $wpdb->prefix . "ecards_settings";
	$tbl_emails = $wpdb->prefix . "ecards_emails";

	$tbl_post_settings = $wpdb->prefix . "ecards_post_settings";	
	$serialsettings = $wpdb->get_var("SELECT settings from $tbl_post_settings WHERE pid = '$pid'");
	
	// if there are no post settings, use default values
	if ($serialsettings == null) {
		$width = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'width'");
		$height = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'height'");
		$feed = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'feed'");
		$domain = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'domain'");
		$requirefrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'requirefrom'");
		$validatefrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'validatefrom'");
		$subject = stripslashes(stripslashes($wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'subject'")));
		$message = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'message'")));
		$confirmmessage = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'confirmmessage'")));
		$defaultfrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'from'");
		$recipients = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'recipients'");
	} else {
		$settings = unserialize($serialsettings);
		$width = $settings['width'];
		$height = $settings['height'];
		$feed = $settings['feed'];
		$message = stripslashes(stripslashes($settings['message']));
		$confirmmessage = stripslashes(stripslashes($settings['confirmmessage']));
		$recipients = $settings['recipients'];
		$requirefrom = $settings['requirefrom'];
		$validatefrom = $settings['validatefrom'];
		$defaultfrom = $settings['from'];
		$domain = $settings['domain'];
		$subject = stripslashes(stripslashes($settings['subject']));
	}

	// send the email with parameters retrieved from database and the image as attachment
	$id = $_REQUEST['confirm'];
	$emailfrom = $wpdb->get_var("SELECT emailfrom from $tbl_emails WHERE link = '$id'");
	$emailto = $wpdb->get_var("SELECT emailto from $tbl_emails WHERE link = '$id'");
	$imageid = $wpdb->get_var("SELECT image from $tbl_emails WHERE link = '$id'");

	$file = 'wp-content/plugins/zpecards/preview/' . $imageid ;
	require('class.phpmailer.php');
	$mail = new PHPMailer();
	$mail->From = $emailfrom;
	$mail->FromName = $emailfrom;
	$mail->AddAddress($emailto);
	$mail->AddReplyTo($emailfrom);
	$mail->WordWrap = 50; 
	$mail->AddAttachment($file, 'e-card.jpg', 'base64', 'image/jpeg');
	$mail->Subject = $subject;
	$mail->Body = $message ;
	$mail->Send();

	return '<center><h2>An email with your ECard has been sent.</h2></center>';
} 

/**
 * get the image from the zetaprint server.
 * @param string $url
 * @param bool $showHeader
 * @return mixed
 */
function getFile( $url, $showHeader ) {
$ch = curl_init ($url);
curl_setopt($ch, CURLOPT_POST, false);
if ($showHeader === true) {
	curl_setopt($ch, CURLOPT_HEADER, 1);
} else {
	curl_setopt($ch, CURLOPT_HEADER, 0);
}

curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
return curl_exec ($ch);
}

/**
 * AJAX call to get the image and send the email.
 * @return string
 */
function ajaxResponse() {
	global $wpdb;
	$pid = $_POST['pid']; 
	$tbl_settings = $wpdb->prefix . "ecards_settings";
	$tbl_emails = $wpdb->prefix . "ecards_emails";
	$tbl_post_settings = $wpdb->prefix . "ecards_post_settings";	
	$serialsettings = $wpdb->get_var("SELECT settings FROM $tbl_post_settings WHERE pid = '$pid'");

	$loc = $_POST['loc']; 
	$imageid = $_POST['imageid']; 
	
	if ($serialsettings == null) {
		$width = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'width'");
		$height = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'height'");
		$feed = $wpdb->get_var( "SELECT value FROM $tbl_settings WHERE name = 'feed'");
		$domain = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'domain'");
		$requirefrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'requirefrom'");
		$validatefrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'validatefrom'");
		$subject = stripslashes(stripslashes($wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'subject'")));
		$message = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'message'")));
		$confirmmessage = stripslashes(stripslashes($wpdb->get_var("SELECT value from $tbl_settings WHERE name = 'confirmmessage'")));
		$defaultfrom = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'from'");
		$recipients = $wpdb->get_var("SELECT value FROM $tbl_settings WHERE name = 'recipients'");
	} else {
		$settings = unserialize($serialsettings);
		$width = $settings['width'];
		$height = $settings['height'];
		$feed = $settings['feed'];
		$message = stripslashes(stripslashes($settings['message']));
		$confirmmessage = stripslashes(stripslashes($settings['confirmmessage']));
		$recipients = $settings['recipients'];
		$requirefrom = $settings['requirefrom'];
		$validatefrom = $settings['validatefrom'];
		$defaultfrom = $settings['from'];
		$domain = $settings['domain'];
		$subject = stripslashes(stripslashes($settings['subject']));
	}
	
	
	$url = $domain . 'preview/' . $imageid ;
	$thePage = getFile( $url, false ) ; // get the image from zetaprints server
	
	// save the image under the preview folder
	$fp = fopen( dirname(__FILE__) .  '/preview/' . $imageid, 'w');
	fwrite($fp, $thePage);
	fclose($fp);
	
	// send the email with parameters retrieved from database 
		$emailto = $_POST['emailto'];
		
		if ($requirefrom == "on") {
			$emailfrom = $_POST['emailfrom'];
		} else {
			$emailfrom = $defaultfrom;
		}
		
		if ( is_email($emailto) === false ) { 
			$emailtoerror = true;
		} else {
			$emailtoerror = false;
		}
		if ( is_email($emailfrom) === false ) { 
			$emailfromerror = true;
		} else {
			$emailfromerror = false;
		}
		
		if ($emailtoerror === false && $emailfromerror === false) {
				
			require('class.phpmailer.php');
			$mail = new PHPMailer();

			// if validate from is required send an email with the link to confirm to sender
			// otherwise  send an email with the attachment to addressee
			if ($validatefrom == "on") {
				$mail->From = $defaultfrom;
				$mail->FromName = $defaultfrom;
				$mail->AddReplyTo($defaultfrom);

				$md5time = md5(time());
				$results = $wpdb->query( "INSERT INTO " . $tbl_emails . " ( link, emailfrom, emailto, image ) VALUES ( '" .  $md5time . "', '" . $emailfrom . "', '" . $emailto .  "', '" . $imageid . "')" );
	
				if ( strpos($loc, '?') ) {
					$message = "Did you send an e-card through our website? We'll send it as soon as you confirm your email address by clicking on this link.\r\n\r\n" . $loc . "&confirm=" . $md5time;
				} else {
					$message = "Did you send an e-card through our website? We'll send it as soon as you confirm your email address by clicking on this link.\r\n\r\n" . $loc . "?confirm=" . $md5time;
				}
				$jsdata = '2';

			} else {
				$mail->From = $emailfrom;
				$mail->FromName = $emailfrom;
				$mail->AddReplyTo($emailfrom);

				$file = dirname(__FILE__) .  '/preview/' . $imageid ;
				$mail->AddAttachment($file, 'e-card.jpg', 'base64', 'image/jpeg');
				$jsdata = '1';
			}

			if ($validatefrom == "on") {
				$mail->AddAddress($emailfrom);
			} else {
				$mail->AddAddress($emailto);
			}
			$mail->WordWrap = 50; 
			$mail->Subject = $subject;
			$mail->Body = $message;
			$res = $mail->Send();
			if ($res != 'true') {
			        $result['status'] = "fail";
				$jsdata = '';
			} else {
			        $result['status'] = "ok";
			}
		
		}
	echo $jsdata;
}

function getTemplate($post, $url, $showHeader) {
    $ch = curl_init ($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    if ($showHeader === true) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    } else {
        curl_setopt($ch, CURLOPT_HEADER, 0);
    }

    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
    return curl_exec ($ch);
}

