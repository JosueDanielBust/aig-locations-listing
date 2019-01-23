<?php 
defined( 'ABSPATH' ) or die( '404 - Not found!' );

/*
Plugin Name:  AIG - Locations Listing
Plugin URI:   https://github.com/adviceinteractivegroup/restoration1_locations_wpplugin
Description:  Create Locations listing with Store Locator data via Rest API
Version:      1.0
Author:       Josue Daniel Bustamante
Author URI:   http://josuedanielbust.com/
License:      MIT
License URI:  https://github.com/adviceinteractivegroup/restoration1_locations_wpplugin/blob/master/LICENSE.md
Text Domain:  aig-locations-listing
Domain Path:  /languages
*/

#region API_Data
$endpoint   =   'https://store-locator.lssdev.com/api/v1/R1_location_api';
$token      =   'VJbldi9KMCBeeNRxatK/XVYUNgy';
#endregion
$error      =   'Is not possible to retrieve this information right now, please try again.';

require_once('states.php');

#region Register_Post_Type
//      Register data structure and post type on Wordpress Core
function create_aig_location_post_type() {
    register_post_type('location',
    array(
        'labels' => array(
            'name'                  =>  __( 'Locations' ),
            'singular_name'         =>  __( 'Location' ),
            'menu_name'             =>  __( 'Store Locator' ),
            'name_admin_bar'        =>  __( 'Locations'),
            'add_new'               =>  __( 'Add New', 'location' ),
            'add_new_item'          =>  __( 'Add new Location' ),
            'new_item'              =>  __( 'New location' ),
            'edit_item'             =>  __( 'Edit location' ),
            'view_item'             =>  __( 'View location' ),
            'all_items'             =>  __( 'All locations' ),
            'search_items'          =>  __( 'Search location' ),
            'parent_item_colon'     =>  __( 'Parent Location:' ),
            'not_found'             =>  __( 'No location found.' ),
            'not_found_in_trash'    =>  __( 'No location found in trash.' )
        ),
        'public'        =>  false,
        'show_in_rest'  =>  false,
        'hierarchical'  =>  false,
        'has_archive'   =>  false,
        'rewrite'       =>  true,
        'show_ui'       =>  true,
        'rest_base'     =>  'location',
        'menu_icon'     =>  'dashicons-location-alt',
        'rewrite'       =>  array('slug' => 'location'),
        'taxonomies'    =>  array( 'locations' ),
        'supports'      =>  array( 'title', 'thumbnail' ),
        )
    );
}
function create_aig_location_taxonomy() {
    register_taxonomy(
        'locations',
        'location',
        array(
            'public'            =>  false,
            'show_ui'           =>  true,
            'hierarchical'      =>  true,
            'label'             =>  __( 'States' ),
        )
    );
}
add_action( 'init', 'create_aig_location_post_type' );
add_action( 'init', 'create_aig_location_taxonomy' );
add_action( 'registered_taxonomy', 'create_states_as_categories', 10, 2 );
register_taxonomy_for_object_type( 'locations', 'location' );
#endregion

#region Register_Custom_Metadata
//      Register custom metadata for custom post type
add_action( 'admin_init', 'aig_add_metadata' );
add_action( 'save_post', 'aig_save_metadata' );

function aig_add_metadata(){
    add_meta_box( 'aig_location_metadata', 'Information', 'aig_location_options', 'location', 'normal', 'high');
}

function aig_location_options(){
    global $post;
    $custom         =   get_post_custom( $post->ID );
    $parent_lp      =   $custom[ 'aig_parent_landing_page' ][0];
    $parent_pc      =   $custom[ 'aig_parent_postal_code' ][0];

    ?>
    <div class="aig-metadata-groups">
        <div class="aig-control-group">
            <label for="aig_parent_landing_page">Location Landing Page</label>
            <?php
                $args = array(
                    'depth'                 =>  1,
                    'selected'              =>  $parent_lp,
                    'echo'                  =>  1,
                    'name'                  =>  'aig_parent_landing_page',
                    'id'                    =>  'aig_parent_landing_page',
                    'value_field'           =>  'ID',
                );
                wp_dropdown_pages( $args );
            ?>
        </div>
        <div class="aig-control-group">
            <label for="aig_parent_postal_code">Parent Postal Code</label>
            <input type="text" name="aig_parent_postal_code" id="aig_parent_postal_code" value="<?php echo $parent_pc; ?>" />
        </div>
    </div>
    <?php
}

