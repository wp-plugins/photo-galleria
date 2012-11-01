<?php

/****************************************************************

Plugin Name: Photo Galleria
Plugin URI: http://graphpaperpress.com/2008/05/31/photo-galleria-plugin-for-wordpress/
Description: Creates beautiful slideshows from embedded WordPress galleries.
Version: 0.5.1
Author: Thad Allender
Author URI: http://graphpaperpress.com
License: GPL


/**
 * Define plugin constants
 */

define ( 'PHOTO_GALLERIA_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) );
define ( 'PHOTO_GALLERIA_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename(__FILE__) ) );
define ( 'PHOTO_GALLERIA_USER_THEME_FOLDER',  '/galleria-themes/' );

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
function photo_galleria_default_options() {

	//Stylesheets Reader
	$alt_stylesheets = array();
	$alt_stylesheets_path = get_theme_root() . PHOTO_GALLERIA_USER_THEME_FOLDER;
	if ( is_dir( $alt_stylesheets_path ) ) {
	    if ( $alt_stylesheet_dir = opendir( $alt_stylesheets_path ) ) {
	        while ( ( $alt_stylesheet_file = readdir( $alt_stylesheet_dir ) ) !== false ) {
	            if ( stristr( $alt_stylesheet_file, '.js' ) !== false ) {
	                $alt_stylesheets[] = $alt_stylesheet_file;
	            }
	        }
	    }
	}

	$options['design'] = array(
		'classic' => array(
			'value' =>	'classic',
			'label' => __( 'Classic' )
		)
	);

	if( !empty( $alt_stylesheets ) ) {
		foreach( $alt_stylesheets as $alt_stylesheet ) {
			$options['design'][$alt_stylesheet] = array(
				'value' =>	$alt_stylesheet,
				'label' => $alt_stylesheet
			);
		}
	}

	$options['transition'] = array(
		'default' => array(
			'value' =>	'',
			'label' => __( 'Default Transition' )
		),
		'fade' => array(
			'value' =>	'fade',
			'label' => __( 'Fade' )
		),
		'flash' => array(
			'value' =>	'flash',
			'label' => __( 'Flash' )
		),
		'pulse' => array(
			'value' =>	'pulse',
			'label' => __( 'Pulse' )
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

	return apply_filters( 'photo_galleria_default_options', $options );
}


/**
 * Create the options page
 */

function photo_galleria_options_do_page() {
	$defaults = photo_galleria_default_options();

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
				 * Design options
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Design' ); ?></th>
					<td>
						<select name="photo_galleria[design]">
							<?php
								$selected = $options['design'];

								foreach ( $defaults['design'] as $option ) {
									$label = $option['label'];
									$value = esc_attr( $option['value'] );

									echo "<option style='padding-right: 10px;' value='$value'";
									if ( $selected == $option['value'] ) {
										echo ' selected=selected ';
									}
									echo ">$label</option>";
								}
							?>
						</select>
						<label class="description" for="photo_galleria[design]"><?php printf( __( 'Select a design. More %1$s are also available. See the FAQ section below.', 'photo_galleria' ), '<a href="http://galleria.io/themes/" target="_blank">Galleria Themes</a>' ); ?></label>
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
				 * Crop options
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Crop to Fit' ); ?></th>
					<td>
						<input id="photo_galleria[crop]" name="photo_galleria[crop]" type="checkbox" value="1" <?php checked( '1', $options['crop'] ); ?> />
						<label class="description" for="photo_galleria[crop]"><?php _e( 'Check to ensure all images are scaled to fill the stage, centered and cropped.' ); ?></label>
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

								foreach ( $defaults['transition'] as $option ) {
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

		<h3><?php _e( 'FAQ', 'photo_galleria' ); ?></h3>
		<h4><?php _e( 'Why does mine not work', 'photo_galleria' ); ?>?</h4>
		<p><?php _e( 'You likely have a plugin that is inserting a conflicting javascript (the stuff that runs Photo Galleria). Deactivate your plugins, one by one, to see which one is the culprit.  If that doesn\'t work, switch to the default WordPress theme to see if your theme is actually adding conflicting javascript. Finally, delete your browser cache after completing the steps above.', 'photo_galleria' ); ?></p>
		<h4><?php _e( 'Does this plugin work with Internet Explorer 6 or 7?', 'photo_galleria' ); ?></h4>
		<p><?php _e( 'No, and it never will.', 'photo_galleria' ); ?></p>
		<h4><?php _e( 'How can I change the gallery background color', 'photo_galleria' ); ?>?</h4>
		<p><?php printf(__( 'The background color of the gallery is controlled with css. Add this css to your theme\'s style.css file: %1$s', 'photo_galleria' ), '<code>.galleria-container { background-color: #ffffff; }</code>' ); ?></p>
		<h4><?php _e( 'How do I center my thumbnails', 'photo_galleria' ); ?>?</h4>
		<p><?php printf(__( 'Add this css to your theme\'s style.css file: %1$s', 'photo_galleria' ), '<code>.galleria-thumbnails { margin: 0 auto; }</code>' ); ?></p>
		<h4><?php _e( 'How can I add more Galleria themes?', 'photo_galleria' ); ?></h4>
		<p><?php printf(__( 'To use one of the %1$s, add a "galleria-themes" directory inside your WordPress theme directory, like this: %2$s Place your galleria theme javascript file directly within the "galleria-themes" directory, like this: %3$s When you visit the Photo Galleria settings page your javascript file will appear in the design dropdown menu.', 'photo_galleria' ), '<a href="http://galleria.io/themes/" target="_blank">Galleria themes</a>', '<br /><code>wp-content/themes/galleria-themes/</code><br />', '<br /><code>wp-content/themes/galleria-themes/dots.js</code><br />' ); ?></p>
	</div>
	<?php
}


/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */

function photo_galleria_options_validate( $input ) {
	$defaults = photo_galleria_default_options();

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['autoplay'] ) )
		$input['autoplay'] = null;
	$input['autoplay'] = ( $input['autoplay'] == 1 ? 1 : 0 );

	// Our checkbox value is either 0 or 1
	if ( ! isset( $input['crop'] ) )
		$input['crop'] = null;
	$input['crop'] = ( $input['crop'] == 1 ? 1 : 0 );

	// Say our text option must be safe text with no HTML tags
	$input['height'] = wp_filter_nohtml_kses( $input['height'] );

	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['design'], $defaults['design'] ) )
		$input['design'] = null;

	// Our select option must actually be in our array of select options
	if ( ! array_key_exists( $input['transition'], $defaults['transition'] ) )
		$input['transition'] = null;

	return $input;
}


/**
 * Load javascripts
 */

function photo_galleria_load_scripts() {

	global $add_galleria_scripts;

	// only continue if shortcode has been called
	if ( !$add_galleria_scripts )
		return;

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'galleria', PHOTO_GALLERIA_PLUGIN_URL . '/js/galleria-1.2.8.min.js', array( 'jquery' ) );
	wp_print_scripts( 'galleria' );
	photo_galleria_script_options();
}
add_action( 'wp_print_footer_scripts', 'photo_galleria_load_scripts' );


