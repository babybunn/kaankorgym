<?php
/**
* Functions and definitions
*
*/



function kkg_enqueue_styles() {
    $parenthandle = 'twentytwenty-style';
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', array(), $theme->parent()->get('Version') );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( $parenthandle ), $theme->get('Version') );

    wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/assets/js/main.js' , array( 'jquery' ), '', true );
}
add_action( 'wp_enqueue_scripts', 'kkg_enqueue_styles' );

// add_role( 'member', 'Member', array( 'read' => true, 'level_0' => true ) );
// add_role( 'trainer', 'Trainer', array( 'read' => true, 'level_0' => true ) );
// remove_role('member');
// remove_role('trainer');

//add columns to User panel list page
function add_user_columns($column) {
    $column['registered_course'] = 'Registered Course';
    $column['registered_date'] = 'Registered Date';
    return $column;
}
add_filter( 'manage_users_columns', 'add_user_columns' );

//add the data
function add_user_column_data( $val, $column_name, $user_id ) {
    $user = get_userdata($user_id);

    switch ($column_name) {
        case 'registered_course' :
            return $user->registered_course;
            break;
        case 'registered_date' :
            return $user->user_registered;
            break;
        default:
    }
    return;
}
add_filter( 'manage_users_custom_column', 'add_user_column_data', 10, 3 );

function kkg_display_course_lists( $atts, $content, $shortcode_tag ){
    $options = shortcode_atts( array(
        'register_page_id' => '',
        'show_register'    => 1,
        'limit'            => -1,
        'specific_id'      => ''
    ), $atts );
    $register_page = get_permalink( $options['register_page_id'] );
    $show_register = $options['show_register'];

    

    if ( isset( $_GET['course'] ) ) {
        $course_query = $_GET['course'];
        $options['specific_id'] = $course_query;
    }
    
    if ( $show_register === 'false' ) $show_register = false; // just to be sure...
    $show_register = (bool) $show_register;
    
    $args = array(
        'post_type'      => 'courses',
        'posts_per_page' => $options['limit'],
        'publish_status' => 'published',
        'p'              => $options['specific_id']
    );
    
    $query = new WP_Query( $args );
    $result = '<div class="course-list-wrapper">';
    if( $query->have_posts() ) :
        
        while( $query->have_posts() ) :
            
            $query->the_post() ;
            $course_price = get_field('price');
            $course_id = get_the_id();
            $result .= '<div class="course-item" course-id="' . $course_id . '">';
            $result .= '<div class="course-content-inner">';
            $result .= '<div class="course-column-left">';
            $result .= '<div class="course-poster">' . get_the_post_thumbnail() . '</div>';
            $result .= '</div>'; // course-column-left
            $result .= '<div class="course-column-right">';
            $result .= '<div class="course-column-text">';
            $result .= '<div class="course-name"><h3>' . get_the_title() . '</h3></div>';
            $result .= '<div class="course-desc"><p>' . get_the_content() . '</p></div>'; 
            if ( $course_price ) {
                if ( $course_price > 0 ) {
                    $result .= '<div class="course-price"><p>THB <span>' . number_format( $course_price ) . '</span></p></div>';
                }
            }
            if ( $show_register ) {
                $result .= '<a class="button button-register-course" href="' . $register_page . '?course=' . $course_id . '">Register to this course</a>';
            }
            $result .= '</div>'; // course-column-text
            $result .= '</div>'; // course-column-right
            $result .= '</div>'; // course-content-inner
            $result .= '</div>'; // course-item
            
        endwhile;
        
        wp_reset_postdata();
        
    endif;    
    $result .= '</div>';
    return $result;   
    
}
add_shortcode( 'courselisting', 'kkg_display_course_lists' );

function kkg_register_custom_widget_area() {
    register_sidebar(
        array(
            'id' => 'page-widget-area',
            'name' => esc_html__( 'Page Widget', 'twentytwenty' ),
            'description' => esc_html__( 'A new widget area made for page content', 'twentytwenty' ),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title-holder"><h3 class="widget-title">',
            'after_title' => '</h3></div>'
        )
    );
}
add_action( 'widgets_init', 'kkg_register_custom_widget_area' );

add_filter( 'pre_user_query', 'kkg_random_user_query' );
function kkg_random_user_query( $class ) {
    if( 'rand' == $class->query_vars['orderby'] )
    $class->query_orderby = str_replace( 'user_login', 'RAND()', $class->query_orderby );
    
    return $class;
}


function kkg_display_trainer_of_the_day( $atts, $content, $shortcode_tag ){
    $options = shortcode_atts( array(
        'register_page_id' => '',
    ), $atts );
    
    $register_page = get_permalink( $options['register_page_id'] );
    
    $trainer_query_args = array(
        'role'    => 'um_trainer',
        'number'  => 1,
        'orderby' => 'rand',
        'order'   => 'DESC'
    );
    
    $trainer_query = new WP_User_Query( $trainer_query_args );
    $trainer_html = '<div class="trainer-list-wrapper">';
    // Get the results
    $trainers = $trainer_query->get_results();
    
    // Check for results
    if ( !empty( $trainers ) ) {
        
        
        foreach ( $trainers as $trainer )
        {
            $trainer_info = get_userdata( $trainer->ID );
            $trainer_html .= '<div class="trainer-inner">';
            $trainer_html .= '<div class="trainer-thumb"><img src="' . esc_url( get_avatar_url( $trainer->ID ) ) . '" /></div>'; 
            $trainer_html .= '<div class="trainer-name">' . $trainer_info->first_name . ' ' . $trainer_info->last_name . '</div>';
            $trainer_html .= '<a class="button button-link" href="' . $register_page . '">Sign Up Now</a>';
            $trainer_html .= '</div>';
        } 
    } else {
        $trainer_html .= '<p>No trainer found</p>';
    }
    
    $trainer_html .= '</div>';
    return $trainer_html;
    
}
add_shortcode( 'traineroftheday', 'kkg_display_trainer_of_the_day' );