function aig_save_metadata(){
    global $post;
    update_post_meta( $post->ID, 'aig_parent_postal_code', $_POST[ 'aig_parent_postal_code' ] );
    update_post_meta( $post->ID, 'aig_parent_landing_page', $_POST[ 'aig_parent_landing_page' ] );
}
#endregion

#region Register_Admin_Panel
//      Register administration screen panel
add_action( 'admin_menu', 'aig_locations_menu' );
function aig_locations_menu() {
	add_menu_page( 'AIG Locations', 'AIG Locations', 'aig_locations_manage_options', 'location', 'aig_locations_options' );
}

add_filter( 'manage_aig_locations_edit_columns', 'aig_locations_edit_columns' );
add_action( 'manage_aig_locations_custom_columns',  'aig_locations_custom_columns' );

function aig_locations_edit_columns($columns){
    unset(
        $columns['categories']
	);
    $new_columns = array(
        'title'     =>  'Location',
        'category'  =>  'State',
        'plp'       =>  'Parent LP',
        'pcp'       =>  'Parent CP',
    );
    return array_merge($columns, $new_columns);
}
function aig_locations_custom_columns($column){
    global $post;
    switch ($column) {
        case 'category':
            echo get_the_term_list($post->ID, 'locations', '', ', ',''); 
            break;
        case 'plp':
            $custom = get_post_custom();
            echo $custom[ 'aig_parent_landing_page' ][0];
            break;
        case 'pcp':
            $custom = get_post_custom();
            echo $custom[ 'aig_parent_postal_code' ][0];
            break;
    }
}
#endregion

