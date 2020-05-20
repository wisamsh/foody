<?php
require('class-coupons-list.php');

$myListTable = new Coupons_List();
$organizations_list = get_orginazations_list();
$filters_data = [];
$has_filters = false;


if (isset($_POST['s'])) {
    $myListTable->prepare_items($_POST['s']);
} else {
    $myListTable->prepare_items();
}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<style>
    .filter-row {
        display: flex;
        flex-direction: column;
        margin-top: 5px;
    }

    .filter-row .button {
        width: 30%;
    }

    .search-box-title {
        text-decoration: underline;
    }
</style>
<form method="post" id="search-from">
    <?php
    $myListTable->search_box('Search', 'coupons-search-id');
    ?>
</form>
<form method="post">
    <input type="hidden" name="page" value="test_list_table">
    <?php
    $myListTable->display();
    ?>
</form>
</div>
<script type="application/javascript">
    function getUpdate(id) {
        window.location =  window.location.protocol + '//' + window.location.hostname + '/wp/wp-admin/admin.php?page=update_coupon&update=true&id=' + id;
    }
</script>