<?php
/*
Plugin Name: zpEcards
Plugin URI: http://www.zetaprints.com/help/tools/ecards-plugin-for-wordpress/
Description: An embeddable Flash plugin for creating and sending e-cards. Based on ZetaPrints templates. 
Author: rscheink@gmail.com
Version: 2.12
Author URI: mailto:admin@zetaprints.com
*/

define('ZP_FILE_PATH', dirname(__FILE__));
define('ZP_DIR_NAME', basename(ZP_FILE_PATH));

register_activation_hook( __FILE__, 'zp_activate' );
add_action('admin_menu', 'zpmenues');
//add_shortcode('zp-e-cards', 'zpshortcode');
add_action('wp_head', 'add_zp_stylesheet');
add_action('save_post', 'ecards_save_function', 100, 2);
add_filter('the_content' , 'zpshortcode', 1);

/**
 * load CSS file.
 * @return void
 */
function add_zp_stylesheet() {
    $zpStyleUrl = plugins_url('/zpecards/css/zpEcards.css');
    echo '<link type="text/css" rel="stylesheet" href="' . $zpStyleUrl . '" />' . "\n";
}

/**
 * add Admin menues for the plugin.
 * @return void
 */
function zpmenues() {
    add_menu_page('ECards Administration', 'Ecards', 'manage_options', ZP_DIR_NAME . '/zpEcards_admin.php' );
    add_submenu_page(ZP_DIR_NAME.'/zpEcards_admin.php', __('Ecards Configuration'), __('Configuration'), 'manage_options' , ZP_DIR_NAME . '/zpEcards_config.php');
}

require_once('zp_ecard_functions.php');

/**
 * used on the WP filter 'the_content'.
 * if the [zp-e-cards] pattern is found on the $content, filter the content and add the flash object or the generated image, otherwise return the $content.
 * @param string $content
 * @return string
 */
function zpshortcode($content) {
    if ( strpos($content, 'zp-e-cards') ) {
        global $post;
        if ( !isset($_REQUEST['imageid']) && !isset($_REQUEST['confirm']) ) {
            $content = default_render($post->ID, $content);  // show the post content and the embed zetaprints flash object
            return $content ;
        } else if ( isset($_REQUEST['imageid']) ) {
            return image_render($post->ID); // throw the content and show the image and form
        } else {
            return confirm_render($post->ID); // throw the content, send the email and show the 'your email has been sent' text
        }
    } else {
        return $content; // not our post, don't touch it
    }
}

/**
 * on plugin activation create database tables.
 * @param bool $bool
 * @return void
 */
function zp_activate() {
    global $wpdb;
    $tbl_settings = $wpdb->prefix . "ecards_settings";
    /*
    if($wpdb->get_var("show tables like '$tbl_settings'") == $tbl_settings ) {
        $sql = "DROP TABLE $tbl_settings";
        $result = $wpdb->query($sql);
    }
     */
    if($wpdb->get_var("show tables like '$tbl_settings'") != $tbl_settings ) {
        $sql = "CREATE TABLE IF NOT EXISTS $tbl_settings (
        sid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(16) COLLATE utf8_unicode_ci NOT NULL,
        value varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
        PRIMARY KEY (sid)
    );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        $sql = 'INSERT INTO ' . $tbl_settings . ' (sid, name, value) VALUES
            (null, \'width\', \'600\'),
            (null, \'height\', \'350\'),
            (null, \'feed\', \'http://zetaprints.com/RssTemplates.aspx?c=D8506E6F-A4CE-4219-968D-01B1AE6CAB90\'),' .
            '(null, \'message\', \'Replace with email body message\'),
            (null, \'confirmmessage\', \'Check your Inbox or Junk folder for a confirmation email. The card will be emailed as soon as you click on the link the email\'),
            (null, \'recipients\', \'Name#1 Surname, name@example.com' . "\r\n" . 'Name#2 Surname, name2@example.com' . "\r\n" . '\'),
            (null, \'requirefrom\', \'on\'),
            (null, \'validatefrom\', \'\'),
            (null, \'from\', \'Replace with your email address\'),
            (null, \'domain\', \'http://zetaprints.com/\'),
            (null, \'subject\', \'Replace with email subject\'
        );';
        $result = $wpdb->query($sql);
    }


    $tbl_emails = $wpdb->prefix . "ecards_emails";
    if($wpdb->get_var("show tables like '$tbl_emails'") != $tbl_emails ) {
        $sql = "CREATE TABLE IF NOT EXISTS $tbl_emails (
            eid bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            link varchar(64) COLLATE utf8_unicode_ci NOT NULL,
            emailfrom varchar(128) COLLATE utf8_unicode_ci NOT NULL,
            emailto varchar(128) COLLATE utf8_unicode_ci NOT NULL,
            image varchar(128) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (eid)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    $tbl_post_settings = $wpdb->prefix . "ecards_post_settings";
    if($wpdb->get_var("show tables like '$tbl_post_settings'") != $tbl_post_settings ) {
        $sql = "CREATE TABLE IF NOT EXISTS $tbl_post_settings (
            pid bigint(20) unsigned NOT NULL,
            settings varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (pid)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

}

// v2 1.9
require('zpEcards_settings.php');
add_action('admin_menu', 'ecards_settings_box');
add_filter('admin_print_scripts', 'adminHead');
add_action('wp_print_scripts', 'zpaddscript' );
add_action('wp_ajax_ecards_ajax', 'ajaxResponse');

/**
 * load javascript file.
 * @return void
 */
function zpaddscript() {
    //wp_enqueue_script('jquery');
    wp_enqueue_script('zpjs', plugins_url('/zpecards/js/zp.js'), array('jquery'), '1.0');
}


/**
 * load admin CSS file.
 * @return void
 */
function adminHead () {
    echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/zpecards/css/zpAdmin.css" />' . "\n";
}

/**
 * not used.
 */
function ecards_edit_function() {
    echo "ecards_edit: ";
    print_r($_POST);
    echo "ecards_edit end <br />";
}

/**
 * save ecards settings for the current post.
 * @param int $arg1
 * @return void
 */
function ecards_save_function($arg1, $arg2) {
    global $wpdb;
    $pid = $arg1;
    $tbl_post_settings = $wpdb->prefix . "ecards_post_settings";
    $settings['width'] = $_POST['width'];
    $settings['height'] = $_POST['height'];
    $settings['feed'] = $_POST['feed'];
    $settings['message'] = addslashes($_POST['message']);
    $settings['confirmmessage'] = addslashes($_POST['confirmmessage']);
    $settings['recipients'] = $_POST['recipients'];
    $settings['requirefrom'] = $_POST['requirefrom'];
    $settings['validatefrom'] = $_POST['validatefrom'];
    $settings['from'] = $_POST['from'];
    $settings['domain'] = $_POST['domain'];
    $settings['subject'] = addslashes($_POST['subject']);
    $serialsettings = prepare_settings($settings);

    $tmp = $wpdb->get_var("SELECT settings from $tbl_post_settings WHERE pid = '$pid'");
    if ($tmp == null) {
        $query = "INSERT INTO $tbl_post_settings ( pid, settings ) VALUES ('$pid', '$serialsettings')";
        $results = $wpdb->query( $query );
    } else {
        $query = "UPDATE $tbl_post_settings SET settings='$serialsettings' WHERE pid = '$pid'" ;
        $results = $wpdb->query( $query );
    }
}

/**
 * escape $settings after serializing.
 * @param array $settings
 * @return string
 */
function prepare_settings($settings) {
    global $wpdb;
    return $wpdb->escape(serialize($settings));
    return serialize($settings);
}


?>
