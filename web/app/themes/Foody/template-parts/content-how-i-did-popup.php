<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/12/18
 * Time: 11:25 AM
 */

$modal = [
    'title' => 'תראו מה יצא לי'
];

?>

<div class="modal fade" tabindex="-1" role="dialog" id="how-i-did-modal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php echo $modal['title'] ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-8">
                    <img id="image" src="" alt="">
                </div>


                <section class="details col-4">
                    <h4 id="user">

                    </h4>

                    <p id="content"></p>
                </section>
            </div>
        </div>
    </div>
</div>

