function create_new_COURSE_user ($name, $surname, $email, $role, $facilitatorEmail) {
    
error_log('inside the create_new_COURSE_user function ');          

  switch ( $role ) {
      case "Course One":
        $assigned_role = "cone_student";
         break;
      case "Course Two":
        $assigned_role = "ctwo_student";
         break;
      case "Course Three":
        $assigned_role = "cthree_student";
         break;
      case "Course Four":
      $assigned_role = "cfour_student";
        break;
  }
  
  
  $password = wp_generate_password( 6, false );
  
  
  if( !username_exists( $email ) ) {
  
        
        $user_id = wp_create_user ( $email, $password, $email );
      
  
        wp_update_user(
          array(
            'ID'          =>    $user_id,
            'nickname'    =>    $email,
            'first_name'  =>    $name,
            'last_name'   =>    $surname
          )
        );
      
  
        $user = new WP_User( $user_id );
        $user->set_role( $assigned_role );
      
      
      
        $message = "Hello, {$name} {$surname},\r\n\r\n"
                  . "Welcome to your {$role} Course\r\n\r\n"
                  . "To login and view the course material please follow this link: LOGIN LINK \r\n\r\n"
                  . "and use your email address as username and the password below: \r\n\r\n"
                  . "Username: {$email}\r\n"
                  . "Password: {$password}";

        $emailArray = array( ARRAY OF EMAIL ADDRESSES THAT NEED THE EMAIL - BACKUPS ARE GOOD HERE );
        
        foreach($emailArray as $email_address)
        {
          wp_mail($email_address, 'Welcome!', $message);
        }

        return 0;
      
  } else {
      return 1;
  }

}

/* WPCF7 hook before mail sends - creates user from form input fields */

function before_send_mail_function( $contact_form, &$abort, $submission ) {

  $form_id = $contact_form->id();
  
  if ( $form_id === 1331 ) {
      $your_name = $submission->get_posted_data('your-name');
  
      $your_surname = $submission->get_posted_data('your-surname');
      
      $your_email = $submission->get_posted_data('your-email');
      
      $clinic = $submission->get_posted_data('COURSE CHOICE');

      $facilitatorEmail = $submission->get_posted_data('facilitator-email');
  
          
      $error = ftmv_create_new_skills_clinic_user($your_name, $your_surname, $your_email, $clinic[0], $facilitatorEmail);
      
      error_log('error value ');
      error_log($error);
      
      if($error === 1) {
            
            $msgs = $contact_form->prop('messages');
            error_log( print_r($msgs, TRUE) );
            $msgs['mail_sent_ng'] = "A user with those details already exists. "; 
            
            error_log( $msgs['mail_sent_ng'] );
            
            $contact_form->set_properties(array('messages' => $msgs));
            $abort = true;
            error_log( print_r($contact_form, TRUE) );
             return $contact_form;
            
        }
        
      return $contact_form;
        
      
  }
}
add_filter( 'wpcf7_before_send_mail', 'before_send_mail_function', 10, 3);


/* Following Functions Check if a page's content is restricted and then further which C the content is so that it gets served to the correct user role and capabilities etc */
function is_it_course_one_content(){

  $custom = get_post_custom();  

  if(isset($custom['cone_content'])) {
    if ( $custom['cone_content'][0] === '1' ){            
      return 1;
      
    }
  }	
	
}

function is_it_course_two_content(){

  $custom = get_post_custom();  

  if(isset($custom['ctwo_content'])) {
    if ( $custom['ctwo_content'][0] === '1' ){            
      return 1;
      
    }
  }	
	
}

function is_it_course_three_content(){

  $custom = get_post_custom();  

  if(isset($custom['cthree_content'])) {
    if ( $custom['cthree_content'][0] === '1' ){            
      return 1;
      
    }
  }	
	
}

function is_it_course_four_content(){
	
	$custom = get_post_custom();  

  if(isset($custom['cfour_content'])) {
    if ( $custom['cfour_content'][0] === '1' ){            
      return 1;
      
    }
  }
	
}

function isItNewStudentPage(){
	
	$custom = get_post_custom();  

  if(isset($custom['new_student_page'])) {
    if ( $custom['new_student_page'][0] === '1' ){            
      return 1;      
    }
  }
	
}

function isContentRestricted() {	
  
  $custom = get_post_custom();  

  if(isset($custom['is_restricted'])) {
    if ( $custom['is_restricted'][0] === '1' ){      
      return 1;
    }
  }

}

function permission_denied() {
  $post_id = 1334;  
	$queriedPost = get_post($post_id);  					
  $theContent = $queriedPost->post_content;
	
  //echo 'this is inside';
  return $theContent;
      //return $theContent
}


add_filter( 'the_content', 'ftmv_content_filter', 99);
 
function ftmv_content_filter( $content ) {

		if ( isContentRestricted() ){

      if  ( is_user_logged_in() ) {        
        
        if ( is_it_course_two_content() ) {             
          if ( ( current_user_can( 'ctwo_content' ) === True )  ){            
            return $content;
          } else {
            return  do_shortcode( permission_denied());	
          }
        } elseif ( is_it_course_four_content() ) {          
          if ( ( current_user_can( 'cfour_content' ) === True )  ){            
            return $content;
          } else {
            return  do_shortcode( permission_denied());	
          }
        } elseif ( is_it_course_three_content() ) {          
          if ( ( current_user_can( 'cthree_content' ) === True )  ){   
            return $content;
        } else {
            return  do_shortcode( permission_denied());	
          }
        } elseif ( is_it_course_one_content() ) {          
          if ( ( current_user_can( 'cone_content' ) === True )  ){   
            return $content;
        } else {
            return  do_shortcode( permission_denied());	
          }
        } elseif ( isItNewStudentPage() ) {                    
          if ( ( current_user_can( 'new_student_create' ) === True )  ){            
            return $content;
          } else {
            return  do_shortcode( permission_denied());	
          }
        } else {
          return  do_shortcode( permission_denied());	
        }
      } else {
        return  do_shortcode( permission_denied());	
      }
		} else {
		  return $content;			
		}
		
}

