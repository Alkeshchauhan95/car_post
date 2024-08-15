<?php
/*
Plugin Name: Custom Post
Description: custom post type create
Author: Alkesh

*/


function car_custom_post_type()
{
    $labels = array('name' => _x('Cars', 'Post Type General Name'),
    'singular_name' => _x('Car', 'Post Type Singular name'),
    'menu_name' => __('Cars'),
    'name_admin_bar' => __('Car'),
    'add_new_item' => __('Add New Car'),
    'edit_item' => __('Edit Car'),
    'new_item'  => __('New Car'),
    'view_item' => __('View Car'),
    'search_items' => __('Search Cars'),
    'not_found' => __('No cars found'),
    'not_found_in_trash' => __('No cars found in Trash'),
    );

    $args = array(
        'label' => __('Car'),
        'description' => __('Car Custom Post Type'),
        'lables' => $labels,
        'supports'  => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'public'  => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',

    );
    register_post_type('car', $args);
}
add_action('init', 'car_custom_post_type', 0);

// texnomany create
function create_car_texnomany()
{
    $labels = array(
        'name' => _x('Makes','texonomy general name'),
        'singular_name' => _x('Make', 'texonomy singular name'),
        'all_items' => __('All Makes'),
        'parents_item' => __('Parent Make'),
        'parent_item_colon' => __('Parent Make'),
        'edit_item' => __('Edit Make'),
        'update_item' => __('Update Make'),
        'add_new_item' => __('Add New Make'),
        'new_item_name' => __('New Make Name'),
        'menu_name' => __('Make'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'make'),
    );
    register_taxonomy('make', array('car'),$args);

     // Model add
     $labels = array(
        'name' => _x('Models','texonomy general name'),
        'singular_name' => _x('Model', 'texonomy singular name'),
        'search_items' => __('Search Model'),
        'all_items' => __('All Model'),
        'edit_item' => __('Edit Model'),
        'update_item' => __('Update Model'),
        'add_new_item' => __('Add New Model'),
        'new_item_name' => __('New Model Name'),
        'menu_name' => __('Model'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'model'),
    );
    register_taxonomy('model', array('car'),$args);

    // Years add
    $labels = array(
        'name' => _x('Years','texonomy general name'),
        'singular_name' => _x('Year', 'texonomy singular name'),
        'search_items' => __('Search Year'),
        'all_items' => __('All Year'),
        'edit_item' => __('Edit Year'),
        'update_item' => __('Update Year'),
        'add_new_item' => __('Add New Year'),
        'new_item_name' => __('New Year Name'),
        'menu_name' => __('Year'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'year'),
    );
    register_taxonomy('year', array('car'),$args);

    // fueal type add
    $labels = array(
        'name' => _x('Fuel Types','texonomy general name'),
        'singular_name' => _x('Fuel Type', 'texonomy singular name'),
        'search_items' => __('Search Fuel Types'),
        'all_items' => __('All Fuel Type'),
        'edit_item' => __('Edit Fuel Type'),
        'update_item' => __('Update Fuel Type'),
        'add_new_item' => __('Add New Fuel Type'),
        'new_item_name' => __('New Fuel Type Name'),
        'menu_name' => __('Fuel Type'),
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'fuel_type'),
    );
    register_taxonomy('fuel_type', array('car'),$args);

}

add_action('init','create_car_texnomany');



// post data
function car_entry_form_shortcode()
{
    if(isset($_POST['car_entry_submit']))
    {
        $car_name = $_POST['car_name'];
        $make = intval($_POST['make']);
        $model = intval($_POST['model']);
        $fuel_type = $_POST['fuel_type'];
        // $fuel_type = sanitize_text_field($_POST['fuel_type']);

        $car_image = $_FILES['car_image'];


        // insert post
        $car_post_id = wp_insert_post(array(
            'post_title' => $car_name,
            'post_type'  => 'car',
            'post_status' => 'publish',
        ));

        // var_dump($fuel_type); die;


        // Assign taxonomies
        wp_set_post_terms($car_post_id, $make, 'make');
        wp_set_post_terms($car_post_id, $model, 'model');
        wp_set_post_terms($car_post_id, $fuel_type, 'fuel_type');

         // upload file
         if ($car_image && !empty($car_image['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload('car_image', $car_post_id);
            if (is_wp_error($attachment_id)) {
                echo 'Error uploading image: ' . $attachment_id->get_error_message();
            } else {
                set_post_thumbnail($car_post_id, $attachment_id);
            }
        }
        
    }


    // Fetch taxonomies for dropdowns and radio buttons
    $makes = get_terms(array('taxonomy' => 'make', 'hide_empty' => false));

    $models = get_terms(array('taxonomy' => 'model', 'hide_empty' => false));

    $fuel_types = get_terms(array('taxonomy' => 'fuel_type', 'hide_empty' => false));


    // var_dump($makes);
    ?>

        <form action="" method="post" enctype="multipart/form-data">

            <label>Car Name :-</label>
            <input type="text" name="car_name" required>

            <br><br>

            <label for=""> Make :- </label>
            <select name="make" id="make">
                <?php
                    foreach($makes as $make)
                    {
                        ?>
                            <option value="<?php echo $make->term_id; ?>"><?php echo $make->name; ?></option>
                        <?php
                    }
                ?>
            </select>
                
            <br><br>

            <label for=""> Model :- </label>
            <select name="model" id="model">
                <?php
                    foreach($models as $model)
                    {
                        ?>
                            <option value="<?php echo $model->term_id; ?>"><?php echo $model->name; ?></option>
                        <?php
                    }
                ?>
            </select>
                
            <br><br>

            <label for=""> Fuel Type :- </label>
                <?php
                    foreach($fuel_types as $fuel_type)
                    {
                        ?>
                            <input type="radio" name="fuel_type" id="fuel_type" value="<?php echo esc_attr($fuel_type->slug); ?>" required>
                            <?php echo $fuel_type->name; ?>
                        <?php
                    }
                ?>
                
            <br><br>

            <label for="car_image"> Upload Image :- </label>
            <input type="file" name="car_image" id="car_image" accept="images/*" required>

            <br><br>

            <input type="submit" name ="car_entry_submit" value="Submit car">

        </form>

    <?php
    return ob_get_clean();

}
add_shortcode('car_entry', 'car_entry_form_shortcode');


// default page create

function car_form_page()
{
	if ( !get_option('custom_form_page'))
	{
		$curr_page = array(
					'post_title' => __('Car Form Page'),
					'post_content' => '[car_entry]',
					'post_status' => 'publish',
					'post_type' => 'page',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_category' => array(1),
					'post_parent' => 0 );
		$curr_created = wp_insert_post( $curr_page );
		update_option( 'custom_form_page', $curr_created );
	}
}
add_action('init','car_form_page');


?>