/**
 * Galleria JS Options
 */

function photo_galleria_script_options(){

	if ( !is_admin() ) {

		global $post, $wp_query;

		// Retreive our plugin options
		$photo_galleria = get_option( 'photo_galleria' );

		$design = $photo_galleria['design'];
		if ( $design == 'classic' || $design == '' )
			$design_url = PHOTO_GALLERIA_PLUGIN_URL . '/js/themes/classic/galleria.classic.min.js';
		elseif ( stristr( $design, '.js' ) !== false )
			$design_url = get_theme_root_uri() . PHOTO_GALLERIA_USER_THEME_FOLDER . $design;

		$autoplay = $photo_galleria['autoplay'];
		if ( $autoplay == 1 )
			$autoplay = '5000';
		else
			$autoplay = 'false';

		$crop = $photo_galleria['crop'];
		if ( $crop == 1 )
			$crop = 'true';
		else
			$crop = 'false';

		$height = $photo_galleria['height'];
		if ( $height == '' )
			$height = 500;

		$transition = $photo_galleria['transition'];

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				Galleria.loadTheme('<?php echo $design_url; ?>');
				// show only on homepage and archive pages
				$('.photo-galleria').galleria({
					autoplay: <?php echo $autoplay; ?>,
					height: <?php echo $height; ?>,
					transition: '<?php echo $transition; ?>',
					clicknext: true,
					imageCrop: <?php echo $crop; ?>,
					data_config: function(img) {
						// will extract and return image captions from the source:
						return  {
							title: $(img).attr('title'),
							description: $(img).parents('.gallery-item').find('.gallery-caption').text()
						};
					}
				});
			});
		</script>
	<?php }

}


