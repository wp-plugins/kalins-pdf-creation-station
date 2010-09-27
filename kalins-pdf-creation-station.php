<?php
/*
Plugin Name: Kalin's PDF Creation Station
Version: 0.9.1
Plugin URI: http://kalinbooks.com/pdf-creation-station/
Description: Build highly customizable PDF documents from any combination of pages and posts, or add a link to any page to download a PDF of that post.
Author: Kalin Ringkvist
Author URI: http://kalinbooks.com/

Kalin's PDF Creation station by Kalin Ringkvist (email: kalin@kalinflash.com)

This is Kalin Ringkvist's first WordPress plugin and first real PHP project so I can't make any guarantees as to the security or reliability of this plugin.

Thanks to Marcos Rezende's Blog as PDF and Aleksander Stacherski's AS-PDF plugins which provided a great starting point.

License:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

define("KALINS_PDF_ADMIN_OPTIONS_NAME", "kalins_pdf_admin_options");
define("KALINS_PDF_TOOL_OPTIONS_NAME", "kalins_pdf_tool_options");

$uploads = wp_upload_dir();
$pdfDirBase = $uploads['basedir'].'/kalins-pdf/';

function kalins_pdf_admin_page() {//load php that builds our admin page
	require_once( WP_PLUGIN_DIR . '/kalins-pdf-creation-station/kalins_pdf_admin_page.php');
}

function kalins_pdf_tool_page() {//load php that builds our tool page
	require_once( WP_PLUGIN_DIR . '/kalins-pdf-creation-station/kalins_pdf_tool_page.php');
}

function kalins_pdf_admin_init(){
	
	//echo "pdf_admin_init";
	
	//creation tool ajax connections
	add_action('wp_ajax_kalins_pdf_tool_create', 'kalins_pdf_tool_create');
	add_action('wp_ajax_kalins_pdf_tool_delete', 'kalins_pdf_tool_delete');
	add_action('wp_ajax_kalins_pdf_tool_defaults', 'kalins_pdf_tool_defaults');
	
	
	//single page admin ajax connections
	add_action('wp_ajax_kalins_pdf_reset_admin_defaults', 'kalins_pdf_reset_admin_defaults');//kalins_pdf_admin_save
	add_action('wp_ajax_kalins_pdf_admin_save', 'kalins_pdf_admin_save');
	
	add_action('contextual_help', 'kalins_pdf_contextual_help', 10, 2);
	
	register_deactivation_hook( __FILE__, 'kalins_pdf_cleanup' );
	
	wp_register_style('kalinPDFStyle', WP_PLUGIN_URL . '/kalins-pdf-creation-station/kalins_pdf_styles.css');
	
	
	//--------------you may remove these three lines (comment them out) if you are using hard-coded PDF links in your theme. This will make your admin panels run slightly more efficiently.--------------
	add_action('save_post', 'kalinsPDF_save_postdata');
	add_meta_box( 'kalinsPDF_sectionid', __( "PDF Creation Station", 'kalinsPDF_textdomain' ), 'kalinsPDF_inner_custom_box', 'post', 'side' );
    add_meta_box( 'kalinsPDF_sectionid', __( "PDF Creation Station", 'kalinsPDF_textdomain' ), 'kalinsPDF_inner_custom_box', 'page', 'side' );
	//--------------------------------
}

function kalins_pdf_configure_pages() {
	
	$mypage = add_submenu_page('options-general.php', 'Kalins PDF Creation Station', 'PDF Creation Station', 'manage_options', __FILE__, 'kalins_pdf_admin_page');
	
	$mytool = add_submenu_page('tools.php', 'Kalins PDF Creation Station', 'PDF Creation Station', 'manage_options', __FILE__, 'kalins_pdf_tool_page');
	
	add_action( "admin_print_scripts-$mypage", 'kalins_pdf_admin_head' );
	add_action('admin_print_styles-' . $mypage, 'kalins_pdf_admin_styles');
	
	add_action( "admin_print_scripts-$mytool", 'kalins_pdf_admin_head' );
	add_action('admin_print_styles-' . $mytool, 'kalins_pdf_admin_styles');
}

function kalins_pdf_admin_head() {
	//echo "My plugin admin head";
	wp_enqueue_script("jquery");
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-ui-dialog");
}

function kalins_pdf_admin_styles(){//not sure why this didn't work if called from pdf_admin_head
	wp_enqueue_style('kalinPDFStyle');
}

function kalinsPDF_inner_custom_box($post) {//creates the box that goes on the post/page edit page
  	// show nonce for verification and post box label
  	echo '<input type="hidden" name="kalinsPDF_noncename" id="kalinsPDF_noncename" value="' .wp_create_nonce( plugin_basename(__FILE__) ) . '" />Create PDF of this page? <br />';
	
	$meta = json_decode(get_post_meta($post->ID, "kalinsPDFMeta", true));//grab meta from this particular post
	
	if($meta){//if that meta exists, set $showLink
		$showLink = $meta->showLink;
	}else{//if there is no meta for this page/post yet, grab the default
		//$adminOptions = kalins_pdf_get_admin_options();
		//$showLink = $adminOptions['showLink'];
		$showLink = "default";
	}
	
	switch($showLink){//KLUDGE - show radio buttons depending on which one is selected (there should be an easier way than repeating all that HTML - I mean, what if I had like 15 different options?)
		case "top":
			echo '<p><input type="radio" name="kalinsPDFLink" value="top" id="opt_top" checked /> Link at top of page<br /><input type="radio" name="kalinsPDFLink" value="bottom" id="opt_bottom" /> Link at bottom of page<br /><input type="radio" name="kalinsPDFLink" value="none" id="opt_none" /> Do not generate PDF<br /><input type="radio" name="kalinsPDFLink" value="default" id="opt_default" /> Use default</p>';
			break;
		case "bottom":
			echo '<p><input type="radio" name="kalinsPDFLink" value="top" id="opt_top" /> Link at top of page<br /><input type="radio" name="kalinsPDFLink" value="bottom" id="opt_bottom" checked /> Link at bottom of page<br /><input type="radio" name="kalinsPDFLink" value="none" id="opt_none" /> Do not generate PDF<br /><input type="radio" name="kalinsPDFLink" value="default" id="opt_default" /> Use default</p>';
			break;
		case "none":
			echo '<p><input type="radio" name="kalinsPDFLink" value="top" id="opt_top" /> Link at top of page<br /><input type="radio" name="kalinsPDFLink" value="bottom" id="opt_bottom" /> Link at bottom of page<br /><input type="radio" name="kalinsPDFLink" value="none" id="opt_none" checked /> Do not generate PDF<br /><input type="radio" name="kalinsPDFLink" value="default" id="opt_default" /> Use default</p>';
			break;
		case "default":
			echo '<p><input type="radio" name="kalinsPDFLink" value="top" id="opt_top" /> Link at top of page<br /><input type="radio" name="kalinsPDFLink" value="bottom" id="opt_bottom" /> Link at bottom of page<br /><input type="radio" name="kalinsPDFLink" value="none" id="opt_none" /> Do not generate PDF<br /><input type="radio" name="kalinsPDFLink" value="default" id="opt_default" checked /> Use default</p>';
			break;
	}
}

function kalinsPDF_save_postdata( $post_id ) {
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return $post_id;
	}
	
	if(!$_POST){//this check is here because the verify_nonce was throwing errors when error reporting was turned on - not sure why the 'DOING_AUTOSAVE' thing didn't catch it
		return;
	}
	
	// verify this came from our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['kalinsPDF_noncename'], plugin_basename(__FILE__) )) {
		return $post_id;
	}
	
	//$pdfDir = WP_PLUGIN_DIR .'/kalins-pdf-creation-station/pdf/singles/';
	//$uploads = wp_upload_dir();
	//$pdfDir = $uploads['basedir'].'/kalins-pdf/';
	//$pdfDir = $pdfDirBase .'singles/';
	
	$uploads = wp_upload_dir();
	$pdfDir = $uploads['basedir'].'/kalins-pdf/singles/';
	
	$fileName = $post_id .'.pdf';
	
	if(file_exists($pdfDir .$fileName)){//if the pdf file for this page already exists,
		unlink($pdfDir .$fileName);//delete it cuz it's now out of date since we're saving new post content
	}
	
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ){
		  return $post_id;
		}
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) ){
			return $post_id;
		}
	}

  	// OK, we're authenticated: we need to find and save the data
	$meta = new stdClass();
	$meta->showLink = $_POST['kalinsPDFLink'];
	
	update_post_meta($post_id, 'kalinsPDFMeta', json_encode($meta));
	
	//echo "updating post meta";
}

function kalinsPDF_content_filter($content){
	if(!is_single() && !is_page()){//if we're not on a single page/post we don't need to do anything else
		return $content;
	}
	
	global $wp_query;
	$post = $wp_query->post;
	$meta = json_decode(get_post_meta($post->ID, "kalinsPDFMeta", true));
	$adminOptions = kalins_pdf_get_admin_options();
	
	if($meta){
		$showLink = $meta->showLink;
	}
	
	if(!$meta || $showLink == "default"){
		if(strlen($content) > $adminOptions['charCount']){//if this post is longer than the minimum character count
			$showLink = $adminOptions['showLink'];
		}else{
			return $content;//if it's not long enough, just quit
		}
	}
	
	if($showLink == "none"){//if we don't want a link or if we're not on a single page/post we don't need to do anything else
		return $content;
	}
	
	if($post->post_type == "page"){
		$postID = "pg_" .$post->ID;
	}else{
		$postID = "po_" .$post->ID;
	}
	
	//-------remove these three lines if you aren't using shortcodes in the link and you want to conserve processing power
	$adminOptions["beforeLink"] = kalins_pdf_page_shortcode_replace($adminOptions["beforeLink"], $post);
	$adminOptions["linkText"] = kalins_pdf_page_shortcode_replace($adminOptions["linkText"], $post);
	$adminOptions["afterLink"] = kalins_pdf_page_shortcode_replace($adminOptions["afterLink"], $post);
    //-------
	
    $strHtml = $adminOptions["beforeLink"] .'<a href="' . get_bloginfo('wpurl') . '/wp-content/plugins/kalins-pdf-creation-station/kalins_pdf_create.php?singlepost=' .$postID .'" target="_blank" >' .$adminOptions["linkText"] .'</a>' .$adminOptions["afterLink"];
    
	switch($showLink){//return the content with the link attached above or below
		case "top":
			return $strHtml .$content;
		case "bottom":
			return $content .$strHtml;
	}
}

function kalins_pdf_contextual_help($text, $screen) {
	if (strcmp($screen, 'settings_page_kalins-pdf-creation-station/kalins-pdf-creation-station') == 0 ) {//if we're on settings page, add setting help and return
		require_once( WP_PLUGIN_DIR . '/kalins-pdf-creation-station/kalins_pdf_admin_help.php');
		return;
	}
	
	if (strcmp($screen, 'tools_page_kalins-pdf-creation-station/kalins-pdf-creation-station') == 0 ) {//otherwise show the tool help page (the two help files are very similar but have a few important differences)
		require_once( WP_PLUGIN_DIR . '/kalins-pdf-creation-station/kalins_pdf_tool_help.php');
	}
}

//--------begin ajax calls---------

function kalins_pdf_reset_admin_defaults(){//called when user clicks the reset button on the single admin page
	check_ajax_referer( "kalins_pdf_admin_reset" );
	$kalinsPDFAdminOptions = kalins_pdf_getAdminSettings();
	update_option(KALINS_PDF_ADMIN_OPTIONS_NAME, $kalinsPDFAdminOptions);
	
	//$pdfDir = WP_PLUGIN_DIR . '/kalins-pdf-creation-station/pdf/singles/';//we delete all cached single pdf files since the defaults have probably changed
	//$pdfDir = $pdfDirBase .'singles/';
	$uploads = wp_upload_dir();
	$pdfDir = $uploads['basedir'].'/kalins-pdf/singles/';
	
	
	if ($handle = opendir($pdfDir)) {//open pdf directory
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && substr($file, stripos($file, ".")+1, 3) == "pdf") {//loop to find all relevant files 
				unlink($pdfDir .$file);//and delete them
			}
		}
		closedir($handle);
	}
	echo json_encode($kalinsPDFAdminOptions);
}

function kalins_pdf_admin_save(){
	
	check_ajax_referer( "kalins_pdf_admin_save" );
	
	$outputVar = new stdClass();
	
	$kalinsPDFAdminOptions = array();//collect our passed in values so we can save them for next time
	
	$kalinsPDFAdminOptions["beforePage"] = stripslashes($_POST['beforePage']);
	$kalinsPDFAdminOptions["beforePost"] = stripslashes($_POST['beforePost']);
	$kalinsPDFAdminOptions["afterPage"] = stripslashes($_POST['afterPage']);
	$kalinsPDFAdminOptions["afterPost"] = stripslashes($_POST['afterPost']);
	$kalinsPDFAdminOptions["titlePage"] = stripslashes($_POST['titlePage']);
	$kalinsPDFAdminOptions["finalPage"] = stripslashes($_POST['finalPage']);
	$kalinsPDFAdminOptions["headerTitle"] = stripslashes($_POST['headerTitle']);
	$kalinsPDFAdminOptions["headerSub"] = stripslashes($_POST['headerSub']);
	
	$kalinsPDFAdminOptions['linkText'] = stripslashes($_POST['linkText']);
	$kalinsPDFAdminOptions['beforeLink'] = stripslashes($_POST['beforeLink']);
	$kalinsPDFAdminOptions['afterLink'] = stripslashes($_POST['afterLink']);
	
	$kalinsPDFAdminOptions["fontSize"] = (int) $_POST['fontSize'];
	$kalinsPDFAdminOptions['charCount'] = (int) stripslashes($_POST['charCount']);
	
	$kalinsPDFAdminOptions['showLink'] = stripslashes($_POST['showLink']);
	$kalinsPDFAdminOptions["includeImages"] = stripslashes($_POST['includeImages']);
	//$kalinsPDFAdminOptions["includeTables"] = stripslashes($_POST['includeTables']);
	
	$kalinsPDFAdminOptions["doCleanup"] = stripslashes($_POST['doCleanup']);
	
	
	update_option(KALINS_PDF_ADMIN_OPTIONS_NAME, $kalinsPDFAdminOptions);//save options to database
	
	//$pdfDir = WP_PLUGIN_DIR . '/kalins-pdf-creation-station/pdf/singles/';
	//$pdfDir = $pdfDirBase .'singles/';
	$uploads = wp_upload_dir();
	$pdfDir = $uploads['basedir'].'/kalins-pdf/singles/';
	
	if ($handle = opendir($pdfDir)) {//open pdf directory
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && substr($file, stripos($file, ".")+1, 3) == "pdf") {//loop to find all relevant files 
				unlink($pdfDir .$file);//and delete them
			}
		}
		closedir($handle);
		$outputVar->status = "success";
	}else{
		$outputVar->status = "fail";
	}
	
	echo json_encode($outputVar);
}

function kalins_pdf_tool_defaults(){//called when user clicks the reset button
	check_ajax_referer( "kalins_pdf_tool_reset" );
	$kalinsPDFAdminOptions = kalins_pdf_getDefaultOptions();
	update_option(KALINS_PDF_TOOL_OPTIONS_NAME, $kalinsPDFAdminOptions);
	echo json_encode($kalinsPDFAdminOptions);
}

function kalins_pdf_tool_create(){//called from create button
	check_ajax_referer( "kalins_pdf_tool_create" );
	require_once(WP_PLUGIN_DIR .'/kalins-pdf-creation-station/kalins_pdf_create.php');
}

function kalins_pdf_tool_delete(){//called from either the "Delete All" button or the individual delete buttons
	
	//echo $pdfDirBase ."echoing";
	
	//echo "WTF!!";
	
	check_ajax_referer( "kalins_pdf_tool_delete" );
	$outputVar = new stdClass();
	$fileName = $_POST["filename"];
	
	//echo json_encode($outputVar);
	
	
	//$pdfDir = WP_PLUGIN_DIR . '/kalins-pdf-creation-station/pdf/';
	//$pdfDir = $pdfDirBase;
	
	$uploads = wp_upload_dir();
	$pdfDir = $uploads['basedir'].'/kalins-pdf/';
	
	//echo $pdfDir .$fileName;
	
	if($fileName == "all"){//if we're deleting all of them
		if ($handle = opendir($pdfDir)) {//open pdf directory
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && substr($file, stripos($file, ".")+1, 3) == "pdf") {//loop to find all relevant files 
					unlink($pdfDir .$file);//and delete them
				}
			}
			closedir($handle);
			$outputVar->status = "success";
		}else{
			$outputVar->status = "fail";
		}
	}else{
		$fileName = $pdfDir .$fileName;
		if(file_exists($fileName)){
			unlink($fileName);//delete only the file passed in
			$outputVar->status = "success";
		}else{
			$outputVar->status = "fail";
		}
	}
	echo json_encode($outputVar);
}

//--------end ajax calls---------

function kalins_pdf_get_tool_options() {
	$kalinsPDFAdminOptions = kalins_pdf_getDefaultOptions();
	
	$devOptions = get_option(KALINS_PDF_TOOL_OPTIONS_NAME);

	if (!empty($devOptions)) {
		foreach ($devOptions as $key => $option){
			$kalinsPDFAdminOptions[$key] = $option;
		}
	}

	update_option(KALINS_PDF_TOOL_OPTIONS_NAME, $kalinsPDFAdminOptions);

	return $kalinsPDFAdminOptions;
}

function kalins_pdf_get_admin_options() {
	$kalinsPDFAdminOptions = kalins_pdf_getAdminSettings();
	
	$devOptions = get_option(KALINS_PDF_ADMIN_OPTIONS_NAME);

	if (!empty($devOptions)) {
		foreach ($devOptions as $key => $option){
			$kalinsPDFAdminOptions[$key] = $option;
		}
	}

	update_option(KALINS_PDF_ADMIN_OPTIONS_NAME, $kalinsPDFAdminOptions);

	return $kalinsPDFAdminOptions;
}

function kalins_pdf_getAdminSettings(){//simply returns all our default option values
	$kalinsPDFAdminOptions = array('headerTitle' => '[post_title] - [post_date]',
		'headerSub' => 'by [post_author] - [blog_name] - [blog_url]',
		'includeImages' => 'false');
	$kalinsPDFAdminOptions['beforePage'] = '<h1>[post_title]</h1><p><b>by [post_author] - [post_date]</b></p><p><a href="[guid]">[guid]</a></p>';
	$kalinsPDFAdminOptions['beforePost'] = '<h1>[post_title]</h1><p><b>by [post_author] - [post_date]</b></p><p><a href="[guid]">[guid]</a></p>';;
	$kalinsPDFAdminOptions['afterPage'] = '<p align="center">_______________________________________________</p><p align="center">PDF generated by Kalin\'s PDF Creation Station</p>';
	$kalinsPDFAdminOptions['afterPost'] = '<p align="center">_______________________________________________</p><p align="center">PDF generated by Kalin\'s PDF Creation Station</p>';
	$kalinsPDFAdminOptions['titlePage'] = '';
	$kalinsPDFAdminOptions['finalPage'] = '';
	$kalinsPDFAdminOptions['fontSize'] = 10;
	$kalinsPDFAdminOptions['showLink'] = "none";
	$kalinsPDFAdminOptions['linkText'] = "Download [post_title] as PDF";
	$kalinsPDFAdminOptions['beforeLink'] = '<br/><p align="right">-- ';
	$kalinsPDFAdminOptions['afterLink'] = " --</p><br/>";
	$kalinsPDFAdminOptions['doCleanup'] = "true";
	$kalinsPDFAdminOptions['charCount'] = 1000;
	
	return $kalinsPDFAdminOptions;
}

function kalins_pdf_getDefaultOptions(){//simply returns all our default option values
	$kalinsPDFAdminOptions = array('headerTitle' => '[blog_name] - [current_time]',
		'headerSub' => '[blog_description] - [blog_url]',
		'filename' => '[blog_name]',
		'includeImages' => 'false');
	$kalinsPDFAdminOptions['beforePage'] = '<h1>[post_title]</h1><p><b>by [post_author] - [post_date]</b></p><p><a href="[guid]">[guid]</a></p>';
	$kalinsPDFAdminOptions['beforePost'] = '<h1>[post_title]</h1><p><b>by [post_author] - [post_date]</b></p><p><a href="[guid]">[guid]</a></p>';;
	$kalinsPDFAdminOptions['afterPage'] = '<p align="center">_______________________________________________</p>';
	$kalinsPDFAdminOptions['afterPost'] = '<p align="center">_______________________________________________</p>';
	$kalinsPDFAdminOptions['titlePage'] = '<p><font size="40">[blog_name]</font></p><p><font size="25">[blog_description]</font></p><p>PDF generated [current_time] by Kalin\'s PDF Creation Station WordPress plugin</p>';
	$kalinsPDFAdminOptions['finalPage'] = '<b>[blog_name]<b><p><b>[blog_description]</b></p><p>PDF generated [current_time] by Kalin\'s PDF Creation Station WordPress plugin</p>';
	$kalinsPDFAdminOptions['fontSize'] = 10;
	
	return $kalinsPDFAdminOptions;
}

function kalins_pdf_cleanup() {//deactivation hook. Clear all traces of PDF Creation Station
	
	$adminOptions = kalins_pdf_get_admin_options();
	if($adminOptions['doCleanup'] == 'true'){//if user set cleanup to true, remove all options and post meta data
		
		delete_option(KALINS_PDF_TOOL_OPTIONS_NAME);
		delete_option(KALINS_PDF_ADMIN_OPTIONS_NAME);//remove all options for admin
		
		$allposts = get_posts();//first get and delete all post meta data
		foreach( $allposts as $postinfo) {
			delete_post_meta($postinfo->ID, 'kalinsPDFMeta');
		}
		
		$allposts = get_pages();//then get and delete all page meta data
		foreach( $allposts as $postinfo) {
			delete_post_meta($postinfo->ID, 'kalinsPDFMeta');
		}
	}
} 

function kalins_pdf_init(){
	//setup internationalization here
	//this doesn't actually run and perhaps there's another better place to do internationalization
}

//----------------begin utility functions-----------------------

//Note: none of these shortcodes are entered into the standard WordPress shortcode system so they only function within Kalin's PDF Creation Station
function kalins_pdf_page_shortcode_replace($str, $page){//replace all passed in shortcodes
	$SCList =  array("[ID]", "[post_date]", "[post_date_gmt]", "[post_title]", "[post_excerpt]", "[post_name]", "[post_modified]", "[post_modified_gmt]", "[guid]", "[comment_count]");
	
	$l = count($SCList);
	for($i = 0; $i<$l; $i++){//loop through all page shortcodes (the ones that only work for before/after page/post and refer directly to a page/post attribute)
		$scName = substr($SCList[$i], 1, count($SCList[$i]) - 2);
		$str = str_replace($SCList[$i], $page->$scName, $str);
	}
	$str = str_replace("[post_author]", get_userdata($page->post_author)->user_login, $str);//post_author requires an extra function call to convert the userID into a name so we can't do it in the loop above
	
	$str = kalins_pdf_global_shortcode_replace($str);//then parse the global shortcodes
	
	return $str;
}

function kalins_pdf_global_shortcode_replace($str){//replace global shortcodes
	$str = str_replace("[blog_name]", get_option('blogname'), $str);
	$str = str_replace("[blog_description]", get_option('blogdescription'), $str);
	$str = str_replace("[blog_url]", get_option('home'), $str);
	$str = str_replace("[current_time]", date("Y-m-d H:i:s", time()), $str);
	return $str;
}

function createPDFDir(){
	
	$uploadDir = wp_upload_dir();
	$newDir = $uploadDir['basedir'].'/kalins-pdf';
	
	if(!file_exists($newDir)){
		mkdir($newDir);
	}
	
	if(!file_exists($newDir .'/singles')){
		mkdir($newDir .'/singles');
	}
}

//---------------------end utility functions-----------------------------------


//wp actions to get everything started
add_action('admin_init', 'kalins_pdf_admin_init');
add_action('admin_menu', 'kalins_pdf_configure_pages');
//add_action( 'init', 'kalins_pdf_init' );//just keep this for whenever we do internationalization - if the function is actually needed, that is.


//content filter is called whenever a blog page is displayed. Comment this out if you aren't using links applied directly to individual posts, or if the link is set in your theme
add_filter("the_content", "kalinsPDF_content_filter" );

?>