#region Enqueue_Scripts
//      Enqueue CSS files for administration screen panel
function aig_locations_custom_admin_styles($hook) {
    if($hook != ('post-new.php' || 'post.php' )) { return; }
    wp_enqueue_style( 'aig_locations_custom_admin_css', plugins_url('/css/admin.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'aig_locations_custom_admin_styles' );

function aig_locations_custom_scripts() {
    wp_enqueue_style( 'aig_locations_custom_css', plugins_url('/css/main.css', __FILE__) );
    wp_enqueue_script( 'aig_locations_custom_js', plugins_url('/js/main.js', __FILE__), array(), null, true );
}
add_action( 'wp_enqueue_scripts', 'aig_locations_custom_scripts' );
#endregion

#region Utils
//      Get Data from Store Locator API
function get_api_data( $zips ) {
    $response = wp_remote_post( $GLOBALS['endpoint'], array( 'headers' => array( 'AccessToken' => $GLOBALS['token'], 'Content-Type' => 'application/json'), 'body' => $zips ) );
    $body = $response['body'];
    $jsondata = json_decode($body, true);
    if ( $jsondata['overallStatusCode'] == 200 ) { return $jsondata['result']; } else { return '404'; }
}
function format_number( $phone ) {
    return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
}
function capitalize( $string ) {
    return ucwords( strtolower( $string ) );
}
#endregion

#region Register_Shortcodes
//      Register shortcodes
//      [locations category="category" slug="category-slug"]
function aig_listing_generator( $category, $slug ) {
    $args = array(
        'post_type'     =>  'location',
        'orderby'       =>  'title',
        'order'         =>  'ASC',
        'nopaging'      =>  'true',
        'tax_query'     =>  array(
            array(
                'taxonomy'  =>  'locations',
                'field'     =>  'slug',
                'terms'     =>  $category,
            ),
        ),
    );
    $the_query = new WP_Query( $args );

    if ( $the_query->have_posts() ) {

        // Getting custom zips from plugin and calling API Query
        $zips = '{"zipscodes":[';
        while ( $the_query->have_posts() ) : $the_query->the_post();
            $custom = get_post_custom( get_the_ID() );
            $zips  .= '"' . $custom[ 'aig_parent_postal_code' ][0] . '",';
        endwhile;
        $zips = substr($zips, 0, -1) . ']}';
        $data = get_api_data( $zips );

        if ( $data == '404' ) {
            ?><h4 class="locations-error"><?php echo $GLOBALS['error'] ?></h4><?php
            wp_reset_postdata();
            return;
        }

        ?>
        <div class="locations-listing <?php echo $slug; ?>">
            <div class="location-title">
                <div class="state-flag state-flag-<?php echo $slug; ?>"></div>
                <h2><?php echo $category; ?></h2>
                <div class="location-logo"></div>
            </div>
            <div class="locations">
                <?php
                while ( $the_query->have_posts() ) : $the_query->the_post();
                    $custom             =   get_post_custom( get_the_ID() );
                    $cp                 =   $custom[ 'aig_parent_postal_code' ][0];
                    $areas              =   $data[ $cp ]['Cities'];
                    $status             =   $data[ $cp ]['status'];
                    $location_name      =   $data[ $cp ]['Name'];
                    $location_addresses =   $data[ $cp ]['Addresses'];

                    $cities = '';
                    foreach ($areas as $key => $city) { $cities .= $city . ', '; }
                    $cities = capitalize( substr($cities, 0, -2) );

                    ?>
                    <div class="location-item">
                        <?php if ( $status != '404' ) { ?>
                            <h3>
                                <a href="<?php echo get_page_link( $custom[ 'aig_parent_landing_page' ][0] )  ?>"><?php echo $location_name ?></a>
                            </h3>
                            <div class="business-info">
                                <?php foreach ($location_addresses as $address) { 
                                    if ( $address['Address'] != ', ,  ' || $address['Phone'] != '' ) { ?>
                                        <p class="aig-icon address aig-icon-address"><?php echo $address['Address'] ?></p>
                                        <p class="aig-icon phone aig-icon-phone">
                                            <a href="tel:<?php echo $address['Phone'] ?>"><?php echo format_number($address['Phone']) ?></a>
                                        </p>
                                <?php }
                                }
                                $website = get_post_meta( $custom[ 'aig_parent_landing_page' ][0], 'cmb_home_location_website', true );
                                if ( $website ) { ?>
                                    <p class="aig-icon site aig-icon-web">
                                        <a href="<?php echo $website; ?>">Visit Website</a>
                                    </p>
                                <?php } ?>
                            </div>
                            <div class="service-info">
                                <h4>Common Areas Serviced</h4>
                                <div>
                                    <?php if( sizeof( $areas ) > 5 ) {
                                        $first_cities = '';
                                        for ($i = 0; $i <= 4; $i++) { $first_cities .= $areas[$i] . ', '; }
                                        $first_cities = capitalize( substr($first_cities, 0, -2) );
                                    ?>
                                        <p>
                                            <?php echo $first_cities ?>...
                                            <a href="#modal" onclick="toggle_modal(this)" l-name="<?php echo $location_name ?>" l-areas="<?php echo $cities ?>">Click here for more</a>
                                        </p>
                                    <?php } else { ?>
                                        <p><?php echo $cities ?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <h4 class="locations-error" data="<?php echo get_post_field( 'post_name', $custom[ 'aig_parent_landing_page' ][0] );  ?>">
                                We're sorry, Is not possible to retrieve this information right now.
                            </h4>
                        <?php } ?>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        </div>
        <?php }
}
function aig_listing_modal() {
    ?>
        <div class="locations-modal">
            <div class="locations-modal-content">
                <h3></h3>
                <h4>Common Areas Serviced</h4>
                <p class="areas"></p>
                <a href="#modal" onclick="toggle_modal()"><p>Click here for less</p></a>
            </div>
        </div>
    <?php
}
function locations_listing_shortcode( $atts ) {
    $a = shortcode_atts( array(
        'category'  =>  'all',
        'slug'      =>  ''
    ), $atts );

    if ( $a[ 'category' ] !== 'all' ) {
        aig_listing_generator( $a[ 'category' ], $a[ 'slug' ] );
    } else {
        foreach ($GLOBALS['states'] as $state => $slug) {
            aig_listing_generator( $state, $slug );
        }
    }
    aig_listing_modal();
};

//      [locations-states category="category" slug="category-slug"]
function aig_states_listing_generator( $category, $slug ) {
    $args = array(
        'post_type'     =>  'location',
        'tax_query'     =>  array(
            array(
                'taxonomy'  =>  'locations',
                'field'     =>  'slug',
                'terms'     =>  $category,
            ),
        ),
    );
    $the_query = new WP_Query( $args );

    if ( $the_query->have_posts() ) {
        echo '<option value="' . $slug .'" class="' . $slug .'">'. $category .'</option>';
        wp_reset_postdata();
    }
}
function states_listing_shortcode() {
    $a = shortcode_atts( array(
        'category'  =>  'all',
        'slug'      =>  ''
    ), $atts );

    if ( $a[ 'category' ] !== 'all' ) {
        aig_states_listing_generator( $a[ 'category' ], $a[ 'slug' ] );
    } else {
        foreach ($GLOBALS['states'] as $state => $slug) {
            aig_states_listing_generator( $state, $slug );
        }
    }
}
add_shortcode( 'locations', 'locations_listing_shortcode' );
add_shortcode( 'locations-states', 'states_listing_shortcode' );
#endregion
?>
