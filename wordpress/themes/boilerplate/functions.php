<?php

/*****************
  WordPress
*****************/

// Keep jQuery from being loaded twice in the admin.

if( !is_admin()){
	wp_deregister_script('jquery');
}

// Hide the wp-admin bar

show_admin_bar( false );

// Declare a default site width

if ( ! isset( $content_width ) ) {
	$content_width = 474;
}

// Enable certain HTML5 WordPress features

add_theme_support( 'html5', 'post-thumbnails', array(
	'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
) );

// Enable post formats and declare which ones are available

add_theme_support( 'post-formats', array(
	'gallery'
) );

/*****************
  WordPress Admin Customization
*****************/

// Login with Email Address

function admin_custom_styles() {
  ?>
  	<style type="text/css">
  	
		.wrap { margin: 50px 83px; }
		#customize-current-theme-link { display:none; }

  	</style>
  <?php
}
add_action('admin_head', 'admin_custom_styles');

// Remove the slide down tabs in the admin

function remove_contextual_help($old_help, $screen_id, $screen){
    $screen->remove_help_tabs();
    return $old_help;
}
add_filter( 'contextual_help', 'remove_contextual_help', 999, 3 );

// Remove update notifications in the admin footer

function my_footer_shh() {
    
    remove_filter( 'update_footer', 'core_update_footer' ); 

}
add_action( 'admin_menu', 'my_footer_shh' ); 

// Add custom 'I heart Enliven' text in the admin footer

function change_footer_text() {
    return 'Made with <span style="color:#ff3e3e;"><i class="dashicons dashicons-heart" style="font-size: 18px;vertical-align: middle;"></i></span> in St. Louis by <a target="_blank" href="http://www.enlivenhq.com">Enliven</a>';
}
add_filter( 'admin_footer_text', 'change_footer_text' );

remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

// Disable WPAdmin dashboard widgets

function disable_dashboard_widgets() {  

  	remove_action('welcome_panel', 'wp_welcome_panel');  
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
    remove_meta_box( 'dashboard_quick_draft', 'dashboard', 'core');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // Right Now
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Incoming Links
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // Plugins
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // Recent Drafts
    remove_meta_box('dashboard_primary', 'dashboard', 'side');   // WordPress blog
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');   // Other WordPress News

}
add_action('wp_dashboard_setup', 'disable_dashboard_widgets');

// Remove certain items from the WPAdmin bar

function remove_admin_bar_items() {

	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('site-name');
	$wp_admin_bar->remove_menu('my-account-with-avatar');
	$wp_admin_bar->remove_menu('comments');
	$wp_admin_bar->remove_menu('new-content');
	$wp_admin_bar->remove_menu('customize');


}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_items' );

// Remove certain items from the WPAdmin menu

function remove_admin_menus() {

	   remove_menu_page( 'edit-comments.php' );
       //remove_menu_page( 'themes.php' );
   	   remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
   	   remove_menu_page( 'tools.php' );
   	   remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag');
   	   remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=language');
   	   remove_submenu_page( 'edit-content', 'edit-content');
   	
}
add_action( 'admin_init', 'remove_admin_menus' );

// Disable access to the WordPress customizer

function no_customize() {
	wp_die( sprintf( __( 'This theme does not support the WordPress Theme Customizer.' ) ) . sprintf( '<br /><a href="javascript:history.go(-1);">Go back</a>' ) );
}
add_action( 'load-customize.php', 'no_customize' );



/*****************
  Media
*****************/

// Login with Email Address

function relativePathForUploads($fileInfos)
{

	$path = get_bloginfo('siteurl');
	$fileInfos['url'] = str_replace($path,'',$fileInfos['url']);

	return $fileInfos;
}
add_filter('wp_handle_upload', 'relativePathForUploads');

/*****************
  Helpers
*****************/

// Get the postID by the slug name

function get_ID_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

/*****************
  Authentication
*****************/

// Login with Email Address

function login_with_email_address($username) {
        $user = get_user_by('email',$username);
        if(!empty($user->user_login))
                $username = $user->user_login;
        return $username;
}
add_action('wp_authenticate','login_with_email_address');

// Change the copy on the login page

function change_username_wps_text($text){
       if(in_array($GLOBALS['pagenow'], array('wp-login.php'))){
         if ($text == 'Username'){$text = 'Username / Email';}
            }
                return $text;
         }
add_filter( 'gettext', 'change_username_wps_text' );


/*****************
  Authentication
*****************/

// Facebook Share Link

function facebook_share_link() {
	$url = get_permalink();
	$title = get_the_title();
	$summary = get_the_content();
	$encodedUrl = urlencode($url);
	$encodedTitle = urlencode($title);
	$encodedSummary = urlencode($summary);

	return 	"http://www.facebook.com/sharer.php?s=100" .
			"&amp;p[url]=$encodedUrl" .
			"&amp;p[title]=$encodedTitle" .
			"&amp;p[summary]=$encodedSummary";
	return get_permalink();
	return 'http://facebook.com';
}

// Twitter Share Link

function twitter_share_link() {
	$url = get_permalink();
	$message = get_the_title();
	$maxMessageLength = 140 - strlen($url);
	if (strlen($message) > $maxMessageLength) $message = substr($message, 0, $maxMessageLength - 3) . '...';
	$tweet = urlencode("$message $url");
	
	return "http://twitter.com/home?status=$tweet";
}

// LinkedIn Share Link

function linkedin_share_link() {
	$url = get_permalink();
	$title = get_the_title();
	$summary = get_the_content();
	$encodedUrl = urlencode($url);
	$encodedTitle = urlencode($title);
	$encodedSummary = urlencode($summary);
	$encodedSource = 'CEO Club Vietnam';
	
	return 	"http://www.linkedin.com/shareArticle?mini=true" .
			"&amp;url=$encodedUrl" .
			"&amp;title=$encodedTitle" .
			"&amp;summary=$encodedSummary" .
			"&amp;source=$encodedSource";
}



/*****************
  Frontend
*****************/

// Add bootstrap classes to pagination buttons.

function posts_link_attributes() {
    return 'class="button button-red button-filled button-small"';
}
add_filter('next_posts_link_attributes', 'posts_link_attributes');
add_filter('previous_posts_link_attributes', 'posts_link_attributes');

