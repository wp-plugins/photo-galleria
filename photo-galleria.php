<?php

/*

Plugin Name: Photo Galleria
Plugin URI: http://graphpaperpress.com/2008/05/30/photo-galleria-plugin-for-wordpress/
Description: This plugin replaces the default gallery feature in WordPress 2.5+ with a minimal, jquery-powered gallery.
Version: 0.1.1
Author: Thad Allender
Author URI: http://graphpaperpress.com
License: GPL

*******************************************************
This plugin recreates your Wordpress photo gallery into a structured, minimal gallery styled with Monc's Galleria jquery script.
http://devkick.com/lab/galleria/
Thanks to Justin Tadlock for improving the Wordpress gallery functionality.
http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin

*/

function photo_galleria_js(){

if(!is_admin()){
echo "<script type='text/javascript' src='".get_bloginfo("url")."/wp-content/plugins/photo-galleria/js/jquery-1.2.6.min.js'></script>
<script type='text/javascript' src='".get_bloginfo("url")."/wp-content/plugins/photo-galleria/js/jquery.galleria.pack.js'></script>
<link rel='stylesheet' href='".get_bloginfo("url")."/wp-content/plugins/photo-galleria/css/galleria-mod.css' type='text/css' />
<link rel='stylesheet' href='".get_bloginfo("url")."/wp-content/plugins/photo-galleria/css/galleria.css' type='text/css' />

<script type='text/javascript'>

	jQuery(function($) {
		
		$('.gallery').addClass('galleria'); // adds new class name to maintain degradability
		
		$('ul.galleria').galleria({
			history   : false, // activates the history object for bookmarking, back-button etc.
			clickNext : true, // helper for making the image clickable
			insert    : '#main_image', // the containing selector for our main image
			onImage   : function(image,caption,thumb) { // let's add some image effects for demonstration purposes
				
				// fade in the image & caption
				if(! ($.browser.mozilla && navigator.appVersion.indexOf('Win')!=-1) ) { // FF/Win fades large images terribly slow
					image.css('display','none').fadeIn(600);
				}
				caption.css('display','none').fadeIn(1000);
				
				// fetch the thumbnail container
				var _li = thumb.parents('li');
				
				// fade out inactive thumbnail
				_li.siblings().children('img.selected').fadeTo(500,0.7);
				
				// fade in active thumbnail
				thumb.fadeTo('fast',1).addClass('selected');
				
				// add a title for the clickable image
				image.attr('title','Next image >>');
			},
			onThumb : function(thumb) { // thumbnail effects goes here
				
				// fetch the thumbnail container
				var _li = thumb.parents('li');
				
				// if thumbnail is active, fade all the way.
				var _fadeTo = _li.is('.active') ? '1' : '0.7';
				
				// fade in the thumbnail when finished loading
				thumb.css({display:'none',opacity:_fadeTo}).fadeIn(500);
				
				// hover effects
				thumb.hover(
					function() { thumb.fadeTo('fast',1); },
					function() { _li.not('.active').children('img').fadeTo('fast',0.7); } // don't fade out if the parent is active
				)
			}
		});
	});
	
	</script>";

}
}

add_action('wp_print_scripts','photo_galleria_js');

function photo_galleria_shortcode($attr) {
global $post;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}
	extract(shortcode_atts(array(
		'orderby' => 'menu_order ASC, ID ASC',
		'id' => $post->ID,
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'captiontag' => 'dd',
		'columns' => 3,
		'size' => 'thumbnail',
	), $attr));

        $count = 1;
	$id = intval($id);
	$attachments = get_children("post_parent=$id&post_type=attachment&post_mime_type=image&orderby={$orderby}");

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link($id, $size, true) . "\n";
		return $output;
	}

	$listtag = tag_escape($listtag);
	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;

// Open gallery
	$output = apply_filters('gallery_style', "<p class='gallery-nav'><a href='#' onclick='$.galleria.prev(); return false;'>&laquo; previous</a> | <a href='#' onclick='$.galleria.next(); return false;'>next &raquo;</a></p>
<div id='main_image'></div>
<ul class='gallery-thumbs galleria'>");

// Loop through each gallery item
	foreach ( $attachments as $id => $attachment ) {
	// Larger image URL
		$a_img = wp_get_attachment_url($id);
	// Attachment page ID
		$att_page = get_attachment_link($id);
	// Returns array
		$img = wp_get_attachment_image_src($id, $size);
		$img = $img[0];
	// If no caption is defined, set the title and alt attributes to title
		$title = $attachment->post_excerpt;
		if($title == '') $title = $attachment->post_title;

// Output each gallery item
if($count == 1)
$output .= "<li class='active'>";
if($count > 1)
$output .= "<li>";

// Set the link to the attachment URL
		$link = $a_img;
		$output .= "\t<a href=\"$link\" title=\"$title\" class=\"$a_class\" rel=\"$a_rel\">";
	// Output image
		$output .= "<img src=\"$img\" alt=\"$title\" />";
	// Close link
		$output .= "</a>";
		$output .= "</li>
";
$count++;
	// Close individual gallery item

	}
// Close gallery
	$output .= "\n</ul><div style='clear:both;' class='clear'>\n";
	return $output;
}

/************************************************
Important stuff that runs this thing
************************************************/

// Remove original gallery shortcode
	remove_shortcode(gallery);

// Add a new shortcode
	add_shortcode('gallery', 'photo_galleria_shortcode');
?>