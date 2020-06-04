<?php
require('class-members-list.php');

$myListTable = new Courses_Members_List();
$organizations_list = get_orginazations_list();
$filters_data = [];
$has_filters = false;


if (isset($_POST['s'])) {
    $myListTable->prepare_items($_POST['s']);
    $filters_data = ['search' => $_POST['s']];
    $has_filters = true;
} elseif (!isset($_POST['export_clicked']) && (isset($_POST['date_from']) && isset($_POST['date_to'])
        || isset($_POST['payment_filter'])
        || isset($_POST['course_filter'])
        || isset($_POST['marketing_filter'])
        || isset($_POST['organization_filter'])
        || isset($_POST['coupon_filter']))) {

    $date_from = isset($_POST['date_from']) ? $_POST['date_from'] : '';
    $date_to = isset($_POST['date_to']) ? $_POST['date_to'] : '';
    $payment_filter = isset($_POST['payment_filter']) ? $_POST['payment_filter'] : '';
    $course_filter = isset($_POST['course_filter']) ? $_POST['course_filter'] : '';
    $marketing_filter = isset($_POST['marketing_filter']) ? $_POST['marketing_filter'] : '';
    $organization_filter = isset($_POST['organization_filter']) ? $_POST['organization_filter'] : '';
    $coupon_filter = isset($_POST['coupon_filter']) ? $_POST['coupon_filter'] : '';

    if (!empty($marketing_filter)) {
        $marketing_filter = $marketing_filter == __('אושר') ? 1 : 0;
    }

    $filters_data = [
        'date_from' => $date_from,
        'date_to' => $date_to,
        'payment_filter' => $payment_filter,
        'course_filter' => $course_filter,
        'marketing_filter' => $marketing_filter,
        'organization_filter' => $organization_filter,
        'coupon_filter' => $coupon_filter
    ];
    $myListTable->prepare_items($filters_data);
    $has_filters = true;

} elseif (isset($_POST['export_clicked']) && isset($_POST['search'])) {
    $myListTable->prepare_items($_POST['search']);
    $filters_data = ['search' => $_POST['search']];
    $has_filters = true;
} else {
    $myListTable->prepare_items();
    $has_filters = false;
}
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.15/lodash.min.js"></script>
<style>
    #overlay {
        position: fixed;
        display: none;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 2;
        cursor: pointer;
    }
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
<div id="overlay" onclick=""> אנא המתינו... </div>
<form method="post" id="search-from">
    <?php
    $myListTable->search_box('Search', 'search-id');
    ?>
</form>
<form method="post">
    <input type="hidden" name="page" value="test_list_table">
    <?php
    $myListTable->display();
    ?>
    <div class="search-box" style="float: right; margin-top: 2%;">
        <h3 class="search-box-title">סינון תוצאות</h3>
        <div class="row filter-row">
            <label for="date-filter" style="font-weight: bold">תאריך:</label>
            <div class="date_container">
                <lablel> מתאריך</lablel>
                <input type="date" id="date-filter-from" name="date_from">
                <lablel> עד</lablel>
                <input type="date" id="date-filter-to" name="date_to">
            </div>
        </div>
        <div class="row filter-row">
            <label for="payment-filter" style="font-weight: bold">סוג תשלום:</label>
            <select id="payment-filter" name="payment_filter">
                <option selected value> -- בחר/י שיטת תשלום --</option>
                <option value="ביט"> ביט</option>
                <option value="כרטיס אשראי"> כרטיס אשראי</option>
                <option value="ועד עובדים"> ועד עובדים</option>
                <option value="buy me">buy me</option>
                <option value="אחר"> אחר</option>
            </select>
        </div>
        <div class="row filter-row">
            <label for="course-filter" style="font-weight: bold">שם קורס:</label>
            <input type="text" id="course-filter" name="course_filter">
        </div>
        <div class="row filter-row">
            <label for="marketing-filter" style="font-weight: bold">דיוור:</label>
            <select id="marketing-filter" name="marketing_filter">
                <option selected value=""> -- בחר/י האם אושר דיוור --</option>
                <option value="אושר"> אושר</option>
                <option value="לא אושר"> לא אושר</option>
            </select>
        </div>
        <div class="row filter-row">
            <label for="organization-filter" style="font-weight: bold">ארגון:</label>
            <select id="organization-filter" name="organization_filter">
                <?php
                if (!empty($organizations_list)) {
                    echo '<option selected value=""> -- בחר/י ארגון -- </option>';
                    foreach ($organizations_list as $organization) {
                        echo '<option value="' . $organization . '" >' . $organization . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="row filter-row">
            <label for="coupon-filter" style="font-weight: bold">קופון:</label>
            <input type="text" id="coupon-filter" name="coupon_filter">
        </div>
        <div class="row filter-row">
            <input type="submit" id="filters-submit" class="button" value="סינון">
        </div>
    </div>
</form>
<form method="post" id="export-form">
    <div class="row filter-row" style="align-items: flex-end;">
        <input type="text" id="export-clicked" name="export_clicked" value="true" hidden>
        <?php
        if ($has_filters) {
            foreach ($filters_data as $filter_name => $filter_value) {
                if (!empty($filter_value)) {
                    echo '<input type="text"  name="' . $filter_name . '" value="' . $filter_value . '" hidden>';
                }
            }
        }
        ?>
        <input type="submit" id="export-submit" class="button" value="ייצא תוצאות">
    </div>
</form>
</div>
<script type="application/javascript">
    function getRefund(bitPaymentInitiationId) {
        var isRefund = confirm("האם לבצע זיכוי?");
        if (isRefund) {
            //todo: ajax call that take care of Refund
            startLoader();
            foodyAjax({
                action: 'foody_bit_refund_process',
                data: {
                    paymentInitiation_id: bitPaymentInitiationId
                }
            }, function (err, data) {
                if (err) {
                    stopLoader();
                    console.log(err);
                } else {
                    debugger;
                    if(typeof data.data.msg != 'undefined'){
                        stopLoader();
                        alert(data.data.msg);
                        window.location = window.location.protocol + '//' + window.location.hostname + '/wp/wp-admin/admin.php?page=foody-course-members%2Fcourse-members-manage.php';
                    }
                    else{
                        if(typeof data.data.error != 'undefined'){
                            stopLoader();
                            alert(data.data.error);
                            window.location = window.location.protocol + '//' + window.location.hostname + '/wp/wp-admin/admin.php?page=foody-course-members%2Fcourse-members-manage.php';
                        }
                    }
                }
            });
        }
    }

    function getUpdate(id) {
        window.location = window.location.protocol + '//' + window.location.hostname + '/wp/wp-admin/admin.php?page=update_course_member&update=true&id=' + id;
    }

    function startLoader() {
        document.getElementById("overlay").style.display = "block";
    }

    function stopLoader() {
        document.getElementById("overlay").style.display = "none";
    }
</script>
