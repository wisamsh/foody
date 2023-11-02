<?php
if (isset($_GET['update']) && $_GET['update'] && isset($_GET['id'])) {
    global $wpdb;
    $have_payment_method = false;
    $id = $_GET['id'];
    $table_name = $wpdb->prefix . 'foody_courses_members';
    $result = $wpdb->get_results("SELECT * FROM $table_name WHERE member_id='$id'");

    $datetime = new DateTime(null, new DateTimeZone('Asia/Jerusalem'));
    $current_date_time = $datetime->format('Y-m-d');

    foreach ($result as $member) {
        $email = isset($member->member_email) ? $member->member_email : '';
        $first_name = isset($member->first_name) ? $member->first_name : '';
        $last_name = isset($member->last_name) ? $member->last_name : '';
        $phone = isset($member->phone) ? $member->phone : '';
        $member_course_name = isset($member->course_name) ? $member->course_name : '';
        $price_paid = isset($member->price_paid) ? floatval($member->price_paid) : '';
        $member_organization = isset($member->organization) ? $member->organization : '';
        $payment_method = isset($member->payment_method) ? $member->payment_method : '';
        $coupon = isset($member->coupon) ? $member->coupon : '';
        $purchase_date = isset($member->purchase_date) ? $member->purchase_date : $current_date_time;
        $note = isset($member->note) ? $member->note : '';
    }

    $organizations_list = get_orginazations_list();
    $courses_list = get_courses_list();
    $payment_methods = ['ביט', 'כרטיס אשראי', 'ועד עובדים', 'buy me', 'אחר'];

    ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .required-astrix{
            color: red;
        }
    </style>

    <h2>עדכון משתמש קורס</h2>
    <div class="row" style="width: 20%; line-height: 35px; margin-right: 3%;">
        <form action="admin.php?page=foody-course-members%2Fcourse-members-manage.php" method="post">
            <input name="member_id" id="member-id" hidden value="<?php echo $id; ?>">
            <div class="form-row pt-3">
                <div class="col-1 offset-md-1 p-3">
                    <label for="purchase-date" class="font-weight-bold pl-2" >תאריך רכישה<span class="required-astrix">*</span></label>
                    <input  class="form-control" name="purchase_date" id="purchase-date" type="date"
                           value="<?php echo $purchase_date; ?>" required>
                </div>
                <hr>
                <div class="col-1 offset-md-1 p-3">
                    <label for="member-email" class="font-weight-bold pl-2">מייל<span class="required-astrix">*</span></label>
                    <input class="form-control" name="member_email" id="member-email" type="email"
                           value="<?php echo $email; ?>" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="first-name" class="font-weight-bold pl-2">שם פרטי<span class="required-astrix">*</span></label>
                    <input class="form-control" name="first_name" id="first-name" type="text"
                           value="<?php echo $first_name; ?>" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="last-name" class="font-weight-bold pl-2">שם משפחה<span class="required-astrix">*</span></label>
                    <input class="form-control" name="last_name" id="last-name" type="text"
                           value="<?php echo $last_name; ?>" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="phone" class="font-weight-bold pl-2">טלפון<span class="required-astrix">*</span></label>
                    <input class="form-control" name="phone" id="phone" type="tel" value="<?php echo $phone; ?>" required>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="course-name" class="font-weight-bold pl-2" style="margin-left: 2%">שם קורס<span class="required-astrix">*</span></label>
                    <select id="course-name" name="course_name" required>
                        <?php
                        if (!empty($courses_list)) {
                            if (empty($member_course_name)) {
                                echo '<option disabled selected value> -- בחר/י קורס -- </option>';
                                foreach ($courses_list as $index => $course_name) {
                                    echo '<option value="' . $index . ':' . $course_name . '" >' . $course_name . '</option>';
                                }
                            } else {
                                echo '<option disabledd value> -- בחר/י קורס -- </option>';
                                foreach ($courses_list as $index => $course_name) {
                                    if ($course_name != $member_course_name) {
                                        echo '<option value="' . $index . ':' . $course_name . '" >' . $course_name . '</option>';
                                    } else {
                                        echo '<option selected value="' . $index . ':' . $course_name . '" >' . $course_name . '</option>';
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="price_paid" class="font-weight-bold pl-2" style="margin-left: 2%">מחיר ששולם<span class="required-astrix">*</span></label>
                    <input name="price_paid" id="price_paid" type="number" placeholder="0.0" step="0.01" min="0"
                           value="<?php echo $price_paid; ?>" required>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="payment-method" class="font-weight-bold pl-2" style="margin-left: 2%">אמצעי
                        תשלום<span class="required-astrix">*</span></label>
                    <select id="payment-method" name="payment_method" required>
                        <?php if (empty($payment_method)) {
                            echo '<option disabled selected value> -- בחר/י שיטת תשלום --</option>';
                        } else {
                            echo '<option disabled value> -- בחר/י שיטת תשלום --</option>';
                            $have_payment_method = true;
                        }
                        foreach ($payment_methods as $single_payment_method) {
                            if($have_payment_method && $single_payment_method == $payment_method) {
                                echo '<option selected value="' . $single_payment_method . '"> ' . $single_payment_method . ' </option>';
                            }
                            else{
                                echo '<option value="' . $single_payment_method . '"> ' . $single_payment_method . ' </option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="organization" class="font-weight-bold pl-2" style="margin-left: 2%">שם ארגון</label>
                    <select id="organization" name="organization">
                        <?php
                        if (!empty($organizations_list)) {
                            if (empty($member_organization)) {
                                echo '<option disabled selected value> -- בחר/י ארגון -- </option>';
                                foreach ($organizations_list as $organization_name) {
                                    echo '<option value="' . $organization_name . '" >' . $organization_name . '</option>';
                                }
                            } else {
                                echo '<option disabledd value> -- בחר/י קורס -- </option>';
                                foreach ($organizations_list as $organization_name) {
                                    if ($organization_name != $member_organization) {
                                        echo '<option value="' . $member_organization . '" >' . $member_organization . '</option>';
                                    } else {
                                        echo '<option selected value="' . $member_organization . '" >' . $member_organization . '</option>';
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="coupon" class="font-weight-bold pl-2">קוד קופון</label>
                    <input class="form-control" name="coupon" id="coupon" type="text" value="<?php echo $coupon; ?>">
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="note" class="font-weight-bold pl-2">הערה</label>
                    <input class="form-control" name="note" id="note" type="text" value="<?php echo $note; ?>">
                </div>
                <hr>

                <div class="col-2 pt-5 text-center">
                    <button type="submit" name="update" class="btn btn-primary mb-2" style="margin-top: 2%;">
                        עדכן משתמש קורס
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php }