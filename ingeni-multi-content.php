<?php
/*
Plugin Name: Ingeni Multi Content
Plugin URI: https://github.com/BruceMcKinnon/ingeni-multi-content
Description: Flexible CPT that supports multiple content blocks within a single post.
Author: Bruce McKinnon
Author URI: https://ingeni.net
Version: 2021.01
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html


2021.01 - Initial version


*/

if ( !class_exists( 'IngeniMultiBlocks' ) ) {
	class IngeniMultiBlocks {
		public $name = 'Ingeni Multi Blocks';
		public $tag = 'multi-block';
		public $options = array();
		public $messages = array();
		public $details = array();

		public function __construct() {
			add_action( 'init', array( &$this, 'cpt_init' ) );

			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'imc_add_meta_boxes' ) );
				add_action( 'save_post', array( &$this, 'imc_content_save' ) );


			} else {
				add_shortcode( 'ingeni-multi-block', array( &$this, 'ingeni_multi_block_shortcode' ) );


				//add_action('wp_head', array( &$this, 'bl_insert_custom_script'), 20 );

				// And enqueue the Leaflet apis
				//add_action( 'wp_enqueue_scripts', array( &$this, 'bl_enqueue_leaflet' ) );

				//add_action('wp_head', array( &$this, 'bl_insert_google_analytics' ));
				//add_action('wp_footer', array( &$this, 'echo_json_ld' ));
			}
		}

		function activate() {
				$this->cpt_init();
				flush_rewrite_rules();
		}
		
		function register() {
				//add_action('admin_enqueue_scripts', array($this, 'enqueue'));
		}
		
		function deactivate() {
				flush_rewrite_rules();
		}




		public function cpt_init() {

			$this->ingeni_mc_custom_post_type();


			// Init auto-update from GitHub repo
			require 'plugin-update-checker/plugin-update-checker.php';
			$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
				'https://github.com/BruceMcKinnon/ingeni-multi-content',
				__FILE__,
				'ingeni-multi-content'
			);

		}


		function ingeni_mc_custom_post_type() {
			$cpt_obj = register_post_type('ingeni_multicontent',
				array(
				'labels' => array(
					'name' => __('Multi Contents', 'textdomain'),
					'singular_name' => __('Multi Content', 'textdomain'),
				),
				'rewrite' => array( 'slug' => 'imc' ), // my custom slug
				'menu_icon'   => 'dashicons-layout',
				// Features this CPT supports in Post Editor
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields', ),
				// A hierarchical CPT is like Pages and can have Parent and child items.
				// A non-hierarchical CPT is like Posts
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
        'show_in_rest' => true,
				'taxonomies' => array('category','post_tag'),
				)
			);

			if ( is_wp_error( $cpt_obj ) ) {
				$this->fb_log('error: '.$cpt_obj->get_error_message());
			}
		}




		//
		// Utility functions
		//
		private function startsWith($haystack, $needle) {
			// search backwards starting from haystack length characters from the end
			return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
		}

		private function endsWith($haystack, $needle) {
			// search forward starting from end minus needle length characters
			return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
		}


		private function get_local_upload_path() {
			$upload_dir = wp_upload_dir();
			return $upload_dir['baseurl'];
		}

		private function fb_log($msg) {
			$upload_dir = wp_upload_dir();
			$logFile = $upload_dir['basedir'] . '/' . 'fb_log.txt';
			date_default_timezone_set('Australia/Sydney');

			// Now write out to the file
			$log_handle = fopen($logFile, "a");
			if ($log_handle !== false) {
				fwrite($log_handle, date("H:i:s").": ".$msg."\r\n");
				fclose($log_handle);
			}
		}



		private function bool2str($value) {
			if ($value)
				return 'true';
			else
				return 'false';
		}

		private function intToBool($value) {
			if (is_int($value)) {
				if ($value == 0) {
					$value = false;
				} else {
					$value = true;
				}
			}
			return $value;
		}

		//
		// End utility functions
		//


		// https://developer.wordpress.org/reference/functions/add_meta_box/
		// https://www.smashingmagazine.com/2012/11/complete-guide-custom-post-types/

		// Adds the meta box containers

		public function imc_add_meta_boxes( ) {

			add_meta_box(
				'imc_content2_title',
				__( 'Content #2 Title', 'textdomain' ),
				array( &$this, 'render_imc_content2_title' ),
				'ingeni_multicontent',
				'normal',
				'high'
			);

			add_meta_box(
				'imc_content2',
				__( 'Content #2', 'textdomain' ),
				array( &$this, 'render_imc_content2' ),
				'ingeni_multicontent',
				'normal',
				'high'
			);

			add_meta_box(
				'imc_content3_title',
				__( 'Content #3 Title', 'textdomain' ),
				array( &$this, 'render_imc_content3_title' ),
				'ingeni_multicontent',
				'normal',
				'high'
			);

			add_meta_box(
				'imc_content3',
				__( 'Content #3', 'textdomain' ),
				array( &$this, 'render_imc_content3' ),
				'ingeni_multicontent',
				'normal',
				'high'
			);
		}

		// Render Meta Box content.
		public function render_imc_content2_title( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field(  plugin_basename( __FILE__ ), 'imc_content2_title_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$value = get_post_meta( $post->ID, '_imc_content2_title', true );

			// Display the form, using the current value.
			?>
			<label for="imc_content2_title">Content #2 Title: </label>
			<input type="text" autocomplete="off" name="imc_content2_title" id="imc_content2_title" value="<?php echo($value); ?>" />
			<?php
		}

		public function render_imc_content2( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field(  plugin_basename( __FILE__ ), 'imc_content2_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$value = get_post_meta( $post->ID, '_imc_content2', true );

			// Display the form, using the current value.
			?>
			<textarea class="wp-editor-area" style="height:250px;width:100%;" autocomplete="off" cols="40" name="imc_content2" id="imc_content2"><?php echo($value); ?></textarea>
			<?php
		}

		public function render_imc_content3_title( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field(  plugin_basename( __FILE__ ), 'imc_content3_title_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$value = get_post_meta( $post->ID, '_imc_content3_title', true );

			// Display the form, using the current value.
			?>
			<label for="imc_content3_title">Content #3 Title: </label>
			<input type="text" autocomplete="off" name="imc_content3_title" id="imc_content3_title" value="<?php echo($value); ?>" />
			<?php
		}

		public function render_imc_content3( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field(  plugin_basename( __FILE__ ), 'imc_content3_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$value = get_post_meta( $post->ID, '_imc_content3', true );

			// Display the form, using the current value.
			?>
			<textarea class="wp-editor-area" style="height:250px;width:100%;" autocomplete="off" cols="40" name="imc_content3" id="imc_content3"><?php echo($value); ?></textarea>
			<?php
		}

		public function render_imc_content4_title( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field(  plugin_basename( __FILE__ ), 'imc_content4_title_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$value = get_post_meta( $post->ID, '_imc_content4_title', true );

			// Display the form, using the current value.
			?>
			<label for="imc_content4_title">Content #4 Title: </label>
			<input type="text" autocomplete="off" name="imc_content4_title" id="imc_content4_title" value="<?php echo($value); ?>" />
			<?php
		}

		public function render_imc_content4( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field(  plugin_basename( __FILE__ ), 'imc_content4_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$value = get_post_meta( $post->ID, '_imc_content4', true );

			// Display the form, using the current value.
			?>
			<textarea class="wp-editor-area" style="height:250px;width:100%;" autocomplete="off" cols="40" name="imc_content4" id="imc_content4"><?php echo($value); ?></textarea>
			<?php
		}



		// Save the meta when the post is saved
		public function imc_content_save( $post_id ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

			if ( !wp_verify_nonce( $_POST['imc_content2_nonce'], plugin_basename( __FILE__ ) ) ) {
				$this->fb_log('bad nonce');
				return;
			}


			// Check the user's permissions.
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					$this->fb_log('cant edit page');
						return $post_id;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					$this->fb_log('cant edit');
						return $post_id;
				}
			}

			// OK, it's safe for us to save the data now.

			// Sanitize the user input.
			$new_content2_title = $_POST['imc_content2_title'];
			// Update the meta field.
			update_post_meta( $post_id, '_imc_content2_title', $new_content2_title );
			// Sanitize the user input.
			$new_content2 = $_POST['imc_content2'];
			// Update the meta field.
			update_post_meta( $post_id, '_imc_content2', $new_content2 );
	
	
			// Sanitize the user input.
			$new_content3_title = $_POST['imc_content3_title'];
			// Update the meta field.
			update_post_meta( $post_id, '_imc_content3_title', $new_content3_title );
			// Sanitize the user input.
			$new_content3 = $_POST['imc_content3'];
			// Update the meta field.
			update_post_meta( $post_id, '_imc_content3', $new_content3 );
		
	
			// Sanitize the user input.
			$new_content4_title = $_POST['imc_content4_title'];
			// Update the meta field.
			update_post_meta( $post_id, '_imc_content4_title', $new_content4_title );
			// Sanitize the user input.
			$new_content4 = $_POST['imc_content4'];
			// Update the meta field.
			update_post_meta( $post_id, '_imc_content4', $new_content4 );
	}



	public function ingeni_multi_block_shortcode( $atts ) {
		$params = shortcode_atts( array(
			'id' => 0,
			'content_id' => 1,
			'show_title' => 1,
			'show_content' => 1,
			'class' => 'imc_wrapper',
		), $atts );

		$retHtml = '<div class="' . $params['class'] . '">';

		if( $params["id"] != "" ) {
			$args = array(
				'post__in' => array( $params["id"] ),
				'post_type' => 'ingeni_multicontent',
			);
	
			$content_post = get_posts( $args );
	
			foreach( $content_post as $post ) {
				$content_num = $params['content'];
				if (($content_num < 1)||($content_num > 4)) {
					$content_num = 1;
				}

				if ($params['show_title'] > 0) {
					$retHtml .= get_post_meta( $post->ID, '_imc_content'.$content_num.'_title', true );
				}
				if ($params['show_content'] > 0) {
					$retHtml .= get_post_meta( $post->ID, '_imc_content'.$content_num, true );
				}
			}
		} else {
			$retHtml .= '<p>Post ID '.$params["id"].' not found!</p>';
		}

		$retHtml .= '</div>';
	}




	} // End of Class


} // End class_exists


if (class_exists('IngeniMultiBlocks')) {
	$multiBlocks = new IngeniMultiBlocks();
	$multiBlocks->register();
}

register_activation_hook(__FILE__, array($multiBlocks, 'activate'));
register_deactivation_hook(__FILE__, array($multiBlocks, 'deactivate'));
register_uninstall_hook(__FILE__, array($multiBlocks, 'uninstall'));

?>