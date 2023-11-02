<?php
if (isset($_GET['update']) && $_GET['update'] && isset($_GET['id'])) {
    global $wpdb;
    $courses_list = get_courses_list(true);
    $courses_names =[];
    $id = $_GET['id'];
    $table_name = $wpdb->prefix . 'foody_courses_coupons';
    $result = $wpdb->get_results("SELECT * FROM $table_name WHERE coupon_id='$id'");
    $all_courses_selected = true;

    $datetime = new DateTime(null, new DateTimeZone('Asia/Jerusalem'));
    $current_date_time = $datetime->format('Y-m-d');

    foreach ($result as $coupon) {
        $creation_date = isset($coupon->creation_date) ? $coupon->creation_date : '';
        $coupon_name = isset($coupon->coupon) ? $coupon->coupon : '';
        $coupon_type = isset($coupon->coupon_type) ? $coupon->coupon_type : '';
        $organization = isset($coupon->organization) ? $coupon->organization : '';
        $expiration_date = isset($coupon->expiration_date) ? $coupon->expiration_date : $current_date_time;
        $coupon_value = isset($coupon->coupon_value) ? $coupon->coupon_value : '';
        $max_amount = isset($coupon->max_amount) ? $coupon->max_amount : '';
        $invoice_desc = isset($coupon->invoice_desc) ? $coupon->invoice_desc : '';
        $used_amount = isset($coupon->used_amount) ? $coupon->used_amount : '';
        if (isset($coupon->course_name)) {
            $course_list = $coupon->course_name;
            $courses_names = explode(',', $course_list);
        }
        $is_percentages = strpos($coupon_value, '%') == true;
        if($is_percentages){
            $coupon_value = str_replace('%', '', $coupon_value);
        }
    }

    ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .required-astrix {
            color: red;
        }
    </style>

    <h2>עדכון קופון</h2>
    <div class="row" style="width: 20%; line-height: 35px; margin-right: 3%;">
        <form action="admin.php?page=coupons_table_admin_page" method="post">
            <input name="coupon_id" id="coupon-id" hidden value="<?php echo $id; ?>">
            <div class="col-1 offset-md-1 p-3">
                <label for="creation-date" class="font-weight-bold pl-2">תאריך יצירה</label>
                <input class="form-control" name="creation_date" id="creation-date" type="date"
                       value="<?php echo $creation_date; ?>" disabled>
            </div>
            <div class="form-row pt-3">
                <div class="col-1 offset-md-1 p-3">
                    <label for="expiration-date" class="font-weight-bold pl-2">תאריך תפוגה<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="expiration_date" id="expiration-date" type="date"
                           value="<?php echo $expiration_date; ?>" required>
                </div>
                <hr>
                <div class="col-1 offset-md-1 p-3">
                    <label for="coupon" class="font-weight-bold pl-2">שם קופון</label>
                    <input class="form-control" name="coupon" id="coupon" type="text" disabled value="<?php echo $coupon_name;?>">
                </div>
                <hr>
                <div class="col-1 p-3">
                    <label for="coupon-type" class="font-weight-bold pl-2">סוג קופון</label>
                    <select id="coupon-type" name="coupon_type" disabled>
                        <option <?php if($coupon_type == __('כללי')){ echo "selected";} ?> value="כללי" > כללי</option>
                        <option <?php if($coupon_type == __('חח״ע')){ echo "selected";} ?> value="חח״ע"> חח״ע</option>
                    </select>
                </div>
                <hr>
                <div class="col-2 p-3" style="display: flex; flex-direction: column">
                    <h5 style="text-decoration: underline">סוג ערך קופון<span class="required-astrix">*</span></h5>
                    <label style="margin-left: 2%;">אחוז
                        <input type="radio" name="percentages" value="true" required="true" <?php if($is_percentages) { echo "checked"; } ?> />
                    </label>
                    <label style="margin-left: 2%">שקלים
                        <input type="radio" name="percentages" value="false" <?php if(!$is_percentages) { echo "checked"; } ?>/>
                    </label>
                </div>
                <hr>
                <div class="col-1 offset-md-1 p-3">
                    <label for="coupon-value" class="font-weight-bold pl-2">ערך קופון<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="coupon_value" id="coupon-value" type="number" min="1"
                           value="<?php echo $coupon_value; ?>" required>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="course-name" class="font-weight-bold pl-2" style="margin-left: 2%">שם קורס<span
                                class="required-astrix">*</span></label>
                    <select id="course-name" name="course_name[]" class="selectpicker" multiple required>
                        <?php
                        if (!empty($courses_list)) {
                            echo '<option disabled value> -- בחר/י קורס/ים -- </option>';
                            foreach ($courses_list as $id => $course_name) {
                                if (!in_array($course_name, $courses_names)) {
                                    echo '<option value="' . $id . '" >' . $course_name . '</option>';
                                    $all_courses_selected = false;
                                } else {
                                    echo '<option value="' . $id . '" selected style="display:none;">' . $course_name . '</option>';
                                }
                            }
                            if($all_courses_selected){
                                echo '<option value="" disabled>' . __('כל הקורסים נבחרו') . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="organization" class="font-weight-bold pl-2" style="margin-left: 2%">שם ארגון</label>
                    <select id="organization" name="organization" disabled>
                        <?php
                            if($organization == '') {
                                echo '<option selected value>לא נבחר ארגון</option>';
                            }
                            else{
                                echo '<option selected value="' . $organization . '" >' . $organization . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <hr>
                <div class="col-1 p-3">
                    <label for="max-amount" class="font-weight-bold pl-2">מספר קופונים<span
                            class="required-astrix">*</span></label>
                    <input class="form-control" name="max_amount" id="max-amount" type="number"
                           min="<?php echo $used_amount; ?>"
                           value="<?php echo $max_amount; ?>" required>
                </div>
                <hr>
                <div class="col-1 p-3">
                    <label for="max-amount" class="font-weight-bold pl-2">מספר קופונים שנוצלו</label>
                    <input class="form-control" name="max_amount" id="max-amount" type="number" value="<?php echo $used_amount;?>" disabled>
                </div>
                <hr>
                <div class="col-1 p-3">
                    <label for="invoice-desc" class="font-weight-bold pl-2">תיאור לחשבונית<span
                                class="required-astrix">*</span></label>
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
    <link rel="stylesheet "type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<?php }