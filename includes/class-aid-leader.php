<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       aid.sg
 * @since      1.0.0
 *
 * @package    Aid_Leader
 * @subpackage Aid_Leader/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Aid_Leader
 * @subpackage Aid_Leader/includes
 * @author     Florian Bentele <fb@aid.sg>
 */
class Aid_Leader {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Aid_Leader_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_VERSION' ) ) {
			$this->version = PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'aid-leader';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_post_types();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Aid_Leader_Loader. Orchestrates the hooks of the plugin.
	 * - Aid_Leader_i18n. Defines internationalization functionality.
	 * - Aid_Leader_Admin. Defines all hooks for the admin area.
	 * - Aid_Leader_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aid-leader-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aid-leader-i18n.php';

		/**
		 * The class resonsible for midata api handling
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aid-leader-scoutdb.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aid-leader-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-aid-leader-public.php';

		$this->loader = new Aid_Leader_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Aid_Leader_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Aid_Leader_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Aid_Leader_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Aid_Leader_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	private function define_post_types() {
		add_action( 'init', 'aid_leader_custom_taxonomy', 0 );
		add_action( 'restrict_manage_posts', 'aid_leader_admin_posts_filter_restrict_manage_posts' );
		add_action( 'init', 'aid_leader_custom_post_type', 0 );
		add_filter( 'parse_query', 'aid_leader_posts_filter' );
		add_action( 'add_meta_boxes', 'aid_leader_add_events_metaboxes' );
		add_action( 'save_post', 'aid_leader_save_qualification', 1, 2 );
		add_action( 'admin_menu', 'aid_leader_settings_page' );
		add_action( 'wp_ajax_sync_midata', 'aid_leader_sync_midata' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Aid_Leader_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}


// Register Custom Post Type
function aid_leader_custom_post_type() {
	$labels = array(
		'name'                  => _x( 'Leiter', 'Post Type General Name', 'aid_leader' ),
		'singular_name'         => _x( 'Leiter', 'Post Type Singular Name', 'aid_leader' ),
		'menu_name'             => __( 'Leiter', 'aid_leader' ),
		'name_admin_bar'        => __( 'Leiter', 'aid_leader' ),
		'archives'              => __( 'Leiter', 'aid_leader' ),
		'attributes'            => __( 'Item Attributes', 'aid_leader' ),
		'parent_item_colon'     => __( 'Parent Item:', 'aid_leader' ),
		'all_items'             => __( 'Alle Leiter', 'aid_leader' ),
		'add_new_item'          => __( 'Neuer Leiter', 'aid_leader' ),
		'add_new'               => __( 'Neuer Leiter', 'aid_leader' ),
		'new_item'              => __( 'New Item', 'aid_leader' ),
		'edit_item'             => __( 'Edit Item', 'aid_leader' ),
		'update_item'           => __( 'Update Item', 'aid_leader' ),
		'view_item'             => __( 'View Item', 'aid_leader' ),
		'view_items'            => __( 'View Items', 'aid_leader' ),
		'search_items'          => __( 'Search Item', 'aid_leader' ),
		'not_found'             => __( 'Not found', 'aid_leader' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'aid_leader' ),
		'featured_image'        => __( 'Featured Image', 'aid_leader' ),
		'set_featured_image'    => __( 'Set featured image', 'aid_leader' ),
		'remove_featured_image' => __( 'Remove featured image', 'aid_leader' ),
		'use_featured_image'    => __( 'Use as featured image', 'aid_leader' ),
		'insert_into_item'      => __( 'Insert into item', 'aid_leader' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'aid_leader' ),
		'items_list'            => __( 'Items list', 'aid_leader' ),
		'items_list_navigation' => __( 'Items list navigation', 'aid_leader' ),
		'filter_items_list'     => __( 'Filter items list', 'aid_leader' ),
	);
	$args   = array(
		'label'               => __( 'Leiter', 'aid_leader' ),
		'description'         => __( 'Leiter', 'aid_leader' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'revisions' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-admin-users',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
	);
	register_post_type( 'aid_leader', $args );
}

// Register Custom Taxonomy
function aid_leader_custom_taxonomy() {
	$labels = array(
		'name'                       => _x( 'Kurse', 'Taxonomy General Name', 'aid_leader' ),
		'singular_name'              => _x( 'Kurs', 'Taxonomy Singular Name', 'aid_leader' ),
		'menu_name'                  => __( 'Kurse', 'aid_leader' ),
		'all_items'                  => __( 'Alle Kurse', 'aid_leader' ),
		'parent_item'                => __( 'Parent Item', 'aid_leader' ),
		'parent_item_colon'          => __( 'Parent Item:', 'aid_leader' ),
		'new_item_name'              => __( 'New Item Name', 'aid_leader' ),
		'add_new_item'               => __( 'Add New Item', 'aid_leader' ),
		'edit_item'                  => __( 'Edit Item', 'aid_leader' ),
		'update_item'                => __( 'Update Item', 'aid_leader' ),
		'view_item'                  => __( 'View Item', 'aid_leader' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'aid_leader' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'aid_leader' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'aid_leader' ),
		'popular_items'              => __( 'Popular Items', 'aid_leader' ),
		'search_items'               => __( 'Search Items', 'aid_leader' ),
		'not_found'                  => __( 'Not Found', 'aid_leader' ),
		'no_terms'                   => __( 'No items', 'aid_leader' ),
		'items_list'                 => __( 'Items list', 'aid_leader' ),
		'items_list_navigation'      => __( 'Items list navigation', 'aid_leader' ),
	);
	$args   = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
	);
	register_taxonomy( 'course', array( 'aid_leader' ), $args );
}

function aid_leader_admin_posts_filter_restrict_manage_posts() {
	$type = 'post';
	if ( isset( $_GET['post_type'] ) ) {
		$type = $_GET['post_type'];
	}

	if ( 'aid_leader' == $type ) {
		$values = array(
			'Experte'    => 'experte',
			'Coaches'    => 'coach',
			'Kursleiter' => 'kursleiter',
		);
		?>
        <select name="aid_leader_funktion">
            <option value=""><?php _e( 'Filter By ', 'wose45436' ); ?></option>
			<?php
			$current_v = isset( $_GET['aid_leader_funktion'] ) ? $_GET['aid_leader_funktion'] : '';
			foreach ( $values as $label => $value ) {
				printf
				(
					'<option value="%s"%s>%s</option>',
					$value,
					$value == $current_v ? ' selected="selected"' : '',
					$label
				);
			}
			?>
        </select>
		<?php
	}
}

function aid_leader_posts_filter( $query ) {
	global $pagenow;
	$type = 'post';
	if ( isset( $_GET['post_type'] ) ) {
		$type = $_GET['post_type'];
	}
	if ( 'aid_leader' == $type && is_admin() && $pagenow == 'edit.php' && isset( $_GET['aid_leader_funktion'] ) && $_GET['aid_leader_funktion'] != '' ) {
		$query->query_vars['meta_key']   = $_GET['aid_leader_funktion'];
		$query->query_vars['meta_value'] = "true";
		$query->query_vars['post_type']  = 'aid_leader';
	}
}

function aid_leader_add_events_metaboxes() {
	add_meta_box( 'aid_leader_qualification', 'Qualifikationen', 'aid_leader_qualification', 'aid_leader', 'side', 'default' );
	add_meta_box( 'aid_leader_stammdaten', 'Stammdaten', 'aid_leader_stammdaten', 'aid_leader', 'advanced', 'high' );
}

function aid_leader_stammdaten() {
	global $post;
	?>
    <div class="aid-leader-stammdaten">
        <label for="midataid">Midata ID:</label><input id="midataid" type="text" name="midataid" value="<?php echo get_post_meta( $post->ID, 'midataid', true ); ?>"><br>
        <label for="changed">Zuletzt geändert:</label><input id="changed" type="text" name="changed" disabled="disabled"
                                                             value="<?php echo get_post_meta( $post->ID, 'changed', true ); ?>"><br>
        <label for="firstname">Vorname:</label><input id="firstname" type="text" name="firstname" value="<?php echo get_post_meta( $post->ID, 'firstname', true ); ?>"><br>
        <label for="lastname">Nachname:</label><input id="lastname" type="text" name="lastname" value="<?php echo get_post_meta( $post->ID, 'lastname', true ); ?>"><br>
        <label for="vulgo">Vulgo:</label><input id="vulgo" type="text" name="vulgo" value="<?php echo get_post_meta( $post->ID, 'vulgo', true ); ?>"><br>
        <label for="abteilung">Abteilung:</label><input id="abteilung" type="text" name="abteilung" value="<?php echo get_post_meta( $post->ID, 'abteilung', true ); ?>"><br>
        <label for="funktion">Ehem. Funktion:</label><input id="funktion" type="text" name="funktion" value="<?php echo get_post_meta( $post->ID, 'funktion', true ); ?>"><br>
        <label for="email">E-Mail:</label><input id="email" type="text" name="email" value="<?php echo get_post_meta( $post->ID, 'email', true ); ?>"><br>
        <label for="anfragedurch">Anfrage durch:</label><input id="anfragedurch" type="text" name="anfragedurch"
                                                               value="<?php echo get_post_meta( $post->ID, 'anfragedurch', true ); ?>"><br>
        <label for="anfrageam">Anfrage am:</label><input id="anfrageam" type="text" name="anfrageam" value="<?php echo get_post_meta( $post->ID, 'anfrageam', true ); ?>"><br>
        <label for="antwort">Antwort:</label><br>
        <textarea id="antwort" name="antwort"><?php echo get_post_meta( $post->ID, 'antwort', true ); ?></textarea><br>
        <label for="bemerkung">Bemerkung:</label><br>
        <textarea id="bemerkung" name="bemerkung"><?php echo get_post_meta( $post->ID, 'bemerkung', true ); ?></textarea><br>
    </div>
	<?php
}

function aid_leader_qualification() {
	global $post;
	echo '<input type="hidden" name="aid_leader_qualification_nonce" id="aid_leader_qualification_nonce" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	$kursleiter = get_post_meta( $post->ID, 'kursleiter', true );
	echo '<input type="hidden" name="kursleiter" value="false" class="widefat" />';
	echo '<input id="aid_kl" type="checkbox" ' . ( $kursleiter == 'true' ? 'checked' : '' ) . ' name="kursleiter" value="true" class="widefat" /><label for="aid_kl">Kursleiter/in</label><br>';
	$coach = get_post_meta( $post->ID, 'coach', true );
	echo '<input type="hidden" name="coach" value="false" class="widefat" />';
	echo '<input id="aid_coach" type="checkbox" ' . ( $coach == 'true' ? 'checked' : '' ) . ' name="coach" value="true" class="widefat" /><label for="aid_coach">Coach </label><br>';
	$experte = get_post_meta( $post->ID, 'experte', true );
	echo '<input type="hidden" name="experte" value="false" class="widefat" />';
	echo '<input id="aid_exp" type="checkbox" ' . ( $experte == 'true' ? 'checked' : '' ) . ' name="experte" value="true" class="widefat" /><label for="aid_exp">Experte/Expertin</label><br>';
}

function aid_leader_save_qualification( $post_id, $post ) {
	if ( isset( $_POST['aid_leader_qualification_nonce'] ) ) {
		if ( ! wp_verify_nonce( $_POST['aid_leader_qualification_nonce'], plugin_basename( __FILE__ ) ) ) {
			return $post->ID;
		}
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $post->ID;
		}
		update_post_meta( $post->ID, 'kursleiter', $_POST['kursleiter'] );
		update_post_meta( $post->ID, 'coach', $_POST['coach'] );
		update_post_meta( $post->ID, 'experte', $_POST['experte'] );
		update_post_meta( $post->ID, 'midataid', $_POST['midataid'] );
		update_post_meta( $post->ID, 'changed', $_POST['changed'] );
		update_post_meta( $post->ID, 'firstname', $_POST['firstname'] );
		update_post_meta( $post->ID, 'lastname', $_POST['lastname'] );
		update_post_meta( $post->ID, 'vulgo', $_POST['vulgo'] );
		update_post_meta( $post->ID, 'abteilung', $_POST['abteilung'] );
		update_post_meta( $post->ID, 'funktion', $_POST['funktion'] );
		update_post_meta( $post->ID, 'email', $_POST['email'] );
		update_post_meta( $post->ID, 'anfragedurch', $_POST['anfragedurch'] );
		update_post_meta( $post->ID, 'anfrageam', $_POST['anfrageam'] );
		update_post_meta( $post->ID, 'antwort', $_POST['antwort'] );
		update_post_meta( $post->ID, 'bemerkung', $_POST['bemerkung'] );

		remove_action( 'save_post', 'aid_leader_save_qualification', 1, 2 );
		$post->post_title = $_POST['firstname'] . ' ' . $_POST['lastname'] . ' v/o ' . $_POST['vulgo'];
		wp_update_post( $post );
		add_action( 'save_post', 'aid_leader_save_qualification', 1, 2 );
	}
}

function aid_leader_settings_page() {
	add_submenu_page( 'edit.php?post_type=aid_leader', 'Leiter', 'Einstellungen', 'manage_options', 'leader-settings', 'aid_leader_settings_page_html' );
}

function aid_leader_settings_page_html() {
	if ( ! defined( 'AID_LEADER_API_USER' ) || ! defined( 'AID_LEADER_API_KEY' ) ) {
		echo '<p>API User oder Key ist nicht konfiguriert</p><p>Bitte folgende Config im wp-config.php ergänzen</p>';
		echo "<pre>define( 'AID_LEADER_API_USER', 'my_api_user@scout.ch');
define( 'AID_LEADER_API_KEY', 'my-random-api-key');</pre>";
	} else {
		?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Leiter - Einstellungen</h1>
            <a id="aid-sync-midata" href="#" class="page-title-action">Midata Synchronisieren</a>
            <hr class="wp-header-end">
            <h2 class="screen-reader-text">Filter items list</h2>
            <div id="ajax-response"></div>
            <br class="clear">
        </div>
		<?php
	}
}

function aid_leader_sync_midata() {
	$db      = new Scout_DB();
	$people  = $db->getGroups();
	$updated = array();
	$created = array();

	foreach ( $people as $person ) {
		if ( isset( $person['id'] ) ) {
			$leader = aid_get_leader_by_midataid( $person['id'] );
			if ( $leader ) {
				// user exists in our db
				update_post_meta( $leader->ID, 'email', $person['email'] );
                // ToDo: update all meta fields, same as above

                // ToDo: only add the items to the $updated array if something really has changed
				$updated[] = array( 'midataid' => $person['id'], 'first_name' => $person['first_name'], 'last_name' => $person['last_name'], 'nickname' => $person['nickname'] );
			} else {
				// user does not exists, creating
				$new = array(
					'post_title' => $person['first_name'] . ' ' . $person['last_name'] . ' v/o ' . $person['nickname'],
					'post_type'  => 'aid_leader',
					'meta_input' => array(
						'firstname' => $person["first_name"],
						'lastname'  => $person["last_name"],
						'midataid'  => $person["id"],
						'vulgo'     => $person["nickname"]
					)
				);
				wp_insert_post( $new );
				$created[] = array( 'midataid' => $person['id'], 'first_name' => $person['first_name'], 'last_name' => $person['last_name'], 'nickname' => $person['nickname'] );;
			}
		}
	}
	$response = array( 'size' => count( $people ), 'created' => $created, 'updated' => $updated );

	echo json_encode( $response );

	wp_die();
}

function aid_get_leader_by_midataid( $midataid ) {
	$args  = array(
		'post_type'  => 'aid_leader',
		'meta_query' => array(
			array(
				'key'     => 'midataid',
				'value'   => $midataid,
				'compare' => '=',
			)
		)
	);
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		return reset( $query->posts );
	} else {
		return false;
	}
}
