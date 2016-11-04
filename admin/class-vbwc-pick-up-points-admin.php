<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://verbbrands.com/
 * @since      1.0.0
 *
 * @package    Vbwc_Pick_Up_Points
 * @subpackage Vbwc_Pick_Up_Points/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vbwc_Pick_Up_Points
 * @subpackage Vbwc_Pick_Up_Points/admin
 * @author     Zach Townsend <zach@verbbrands.com>
 */
class Vbwc_Pick_Up_Points_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Register necessary Custom Post Types
		add_action( 'init', [$this, 'register_cpt'] );

		// Generate shipping type on post creation
		add_action( 'save_post', [$this, 'generate_shipping_zone'], 10, 3);

		// Remove the shipping zone when deleting a post
		add_action( 'trashed_post', [$this, 'delete_shipping_zone'] );

	}

	private function is_pup( $post_id ) {
		return 'pickup-point' === get_post_type( $post_id );
	}

	public function register_cpt() {
		
		/**
		 * Pick Up Point custom post type
		 */

		$labels = array(
			'name'                => __( 'Pick Up Points', $this->plugin_name ),
			'singular_name'       => __( 'Pick Up Point', $this->plugin_name ),
			'add_new'             => _x( 'Add New Pick Up Point', $this->plugin_name, $this->plugin_name ),
			'add_new_item'        => __( 'Add New Pick Up Point', $this->plugin_name ),
			'edit_item'           => __( 'Edit Pick Up Point', $this->plugin_name ),
			'new_item'            => __( 'New Pick Up Point', $this->plugin_name ),
			'view_item'           => __( 'View Pick Up Point', $this->plugin_name ),
			'search_items'        => __( 'Search Pick Up Points', $this->plugin_name ),
			'not_found'           => __( 'No Pick Up Points found', $this->plugin_name ),
			'not_found_in_trash'  => __( 'No Pick Up Points found in Trash', $this->plugin_name ),
			'parent_item_colon'   => __( 'Parent Pick Up Point:', $this->plugin_name ),
			'menu_name'           => __( 'Pick Up Points', $this->plugin_name ),
		);
		
		$args = array(
			'labels'                   => $labels,
			'hierarchical'        => true,
			'description'         => 'Pick Up Points for Woocommerce Shop',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-location',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array( 'title'	)
		);
		
		register_post_type( 'pickup-point', $args );
		
	}

	public function generate_shipping_zone( $post_id, $post, $update ) {

		if ( $this->is_pup( $post_id ) ) {
			
			global $woocommerce;

			// If:
			// - the post is a revision,
			// - update is true, or
			// - is in trash
			// ... then don't do nuffin
			if ( wp_is_post_revision( $post_id ) || ! $update || get_post_status( $post ) === 'trash' )
				return;

			$available_zones = WC_Shipping_Zones::get_zones();
			
			// If zone with the same title exists, stop
			foreach ($available_zones as $the_zone) {
				if ( $the_zone['zone_name'] === $post->post_title) {
					return;
				}
			}

			// Get existing shipping methods
			

			$shipping_zone = new WC_Shipping_Zone();
			$shipping_zone->set_zone_name($post->post_title);
			$shipping_zone->create();
			$shipping_zone->add_shipping_method( 'local_pickup' );

			// Create a link between the post and the shipping zone
			add_post_meta( $post_id, '_pup_shipping_zone', $shipping_zone->get_id(), true );
		}
	}

	public function delete_shipping_zone( $post_id ) {

		if ( $this->is_pup( $post_id ) ) {
			
			// If the post meta exists...
			if ( $zone_id = get_post_meta( $post_id, '_pup_shipping_zone', true ) ) {
				// Get the associated shipping zone
				WC_Shipping_Zones::delete_zone( (int) $zone_id );

				// Remove the post meta association
				delete_post_meta( $post_id, '_pup_shipping_zone', $zone_id );
			}

		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vbwc_Pick_Up_Points_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vbwc_Pick_Up_Points_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vbwc-pick-up-points-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vbwc_Pick_Up_Points_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vbwc_Pick_Up_Points_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vbwc-pick-up-points-admin.js', array( 'jquery' ), $this->version, false );

	}

}