//add_action( 'wp', 'ftmv_add_roles_and_caps' );
function ftmv_add_roles_and_caps()
{
  $role = get_role( 'administrator' );
  $role->add_cap( 'cone_content', true);

  /* this was used to create different roles that would then be assigned to new students or admins so they could view restricted pages as well as create new students
  $role = add_role( 'THE_STUDENT_ROLE', 'The nice text of the student role', array(
    'cone_content' => true,
) ); */


  //$wp_roles = new WP_Roles(); // create new role object
  //$wp_roles->remove_role('tc_student');
 
 /* $role = get_role( 'administrator' );
 //$role->remove_cap( 'presentation_clinic_view', true); 
 $role->add_cap( 'new_student_create', true);      */

}


/* Adding content to Woocomme menus */

function add_courses_endpoint() {
  add_rewrite_endpoint( 'your-courses', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'add_courses_endpoint' );

// ------------------
// 2. Add new query var

function courses_query_vars( $vars ) {
  $vars[] = 'your-courses';
  return $vars;
}

add_filter( 'query_vars', 'courses_query_vars', 0 );

// ------------------
// 3. Insert the new endpoint into the My Account menu

function add_courses_link_my_account( $items ) {
  $items['your-courses'] = 'Your Courses';
  return $items;
}

add_filter( 'woocommerce_account_menu_items', 'add_courses_link_my_account' );

// ------------------
// 4. Add content to the new tab

function courses_content() {
      
  if ( current_user_can( 'cthree_content' ) === True  ){
    $post_id = 1341;  
    $queriedPost = get_post($post_id);  						
    echo do_shortcode( $queriedPost->post_content );
  }

  if ( current_user_can( 'ctwo_content' ) === True  ){ 
    $post_id = 1343;  
    $queriedPost = get_post($post_id);  						
    echo do_shortcode( $queriedPost->post_content );
  }

  if ( current_user_can( 'cone_content' ) === True  ){ 
    $post_id = 1416;  
    $queriedPost = get_post($post_id);  						
    echo do_shortcode( $queriedPost->post_content );
  }
 
	if ( current_user_can( 'cfour_content' ) === True  ){ 
    $post_id = 1522;  
    $queriedPost = get_post($post_id);  						
    echo do_shortcode( $queriedPost->post_content );
  }
	
}

add_action( 'woocommerce_account_your-courses_endpoint', 'ftmv_courses_content' );


function ftmv_reorder_my_account_menu() {
  $neworder = array(
      'dashboard'          => __( 'Dashboard', 'woocommerce' ),      
      'your-courses'       => __( 'Your Courses', 'woocommerce' ),              
      'edit-account'       => __( 'Account Details', 'woocommerce' ),
      'customer-logout'    => __( 'Logout', 'woocommerce' ),
  );
  return $neworder;
}
add_filter ( 'woocommerce_account_menu_items', 'ftmv_reorder_my_account_menu' );


/* The below was used to push data to the DOM so that it could be used in page - the use case here was forms where users had to fill in their details, once the user system was implemented these values were auto-filled */
function printUserDetailsToDom(){
    $current_user = wp_get_current_user();
    
    $firstName = esc_html( $current_user->user_firstname );
    $lastName = esc_html( $current_user->user_lastname );
    $email = esc_html( $current_user->user_email );

    echo '<script type="text/javascript">';
    echo "const StudentDetails = {
      firstName: '{$firstName}',
      lastName: '{$lastName}',
      email: '{$email}' }";  
    echo '</script>';
}

add_action('wp_footer', 'ftmv_get_student_details');

function ftmv_get_student_details() {

  if ( is_it_course_two_content() ) {             
          if ( ( current_user_can( 'ctwo_content' ) === True )  ){            
            printUserDetailsToDom();
          } 
  } elseif ( is_it_course_four_content() ) {          
    if ( ( current_user_can( 'cfour_content' ) === True )  ){            
      printUserDetailsToDom();
    } 
  } elseif ( is_it_course_three_content() ) {          
    if ( ( current_user_can( 'cthree_content' ) === True )  ){   
      printUserDetailsToDom();
    } 
  } elseif ( isItNewStudentPage() ) {          
    if ( ( current_user_can( 'new_student_create' ) === True )  ){   
      printUserDetailsToDom();
    } 
  } 
}



function my_login_redirect( $redirect_to, $request, $user ) {
  //is there a user to check?
  global $user;
  if ( isset( $user->roles ) && is_array( $user->roles ) ) {

      if ( in_array( 'cone_student', $user->roles ) ) {
          // redirect them to the default place
          $data_login = "WEBSITE/my-account";

          return $data_login;
      } elseif ( in_array( 'ctwo_student', $user->roles ) ) {
        // redirect them to the default place
        $data_login = "WEBSITE/my-account";

        return $data_login;
      } elseif ( in_array( 'cthree_student', $user->roles ) ) {
        // redirect them to the default place
        $data_login = "WEBSITE/my-account";

        return $data_login;
      } elseif ( in_array( 'cfour_student', $user->roles ) ) {
        // redirect them to the default place
        $data_login = "WEBSITE/my-account";

        return $data_login;
      } elseif ( in_array( 'administrator', $user->roles ) ) {
        // redirect them to the default place
        $data_login = "WEBSITE/new-student-creation-page";

        return $data_login;
      } 
  }   
}
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

