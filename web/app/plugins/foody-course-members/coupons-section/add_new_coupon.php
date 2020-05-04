<?php
$courses_list = get_courses_list(true);
$organizations_list = get_orginazations_list();
$datetime = new DateTime(null, new DateTimeZone('Asia/Jerusalem'));
$current_date_time = $datetime->format('Y-m-d');
?>
    <style>
        .required-astrix {
            color: red;
        }

        li{
            text-align: right !important;
        }
    </style>
    <h2>קופון חדש</h2>
    <div class="row" style="width: 20%; line-height: 35px; margin-right: 3%;">
        <form action="admin.php?page=coupons_table_admin_page" method="post">
            <div class="form-row pt-3">
                <div class="col-1 offset-md-1 p-3">
                    <label for="creation-date" class="font-weight-bold pl-2">תאריך יצירה<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="creation_date" id="creation-date" type="date"
                           value="<?php echo $current_date_time; ?>" required>
                </div>
                <hr>
                <div class="col-1 offset-md-1 p-3">
                    <label for="coupon" class="font-weight-bold pl-2">שם קופון<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="coupon" id="coupon" type="text" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="coupon-type" class="font-weight-bold pl-2">סוג קופון<span
                                class="required-astrix">*</span></label>
                    <select id="coupon-type" name="coupon_type" required>
                        <option disabled selected value> -- בחר/י סוג קופון --</option>
                        <option value="כללי"> כללי</option>
                        <option value="חח״ע"> חח״ע</option>
                    </select>
                </div>
                <hr id="coupon-type-hr">
<!--                <div class="col-2 p-3">-->
<!--                    <label for="course-name" class="font-weight-bold pl-2" style="margin-left: 2%">שם קורס<span-->
<!--                                class="required-astrix">*</span></label>-->
<!--                    <select id="course-name" name="course_name[]"  class="selectpicker" multiple required>-->
<!--                        --><?php
//                        if (!empty($courses_list)) {
//                            echo '<option disabled value> -- בחר/י קורס/ים -- </option>';
//                            foreach ($courses_list as $id => $course_name) {
//                                echo '<option value="' . $id . '" >' . $course_name . '</option>';
//                            }
//                        }
//                        ?>
<!--                    </select>-->
<!--                </div>-->
<!--                <hr>-->
                <div class="col-1 offset-md-1 p-3">
                    <label for="expiration-date" class="font-weight-bold pl-2">תאריך תפוגה<span class="required-astrix">*</span></label>
                    <input class="form-control" name="expiration_date" id="expiration-date" type="date"
                           value="<?php echo $current_date_time; ?>" required>
                </div>
                <hr>
                <div class="col-1 p-3">
                    <label for="coupon-value" class="font-weight-bold pl-2">ערך קופון<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="coupon_value" id="coupon-value" type="number">
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="organization" class="font-weight-bold pl-2" style="margin-left: 2%">שם ארגון</label>
                    <select id="organization" name="organization">
                        <?php
                        if (!empty($organizations_list)) {
                            echo '<option disabled selected value> -- בחר/י ארגון -- </option>';
                            foreach ($organizations_list as $organization) {
                                echo '<option value="' . $organization . '" >' . $organization . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="max-amount" class="font-weight-bold pl-2">כמות קופונים<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="max_amount" id="max-amount" type="number" required>
                </div>
                <hr>
<!--                <div class="col-2 p-3">-->
<!--                    <label for="used-amount" class="font-weight-bold pl-2">קופונים ששומשו<span-->
<!--                                class="required-astrix">*</span></label>-->
<!--                    <input class="form-control" name="used_amount" id="used-amount" type="number" required>-->
<!--                </div>-->
<!--                <hr>-->
                <div class="col-2 p-3">
                    <label for="invoice-desc" class="font-weight-bold pl-2">תיאור לחשבונית<span class="required-astrix">*</span></label>
                    <input class="form-control" name="invoice_desc" id="invoice-desc" type="text" required>
                </div>
                <hr>
                <div class="col-2 pt-5 text-center">
                    <button type="submit" name="new_coupon" class="btn btn-primary mb-2" style="margin-top: 2%;">
                        קופון חדש
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
    <script type="text/javascript">
        jQuery(document).ready(function () {

            $('#coupon-type').on('change', function () {
                let val  = $(this).val();
                if(val == 'כללי'){
                    create_select_courses_dropdown(true);
                }
                else if(val == 'חח״ע'){
                    create_select_courses_dropdown(false);
                }
                else{
                    if($('#course-name').length){
                        $('#course-name').remove();
                    }
                }
            });
        });

        function create_select_courses_dropdown(is_multi) {
            let select_element = '<div class="col-2 p-3" id="course-name-selector">\n' +
                '        <label for="course-name" class="font-weight-bold pl-2" style="margin-left: 2%">שם קורס<span' +
                '                    class="required-astrix">*</span></label>\n';

            select_element += is_multi ? '<select id="course-name" name="course_name[]"  class="selectpicker" multiple required>\n' : '<select id="course-name" name="course_name" required>\n';
            <?php
            if (!empty($courses_list)) {
                ?>
                    select_element += is_multi ? '<option disabled value> -- בחר/י קורס/ים -- </option>' : '<option disabled selected value> -- בחר/י קורס -- </option>';
                <?php
                foreach ($courses_list as $id => $course_name) {
                    ?>
                        select_element += is_multi ? '<option value="<?php echo $id; ?>"><?php echo $course_name; ?></option>' : '<option value="<?php echo $course_name; ?>"><?php echo $course_name; ?></option>';
                    <?php
                }
            }
            ?>
            select_element += '        </select>\n' +
                '    </div>\n' +
                '    <hr id="course-name-hr">';

            if($('#course-name-selector').length){
                $('#course-name-selector').remove();
                $('#course-name-hr').remove();
            }

            $('#coupon-type-hr').after(select_element);

            if(is_multi) {
                $('#course-name').selectpicker();
            }
        }
    </script>
<?php
