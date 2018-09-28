<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/29/18
 * Time: 12:53 PM
 */

if (!is_admin()):
    $globals = array(
        'isMobile' => wp_is_mobile(),
        'ajax' => admin_url( 'admin-ajax.php' ),
    );

    ?>
    <script>
        globals = <?php echo json_encode($globals) ?>;
    </script>

<?php endif; ?>