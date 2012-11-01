<?php

/****************************************************************

Plugin Name: Photo Galleria
Plugin URI: http://graphpaperpress.com/2008/05/31/photo-galleria-plugin-for-wordpress/
Description: Creates beautiful slideshows from embedded WordPress galleries.
Version: 0.5
Author: Thad Allender
Author URI: http://graphpaperpress.com
License: GPL


/**
 * Init plugin options
 */

function photo_galleria_options_init(){
	register_setting( 'photo_galleria_options', 'photo_galleria', 'photo_galleria_options_validate' );
}
add_action( 'admin_init', 'photo_galleria_options_init' );


/**
 * Add admin menu page
 */

function photo_galleria_options_add_page() {
	add_options_page( __( 'Photo Galleria' ), __( 'Photo Galleria' ), 'manage_options', 'photo_galleria_options', 'photo_galleria_options_do_page' );
}
add_action( 'admin_menu', 'photo_galleria_options_add_page' );


/**
 * Create arrays for our select and radio options
 */

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
	global $transition_options;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;

	?>
	<div class="wrap">
		<h2><?php _e( 'Photo Galleria' ); ?></h2>
		<p><?php printf(__( 'This plugin was made by us for you for free. If you like this plugin, you will love our %1$s.', 'photo_galleria' ), '<a href="http://graphpaperpress.com" target="_blank" title="visit Graph Paper Press">WordPress themes for photographers</a>' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields('photo_galleria_options'); ?>
			<?php $options = get_option('photo_galleria'); ?>

			<table class="form-table">

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

			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
			</p>
		</form>

		<h3><?php _e( 'Common questions', 'photo_galleria' ); ?></h3>
		<h4><?php _e( 'Why does mine not work', 'photo_galleria' ); ?>?</h4>
		<p><?php _e( 'You likely have a plugin that is inserting a conflicting javascript (the stuff that runs Photo Galleria). Deactivate your plugins, one by one, to see which one is the culprit.  If that doesn\'t work, switch to the default WordPress theme to see if your theme is actually adding conflicting javascript. Finally, delete your browser cache after completing the steps above.', 'photo_galleria' ); ?></p>
		<h4><?php _e( 'Does this plugin work with Internet Explorer 6 or 7?', 'photo_galleria' ); ?></h4>
		<p><?php _e( 'No, and it never will.', 'photo_galleria' ); ?></p>
		<h4><?php _e( 'How can I change the gallery background color', 'photo_galleria' ); ?>?</h4>
		<p><?php printf(__( 'The background color of the gallery is controlled with css. Add this css to your theme\'s style.css file: %1$s', 'photo_galleria' ), '<code>.galleria-container { background-color: #ffffff; }</code>' ); ?></p>
		<h4><?php _e( 'How do I center my thumbnails', 'photo_galleria' ); ?>?</h4>
		<p><?php printf(__( 'Add this css to your theme\'s style.css file: %1$s', 'photo_galleria' ), '<code>.galleria-thumbnails { margin: 0 auto; }</code>' ); ?></p>
	</div>
	<?php
}


/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */

function photo_galleria_options_validate( $input ) {
	global $transition_options;

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['autoplay'] ) )
		$input['autoplay'] = null;
	$input['autoplay'] = ( $input['autoplay'] == 1 ? 1 : 0 );

	// Say our text option must be safe text with no HTML tags
	$input['height'] = wp_filter_nohtml_kses( $input['height'] );

	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['transition'], $transition_options ) )
		$input['transition'] = null;

	return $input;
}


/**
 * Load javascripts
 */

if ( !is_admin() )
	add_action( 'init', 'photo_galleria_load_scripts' );

function photo_galleria_load_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'photo-galleria', plugins_url( 'js/galleria-1.2.8.min.js', __FILE__ ), array( 'jquery' ) );
}


/**
 * Load galleria.js in footer
 */

function photo_galleria_scripts_head(){

	if ( !is_admin() ) {

		global $post, $wp_query;

			// Retreive our plugin options
			$photo_galleria = get_option( 'photo_galleria' );
			$autoplay = $photo_galleria['autoplay'];
			if ( $autoplay == 1 ) { $autoplay = '5000'; }
			if ( $autoplay == 0 ) { $autoplay = 'false'; }
			$height = $photo_galleria['height'];
		    if ( $height == '' ) { $height = 500; }
			$transition = $photo_galleria['transition'];

		?>
		<script type="text/javascript">
			Galleria.loadTheme('<?php echo plugin_dir_url( __FILE__); ?>js/themes/classic/galleria.classic.js');
			// show only on homepage and archive pages
			jQuery('.galleria').galleria({
				autoplay: <?php echo $autoplay; ?>,
				height: <?php echo $height; ?>,
				transition: '<?php echo $transition; ?>',
				clicknext: true,
				data_config: function(img) {
					// will extract and return image captions from the source:
					return  {
						title: jQuery(img).parent().next('strong').html(),
						description: jQuery(img).parent().next('strong').next().html()
					};
				}
			});
		</script>
	<?php }

}
add_action( 'wp_footer', 'photo_galleria_scripts_head' );


/**
 * Build the gallery shortcode
 */

function photo_galleria_shortcode($attr) {

global $post;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}
	extract( shortcode_atts( array(
		'orderby' => 'menu_order ASC, ID ASC',
		'id' => $post->ID,
		'size' => 'large',
	), $attr ) );

	$id = intval( $id );
	$attachments = get_children( "post_parent=$id&post_type=attachment&post_mime_type=image&orderby={$orderby}" );

	if ( empty( $attachments ) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link( $id, $size, true ) . "\n";
		return $output;
	}

	// Build galleria markup
	$output = apply_filters( 'gallery_style', '<div id="galleria-' . $post->ID . '" class="galleria">' );

	// Loop through each image
	foreach ( $attachments as $id => $attachment ) {

		// Attachment page ID
		$att_page = get_attachment_link( $id );
		// Returns array
		$img = wp_get_attachment_image_src( $id, 'large' );
		$img = $img[0];
		$thumb = wp_get_attachment_image_src( $id, 'thumbnail' );
		$thumb = $thumb[0];
		// Set the image titles
		$title = $attachment->post_title;
		// Get the Permalink
		$permalink = get_permalink();
		// Set the image captions
		$description = htmlspecialchars( $attachment->post_content, ENT_QUOTES );
		if( $description == '' ) $description = htmlspecialchars( $attachment->post_excerpt, ENT_QUOTES );

		// Build html for each image
		$output .= "\n\t\t<div>";
		$output .= "\n\t\t\t<a href='" . $img . "'>";
		$output .= "\n\t\t\t\t<img src='" . $thumb . "' longdesc='" . $permalink . "' alt='" . $description . "' title='" . $description . "' />";
		$output .= "\n\t\t</a>";
		$output .= "\n\t\t<strong>" . $title . "</strong>";
		$output .= "\n\t\t<span>" . $description . "</span>";
		$output .= "\n\t\t</div>";

	// End foreach
	}

	// Close galleria markup
	$output .= "\n\t</div><!-- End Galleria -->";
	return $output;
}


/**
 * Remove and add new gallery shortcode
 */

function photo_galleria_add_shortcode() {
	remove_shortcode( 'gallery' );
	add_shortcode( 'gallery', 'photo_galleria_shortcode' );
}
add_action( 'wp_head', 'photo_galleria_add_shortcode' );