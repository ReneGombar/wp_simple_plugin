<?php
/*
 * Plugin Name: Simple LMS Plugin
 * Description: Learning management system plugin 
 * Version: 1.0
 * Author: Rene
 * Author URI: https://www.simplelms.com
 */

/**
 * Register a custom post type called "Courses".
 *
 * @see get_post_type_labels() for label keys.
 */
function course_cpt() {
	$labels = array(
		'name'                  => _x( 'Courses', 'Post type general name', 'textdomain' ),
		'singular_name'         => _x( 'Course', 'Post type singular name', 'textdomain' ),
		
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'course' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor',  'thumbnail' ),
        'menu_icon'         =>"dashicons-database-view"
	);
	register_post_type( 'courses', $args );
}
add_action( 'init', 'course_cpt' );

/**ADD CUSTOM FIELDS */
function CF_Courses_Main(){
    add_meta_box(
        "cf_course_id",         // ID of the custom field
        "Course Custom Fields", // Title of the custom field
        "CF_Courses",           //! Function call to the below function
        "courses",              // this is the name of the custom post type that is registered with register_post_type above
        "normal",               // Priority - location of our custom fields (below or side)
        "low",                  // Priority - position of the custom fields (up/down)
    ); 
}

function CF_Courses(){
    
    wp_head(); //! MUST BE HERE when including css files, js files
    
    global  $wpdb;  //access the db
    $thisID = get_the_ID();     //retrieve  the ID of the current post

    $ourdb = $wpdb->prefix."lms_course_details";        //variable that holds the full table name
    
    $price = $wpdb->get_var("SELECT `price` FROM `$ourdb` WHERE `ID` = $thisID ");   //! query the DB to return the `price` of the matching ID
    $subtitle = $wpdb->get_var("SELECT `subtitle` FROM `$ourdb` WHERE `ID` = $thisID ");   //! query the DB to return the `subtitle` of the matching ID
    $curriculum = $wpdb->get_var("SELECT `curriculum` FROM `$ourdb` WHERE `ID` = $thisID ");   //! query the DB to return the `curriculum` of the matching ID
    $video = $wpdb->get_var("SELECT `video` FROM `$ourdb` WHERE `ID` = $thisID ");   //! query the DB to return the `subtitle` of the matching ID

    echo "OurDB:".$ourdb.". Post ID: ".$thisID." Price is: ".$price;

    ?>
    <div class='courseContainer'>
        <div class='fieldCountainer'>
            <h6>Course Subtitle</h6>
            <input type="text" name="subtitle" value="<?php echo $subtitle; ?>">
        </div>
        <div class='fieldCountainer'>
            <h6>Course Price</h6>
            <input type="text" name="price" value="<?php echo $price; ?>">
        </div>
        <div class='fieldCountainer'>
            <h6>Video Trailer</h6>
            <input type="text" name="video" value="<?php echo $video; ?>" >
        </div>
        <div class='fieldCountainer'>
            <h6>Curriculum</h6>
            <input type="text" name="curriculum" value="<?php echo $curriculum; ?>">
        </div>
    </div>
    <?php
    
}

add_action('admin_init','CF_Courses_Main');


/**ADD CSS FILE IN OUR PLUGIN  */
function add_style(){
    $style_url = plugin_dir_url(__FILE__).'scripts/style.css';  //returns the relative url of the style.css
    $style_file_path = plugin_dir_path(__FILE__).'scripts/style.css';   //returns the local path of the style.css for style updates
    
    //TODO wp_register_style('style',plugin_dir_url(__FILE__).'scripts/style.css');    //registers the style file based on its location from __FILE__
            // __FILE__ returns full path and name that is being executed and .'scripts/style.css' appends this to the path

    wp_enqueue_style('style',$style_url, array(), filemtime($style_file_path), false );
}

add_action('wp_enqueue_scripts','add_style');


/**LOAD COURSE TEMPLATE from a file template_courses.php*/ 
// function to display our custom post on the client page
function template_courses($template){
    global $post;

    //check if using course post type is equal to 'courses' and find the file 
    if('courses' === $post->post_type && locate_template( array('template_courses') ) !== $template)
    {
        
        return plugin_dir_path(__fiLE__).'templates/template_courses.php';
    }
    return $template;
}

add_filter('single_template','template_courses');

/** Create DATABASE TABLE - Course_details*/
// only create table when activating the plugin
function database_table(){
    global $wpdb;       //global wordpress database

    $database_table_name = $wpdb->prefix."lms_course_details";      //crate new databse table with wordpress prefix
    $charset = $wpdb->get_charset_collate();
    $course_det = "CREATE TABLE $database_table_name (
        ID      int(9) NOT NULL,            
        title   text(100) NOT NULL,        
        subtitle text(500) NOT NULL,
        video varchar(100) NOT NULL,
        price float(5) NOT NULL,
        thumbnail text NOT NULL,
        curriculum text NOT NULL,
        PRIMARY KEY (ID)
    ) $charset; ";
    require_once(ABSPATH."wp-admin/includes/upgrade.php");
    dbDelta($course_det);
}

register_activation_hook(__FILE__,'database_table');


/**SAVE DATA INTO DATABASE */
// function to save information into database
function save_custom_fields(){
    global $wpdb;       //get access to the wordpress database

    $ID = get_the_ID();         // returns the id of the post
    $title = get_the_title();   // returns the title of the post
    
    
    // retrieve information from the global variable _POST after the form was submited with an update button
    //$price = $_POST["price"];           
    
    $price = array_key_exists('price',$_POST) ? $_POST['price'] : 0.0;
    $subtitle = array_key_exists('subtitle',$_POST) ? $_POST['subtitle'] : '';
    $video = array_key_exists('video',$_POST) ? $_POST['video'] : '';
    $curriculum = array_key_exists('curriculum',$_POST) ? $_POST['curriculum'] : '';

    // use built in insert function to add a new item into the table, use:  insert(tableName, [columns,])
    $wpdb->insert(
        $wpdb->prefix.'lms_course_details',   //database table with prefix
        [
            'ID' => $ID,
        ]
    );

    // use built in function update to update existing item. Use: update( table, [rows to update,], [matching criteria e.g.ID]);
    $wpdb->update(
        $wpdb->prefix.'lms_course_details',   //database table with prefix
        [
            'title' => $title,
            'price' => floatval($price),    //convert string to float
            'subtitle' => $subtitle,
            'curriculum' => $curriculum,
            'video' => $video,
        ],
        [
            'ID' => $ID,
            
        ],
    );
}

add_action('save_post','save_custom_fields');

/**
 * LOAD ARCHIVE TEMPLATE
 */

function template_archive(){
    if (is_archive()){
        return plugin_dir_path(__FILE__).'templates/template_archive.php';
    }
    return $template;
}

add_filter('template_include','template_archive');