/**
 * Lets make new gallery shortcode
 */

function photo_galleria_shortcode( $attr ) {
	global $add_galleria_scripts;
	$add_galleria_scripts = true;

	//change default gallery_shortcode to link to images of a specified size instead of originals
	add_action( 'wp_get_attachment_link', 'photo_galleria_get_attachment_link', 2, 6 );

	//force gallery to link to image files
	$attr['link'] = 'file';

	$style = '';

	if ( isset( $attr['height'] ) ) {
		$height = intval( $attr['height'] );
		$style = "style='height:{$height}px;'";
	}

	$content = "<div class='photo-galleria' $style>";
	$content .= gallery_shortcode( $attr );
	$content .= '</div><!-- .photo-galleria -->';

	//remove our action to avoid changing this behavior for others
	remove_action( 'wp_get_attachment_link', 'photo_galleria_get_attachment_link', 2, 6 );

	return $content;
}


/**
 * Get attachment links for use in new shortcode
 */

function photo_galleria_get_attachment_link( $content, $id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false ) {
	$photo_galleria = get_option( 'photo_galleria' );

	$id = intval( $id );
	$_post = & get_post( $id );

	if ( ( 'attachment' != $_post->post_type) || !$url = wp_get_attachment_image_src( $_post->ID, 'large' ) ) {
		return __( 'Missing Attachment' );
	} else {
		$url = $url[0];
	}

	if ( $permalink )
		$url = get_attachment_link( $_post->ID );

	$post_title = esc_attr( $_post->post_title );

	if ( $text ) {
		$link_text = esc_attr($text);
	} elseif ( ( is_int( $size ) && $size != 0 ) or ( is_string( $size ) && $size != 'none' ) or $size != false ) {
		$link_text = wp_get_attachment_image( $id, $size, $icon );
	} else {
		$link_text = '';
	}

	if ( trim( $link_text ) == '' )
		$link_text = $_post->post_title;

	return apply_filters( 'photo_galleria_get_attachment_link', "<a href='$url' title='$post_title'>$link_text</a>", $id, $size, $permalink, $icon, $text );
}


/**
 * Remove default gallery shortcode so we can add new gallery shortcode
 */

function photo_galleria_init() {
	// Remove original wp gallery shortcode
	remove_shortcode( 'gallery' );
	// Add our new shortcode with galleria markup
	add_shortcode( 'gallery', 'photo_galleria_shortcode' );
}
add_action( 'init', 'photo_galleria_init' );


/**
 * Display a link to the photo galleria settings on plugins page
 */

function photo_galleria_plugin_action_links( $links, $file ) {
    static $this_plugin;

    if ( !$this_plugin ) {
        $this_plugin = plugin_basename( __FILE__ );
    }

    if ( $file == $this_plugin ) {
        // The "page" query string value must be equal to the slug
        // of the Settings admin page we defined earlier
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=photo_galleria_options' ) . '">Settings</a>';
        // add the settings page link to the beginning of the array
        array_unshift( $links, $settings_link );
    }

    return $links;
}
add_filter( 'plugin_action_links', 'photo_galleria_plugin_action_links', 10, 2 );