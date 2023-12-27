<?php
  /*
   * Show sites current user is a member with
   * 
   */

/** Sets up the WordPress Environment. */
require_once( ABSPATH . 'wp-load.php' );

add_action( 'wp_head', 'wp_no_robots' );

require_once( ABSPATH . 'wp-blog-header.php' );

add_action( 'wp_head', 'wpmu_signup_stylesheet' );

get_header( );

?>


<div>
        <?php
        $user = get_user_by( 'id', $user_id );
        $existing_user_email = $user->user_email;
        $user_blogs = get_blogs_of_user( $user_id );
        $user_blogs_sorted = array();
        foreach ( $user_blogs AS $user_blog ) {
                        $user_blogs_sorted[ $user_blog->blogname ] = $user_blog->siteurl;
        }

        // A real quick way to do a case-insensitive sort of an array keyed by strings: 
        uksort($user_blogs_sorted , "strnatcasecmp");
        
        printf( '<h2>' . __( 'Hi %1$s, You have been added to this site with username %2$s.  Your current sites on the Network are:' ) . '</h2>',
          '<strong>' . $existing_user_email . '</strong>',
          '<strong><em>' .  $user->user_login . '</strong></em>'
        );  

        foreach ( $user_blogs AS $user_blog ) {
                        
                // find the login page for each site in the loop
                switch_to_blog( $user_blog->userblog_id );
                $current_login_url = wp_login_url( );
                restore_current_blog( );

                $sitename = $user_blog->blogname;
                 ?>
                <h2>
                    <strong>
                        <a href="<?php echo $current_login_url ?>" ><?php echo $sitename ?></a>
                    </strong>
                </h2>
                <?php 
        }
        ?>
</div>

<?php  get_footer( );