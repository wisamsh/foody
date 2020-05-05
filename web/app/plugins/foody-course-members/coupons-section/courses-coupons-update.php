<?php
if (isset($_GET['update']) && $_GET['update'] && isset($_GET['id'])) {
    global $wpdb;

    $id = $_GET['id'];
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $result = $wpdb->get_results("SELECT * FROM $table_name WHERE coupon_id='$id'");

    $datetime = new DateTime(null, new DateTimeZone('Asia/Jerusalem'));
    $current_date_time = $datetime->format('Y-m-d');

    foreach ($result as $coupon) {
        $expiration_date = isset($coupon->expiration_date) ? $coupon->expiration_date : $current_date_time;
        $coupon_value = isset($coupon->coupon_value) ? $coupon->coupon_value : '';
        $max_amount = isset($coupon->max_amount) ? $coupon->max_amount : '';
        $invoice_desc = isset($coupon->invoice_desc) ? $coupon->invoice_desc : '';
        $used_amount = isset($coupon->used_amount) ? $coupon->used_amount : '';
    }

    ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .required-astrix{
            color: red;
        }
    </style>

    <h2>עדכון קופון</h2>
    <div class="row" style="width: 20%; line-height: 35px; margin-right: 3%;">
        <form action="admin.php?page=coupons_table_admin_page" method="post">
            <input name="coupon_id" id="coupon-id" hidden value="<?php echo $id; ?>">
            <div class="form-row pt-3">
                <div class="col-1 offset-md-1 p-3">
                    <label for="expiration-date" class="font-weight-bold pl-2" >תאריך תפוגה<span class="required-astrix">*</span></label>
                    <input  class="form-control" name="expiration_date" id="expiration-date" type="date"
                           value="<?php echo $expiration_date; ?>" required>
                </div>
                <hr>
                <div class="col-1 offset-md-1 p-3">
                    <label for="coupon-value" class="font-weight-bold pl-2">ערך קופון<span class="required-astrix">*</span></label>
                    <input class="form-control" name="coupon_value" id="coupon-value" type="number" min="1"
                           value="<?php echo $coupon_value; ?>" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="max-amount" class="font-weight-bold pl-2">מספר קופונים<span class="required-astrix">*</span></label>
                    <input class="form-control" name="max_amount" id="max-amount" type="number" min="<?php echo $used_amount;?>"
                           value="<?php echo $max_amount; ?>" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="invoice-desc" class="font-weight-bold pl-2">תיאור לחשבונית<span class="required-astrix">*</span></label>
                    <input class="form-control" name="invoice_desc" id="invoice-desc" type="text"
                           value="<?php echo $invoice_desc; ?>" required>
                </div>
                <hr>
                <div class="col-2 pt-5 text-center">
                    <button type="submit" name="update" class="btn btn-primary mb-2" style="margin-top: 2%;">
                        עדכן קופון
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php }