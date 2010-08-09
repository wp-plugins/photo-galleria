<?php
/****************************************************************
Plugin Name: Photo Galleria
Plugin URI: http://graphpaperpress.com/2008/05/31/photo-galleria-plugin-for-wordpress/
Description: Creates beautiful slideshows from embedded WordPress galleries.
Version: 0.3.2
Author: Thad Allender
Author URI: http://graphpaperpress.com
License: GPL
*****************************************************************
Bravo Aino!
http://galleria.aino.se/
****************************************************************/

/**
 * Define plugin constants
 */
$foldername ='photo-galleria';
load_plugin_textdomain($foldername, PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)));
$pluginURI = get_option('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));

add_action( 'admin_init', 'photo_galleria_options_init' );
add_action( 'admin_menu', 'photo_galleria_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function photo_galleria_options_init(){
	register_setting( 'photo_galleria_options', 'photo_galleria', 'photo_galleria_options_validate' );
}

/**
 * Load up the menu page
 */
function photo_galleria_options_add_page() {
	add_options_page( __( 'Photo Galleria' ), __( 'Photo Galleria' ), 'manage_options', 'photo_galleria_options', 'photo_galleria_options_do_page' );
}

/**
 * Create arrays for our select and radio options
 */
 
$design_options = array(
	'classic' => array(
		'value' =>	'classic',
		'label' => __( 'Classic' )
	),
	'dots' => array(
		'value' =>	'dots',
		'label' => __( 'Dots' )
	),
		'lightbox' => array(
		'value' =>	'lightbox',
		'label' => __( 'Lightbox' )
	),
);

$transition_options = array(
	'fade' => array(
		'value' =>	'fade',
		'label' => __( 'Fade' )
	),
	'flash' => array(
		'value' =>	'flash',
		'label' => __( 'Flash' )
	),
	'slide' => array(
		'value' => 'slide',
		'label' => __( 'Slide' )
	),
	'fadeslide' => array(
		'value' => 'fadeslide',
		'label' => __( 'Fade & Slide' )
	)
);

/**
 * Create the options page
 */
function photo_galleria_options_do_page() {
	global $design_options, $transition_options;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;

	?>
	<div class="wrap">
		<h2><?php _e( 'Photo Galleria' ); ?></h2>
		
		<p>This plugin was made by us for you for free.  If you need help with this plugin, visit our <a href="http://graphpaperpress.com/support/" target="_blank" title="visit the Graph Paper Press support forums">support forum</a>, which is staffed daily by three WordPress developers.  No question goes unanswered.  If you like this plugin, you will love <a href="http://graphpaperpress.com" target="_blank" title="visit Graph Paper Press">our themes</a>.</p>
		
		<form method="post" action="options.php">
			<?php settings_fields('photo_galleria_options'); ?>
			<?php $options = get_option('photo_galleria'); ?>

			<table class="form-table">
			
				<?php
				
				/**
				 * Design options
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Design' ); ?></th>
					<td>
						<select name="photo_galleria[design]">
							<?php
								$selected = $options['design'];
								$p = '';
								$r = '';

								foreach ( $design_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="photo_galleria[design]"><?php _e( 'Select a design.  Don\'t be shy.' ); ?></label>
					</td>
				</tr>

				<?php
				/**
				 * Autoplay
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Autoplay' ); ?></th>
					<td>
						<input id="photo_galleria[autoplay]" name="photo_galleria[autoplay]" type="checkbox" value="1" <?php checked( '1', $options['autoplay'] ); ?> />
						<label class="description" for="photo_galleria[autoplay]"><?php _e( 'Check to play as a slideshow' ); ?></label>
					</td>
				</tr>

				<?php
				
				/**
				 * Height options
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Height' ); ?></th>
					<td>
						<input style="width:100px" id="photo_galleria[height]" class="regular-text" type="text" name="photo_galleria[height]" value="<?php esc_attr_e( $options['height'] ); ?>" />
						<label class="description" for="photo_galleria[height]"><?php _e( 'Set a maximum fixed height in pixels. Otherwise, things break.  Numbers only.  Example: 590' ); ?></label>
					</td>
				</tr>

				<?php
				
				/**
				 * Transition options
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Transition' ); ?></th>
					<td>
						<select name="photo_galleria[transition]">
							<?php
								$selected = $options['transition'];
								$p = '';
								$r = '';

								foreach ( $transition_options as $option ) {
									$label = $option['label'];
									if ( $selected == $option['value'] ) // Make default first in list
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr( $option['value'] ) . "'>$label</option>";
									else
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr( $option['value'] ) . "'>$label</option>";
								}
								echo $p . $r;
							?>
						</select>
						<label class="description" for="photo_galleria[transition]"><?php _e( 'How do you want Photo Galleria to transition from image to image?' ); ?></label>
					</td>
				</tr>
				
				<?php
				
				/**
				 * Color options
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Background color' ); ?></th>
					<td>
						<input style="width:100px" id="photo_galleria[color]" class="regular-text" type="text" name="photo_galleria[color]" value="<?php esc_attr_e( $options['color'] ); ?>" />
						<label class="description" for="photo_galleria[color]"><?php _e( 'Must be a hexidecimal value, example: #000000.  Must include the #.' ); ?></label>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
			</p>
		</form>
		
		<h3>Common questions</h3>
		<h4>Why does mine not work?</h4>
		<p>You likely have a plugin that is inserting a conflicting javascript (the stuff that runs Photo Galleria). Deactivate your plugins, one by one, to see which one is the culprit.  If that doesn't work, switch to the default WordPress theme to see if your theme is actually adding conflicting javascript.  If it is, consider upgrading to <a href="http://graphpaperpress.com" target="_blank" title="visit Graph Paper Press">a better theme.</a>  Finally, delete your browser cache after completing the steps above.</p>
		<h4>Can I have multiple Photo Gallerias on my homepage, archive page, page or post?</h4>
		<p>No.  Why?  Photo Galleria loads large images all at once.  If you have, say, 10 posts on your archive page, each containing 10 images, your users would have to wait for 100 large images to load before they could even begin to interact with your Photo Galleria.  This would make for a terrible user experience.</p>
		<h4>How do I change colors, thumbnail sizes or icons?</h4>
		<p>Virtually every aspect of each theme is customizable with CSS.  Themes are located here: /wp-content/plugins/photo-galleria/themes/.  For example, here I will enlarge the thumbnails:</p>
        <p><code>.galleria-thumbnails .galleria-image{width:90px;height:60px;}<br />
        .galleria-thumbnails-list{height:60px;}<br />
        .galleria-thumb-nav-left,<br />
        .galleria-thumb-nav-right{height:55px;}<br />
        .galleria-info,<br />
        .galleria-counter{bottom:80px;}</code></p>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function photo_galleria_options_validate( $input ) {
	global $design_options, $transition_options;

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['autoplay'] ) )
		$input['autoplay'] = null;
	$input['autoplay'] = ( $input['autoplay'] == 1 ? 1 : 0 );

	// Say our text option must be safe text with no HTML tags
	$input['height'] = wp_filter_nohtml_kses( $input['height'] );
	
	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['design'], $design_options ) )
		$input['design'] = null;

	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['transition'], $transition_options ) )
		$input['transition'] = null;

	return $input;
}

/**
 * Load javascripts
 */
if (!is_admin()) add_action( 'init', 'photo_galleria_load_scripts' );
function photo_galleria_load_scripts( ) {
	wp_enqueue_script('jquery');
	wp_enqueue_script('photo-galleria', plugins_url( 'galleria.js', __FILE__ ), array('jquery'));
}

/**
 * Add scripts to head
 */
function photo_galleria_scripts_head(){

	// Retreive our plugin options
	$photo_galleria = get_option( 'photo_galleria' );
	$design = $photo_galleria['design'];
		if ($design == 'classic' || $design == '') {
				$design = '/themes/classic/galleria.classic.js';}
			elseif ($design == 'dots') {
				$design = '/themes/dots/galleria.dots.js';}
			elseif ($design == 'lightbox') {
				$design = '/themes/lightbox/galleria.lightbox.js';}
	$autoplay = $photo_galleria['autoplay'];
		if ($autoplay == 1) { $autoplay = '5000'; }
		if ($autoplay == 0) { $autoplay = 'false'; }
	$height = $photo_galleria['height'];
    if($height=="")
        $height = 500;
	$transition = $photo_galleria['transition'];
	$color = $photo_galleria['color'];
	
	$pluginURI = get_option('siteurl').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
	
	if(!is_admin()){
		echo "<script>
			    
  // Load theme
  Galleria.loadTheme('" . $pluginURI . "" . $design . "');
  
  // run galleria and add some options
  jQuery('#galleria').galleria({
  		autoplay: " . $autoplay . ",
      height: " . $height . ",
      transition: '" . $transition . "',
      data_config: function(img) {
          // will extract and return image captions from the source:
          return  {
              title: jQuery(img).parent().next('strong').html(),
              description: jQuery(img).parent().next('strong').next().html()
          };
      }
  });
  </script>
  <style type='text/css'>.galleria-container {background-color: " . $color . "}</style>";
	}
}

add_action('wp_footer','photo_galleria_scripts_head');

/**
 * Lets make new gallery shortcode
 */
function photo_galleria_shortcode($attr) {

global $post;
$pid = $post->ID;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}
	extract(shortcode_atts(array(
		'orderby' => 'menu_order ASC, ID ASC',
		'id' => $post->ID,
		'size' => 'medium',
	), $attr));

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

	// Build galleria markup
	$output = apply_filters('gallery_style', '<div id="galleria"><!-- Begin Galleria -->');

	// Loop through each image
	foreach ( $attachments as $id => $attachment ) {
		
		// Attachment page ID
		$att_page = get_attachment_link($id);
		// Returns array
		$img = wp_get_attachment_image_src($id, 'large');
		$img = $img[0];
		$thumb = wp_get_attachment_image_src($id, 'thumbnail');
		$thumb = $thumb[0];
		// Set the image titles
		$title = $attachment->post_title;
		// Set the image captions
		$description = $attachment->post_content;
		if($description == '') $description = $attachment->post_excerpt;

		// Build html for each image
		$output .= "\n\t\t<div>";
		$output .= "\n\t\t\t<a href='".$img."'>";
		$output .= "\n\t\t\t\t<img src='".$thumb."' alt='".$description."' title='".$description."' />";
		$output .= "\n\t\t</a>";
		$output .= "\n\t\t<strong>".$title."</strong>";
		$output .= "\n\t\t<span>".$description."</span>";
		$output .= "\n\t\t</div>";
	
	// End foreach
	}
	
	// Close galleria markup
	$output .= "\n\t</div><!-- End Galleria -->";
	return $output;
}

	// Remove original wp gallery shortcode
	remove_shortcode(gallery);

	// Add our new shortcode with galleria markup
	add_shortcode('gallery', 'photo_galleria_shortcode');
?>