function kkg_display_trainer( $atts, $content, $shortcode_tag ){
    $options = shortcode_atts( array(
        'column' => 'full',
        'limit'  => -1,
    ), $atts );
    
    
    $trainer_query_args = array(
        'role'    => 'um_trainer',
        'number'  => $options['limit'],
        'order'   => 'DESC'
    );
    
    $trainer_query = new WP_User_Query( $trainer_query_args );
    $trainer_html = '<div class="trainer-lists"><div class="row">';
    // Get the results
    $trainers = $trainer_query->get_results();
    
    // Check for results
    if ( !empty( $trainers ) ) {
        
        
        foreach ( $trainers as $trainer )
        {
            $column_class = $options["column"];
            $trainer_info = get_userdata( $trainer->ID );
            $trainer_html .= '<div class="col col-' . $column_class . '">';
            $trainer_html .= '<div class="trainer-inner">';
            $trainer_html .= '<div class="trainer-thumb"><img src="' . esc_url( get_avatar_url( $trainer->ID ) ) . '" /></div>'; 
            $trainer_html .= '<div class="trainer-name">' . $trainer_info->first_name . ' ' . $trainer_info->last_name . '</div>';
            $trainer_html .= '</div>';
            $trainer_html .= '</div>'; // column
        } 
    } else {
        $trainer_html .= '<p>No trainer found</p>';
    }
    
    $trainer_html .= '</div></div>';
    return $trainer_html;
    
}
add_shortcode( 'trainers', 'kkg_display_trainer' );

function kkg_register_course_user( $fields, $entry, $form_data, $entry_id ) {
    global $current_user;
    // form #5.
    if ( absint( $form_data['id'] ) !== 119 ) {
        return;
    }
    
    $entry_fields = $entry['fields'];
    $user_course_id = $entry_fields[1];
    $course_args = array(
        'p'              => $user_course_id,
        'post_type'      => 'courses',
    );
    
    $course_query = new WP_Query( $course_args );
    $user_id    = get_current_user_id();
    if ( $course_query ) {
        if ( user_can( $current_user, 'um_member' ) ) {
            while ( $course_query->have_posts() ) {
                $course_query->the_post();
                $course_title = get_the_title();
                $previous_registered_course = get_user_meta( $user_id, 'registered_course', false );
    
                if ( !empty( $previous_registered_course ) ) {
                    update_user_meta( $user_id, 'registered_course', $course_title );
                }else {
                    add_user_meta( $user_id, 'registered_course', $course_title );
                }
            }
        }
    }
}
add_action( 'wpforms_process_complete', 'kkg_register_course_user', 10, 4 );

function kkg_export_users() {
	$screen = get_current_screen();
	// Only add to users.php page
	if ( $screen->id != "users" )
		return;
?>
    <script type="text/javascript">
		jQuery(document).ready( function($) {
			jQuery( '.tablenav.top .clear, .tablenav.bottom .clear' ).before('<form action="#" method="POST"><input type="hidden" id="kkg_export_csv" name="kkg_export_csv" value="1" /><input class="button button-primary user_export_button" type="submit" value="<?php esc_attr_e('Export CSV', 'twentytwenty');?>" /></form>');
		});
	</script>
<?php
}
add_action( 'admin_footer', 'kkg_export_users' );

function export_csv() {
	if ( ! empty( $_POST['kkg_export_csv'] ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			// set header for CSV file
			header("Content-type: application/force-download");
			header('Content-Disposition: inline; filename="users_'.date('Ymd').'.csv"');

			$args = array (
				'order' => 'ASC',
				'orderby' => 'display_name',
				'fields' => 'all',
			);

			$wp_users = get_users( $args );

			echo '" User ID "," User Name "," First Name "," Last Name "," Email ID "," Nick Name "," User Role "," Registered Date "' . "\r\n";

			foreach ( $wp_users as $user ) {
				$user_id   = $user->ID;
				$user_name = $user->user_login;
				$reg_date  = $user->user_registered;
				$meta      = get_user_meta($user_id);
				$role      = $user->roles;
				$email     = $user->user_email;

				$first_name = ( isset($meta['first_name'][0]) && $meta['first_name'][0] != '' ) ? $meta['first_name'][0] : '' ;
				$last_name  = ( isset($meta['last_name'][0]) && $meta['last_name'][0] != '' ) ? $meta['last_name'][0] : '' ;
				$nickname   = ( isset($meta['nickname'][0]) && $meta['nickname'][0] != '' ) ? $meta['nickname'][0] : '' ;

				echo '"'.$user_id.'","'.$user_name.'","'.$first_name.'","'.$last_name.'","'.$email.'","'.$nickname.'","'.ucfirst($role[0]).'","'.$reg_date.'"'."\r\n";
			}
			exit();
		}
	}
}
add_action( 'admin_init', 'export_csv' );