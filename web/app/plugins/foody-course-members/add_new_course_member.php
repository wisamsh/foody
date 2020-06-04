<?php
$courses_list = get_courses_list();
$organizations_list = get_orginazations_list();
?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .required-astrix {
            color: red;
        }
    </style>

    <h2>משתמש קורס חדש</h2>
    <div class="row" style="width: 20%; line-height: 35px; margin-right: 3%;">
        <form action="admin.php?page=foody-course-members%2Fcourse-members-manage.php" method="post">
            <div class="form-row pt-3">
                <div class="col-1 offset-md-1 p-3">
                    <label for="purchase-date" class="font-weight-bold pl-2">תאריך רכישה<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="purchase_date" id="purchase-date" type="date" required>
                </div>
                <hr>
                <div class="col-1 offset-md-1 p-3">
                    <label for="member-email" class="font-weight-bold pl-2">מייל<span
                                class="required-astrix">*</span></label>
                    <input class="form-control" name="member_email" id="member-email" type="email" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="first-name" class="font-weight-bold pl-2">שם פרטי<span class="required-astrix">*</span></label>
                    <input class="form-control" name="first_name" id="first-name" type="text" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="last-name" class="font-weight-bold pl-2">שם משפחה<span class="required-astrix">*</span></label>
                    <input class="form-control" name="last_name" id="last-name" type="text" required>
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="phone" class="font-weight-bold pl-2">טלפון<span class="required-astrix">*</span></label>
                    <input class="form-control" name="phone" id="phone" type="tel" required>
                </div>
                <hr>

                <div class="col-2 p-3" style="display: flex; flex-direction: column">
                    <h5 style="text-decoration: underline">דיוור<span class="required-astrix">*</span></h5>
                    <label style="margin-left: 2%;">אישר דיוור
                        <input type="radio" name="enable_marketing" value="true" required="true"/>
                    </label>
                    <label style="margin-left: 2%">לא אישר דיוור
                        <input type="radio" name="enable_marketing" value="false"/>
                    </label>
                </div>
                <hr>
                <!---->
                <!--                <div class="col-2 p-3">-->
                <!--                    <label for="course-name" class="font-weight-bold pl-2" >שם קורס<span class="required-astrix">*</span></label>-->
                <!--                    <input class="form-control" name="course_name" id="course-name" type="text" required>-->
                <!--                </div>-->
                <!--                <hr>-->
                <div class="col-2 p-3">
                    <label for="course-name" class="font-weight-bold pl-2" style="margin-left: 2%">שם קורס<span
                                class="required-astrix">*</span></label>
                    <select id="course-name" name="course_name" required>
                        <?php
                        if (!empty($courses_list)) {
                            echo '<option disabled selected value> -- בחר/י קורס -- </option>';
                            foreach ($courses_list as $index => $course_name) {
                                echo '<option value="' . $index . ':' . $course_name . '" >' . $course_name . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <hr>

                <div class="col-2 p-3">
                    <label for="course-price" class="font-weight-bold pl-2" style="margin-left: 2%">מחיר ששולם<span
                                class="required-astrix">*</span></label>
                    <input name="course_price" id="course-price" type="number" placeholder="0.0" step="0.01" min="0"
                           required>
                </div>
                <hr>

                <div class="col-2 p-3">
                    <label for="payment-method" class="font-weight-bold pl-2" style="margin-left: 2%">אמצעי
                        תשלום<span class="required-astrix">*</span></label>
                    <select id="payment-method" name="payment_method" required>
                        <option disabled selected value> -- בחר/י שיטת תשלום --</option>
                        <option value="ביט"> ביט</option>
                        <option value="כרטיס אשראי"> כרטיס אשראי</option>
                        <option value="ועד עובדים"> ועד עובדים</option>
                        <option value="buy me">buy me</option>
                        <option value="אחר"> אחר</option>
                    </select>
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
                    <label for="coupon" class="font-weight-bold pl-2">קוד קופון</label>
                    <input class="form-control" name="coupon" id="coupon" type="text">
                </div>
                <hr>

                <div class="col-2 p-3">
                    <label for="transaction-id" class="font-weight-bold pl-2">מזהה טרנזקציה</label>
                    <input class="form-control" name="transaction_id" id="transaction-id" type="text">
                </div>
                <hr>

                <div class="col-2 p-3" style="display: flex; flex-direction: column">
                    <h5 style="text-decoration: underline">העברת פרטי המשתמש<span class="required-astrix">*</span></h5>
                    <label style="margin-left: 2%;">להעביר פרטים לסקולר
                        <input type="radio" name="send_data" value="true" required="true"/>
                    </label>
                    <label style="margin-left: 2%"> לא להעביר פרטים לסקולר
                        <input type="radio" name="send_data" value="false"/>
                    </label>
                </div>
                <hr>

                <div class="col-2 p-3">
                    <label for="note" class="font-weight-bold pl-2">הערה</label>
                    <input class="form-control" name="note" id="note" type="text">
                </div>
                <hr>

                <div class="col-2 pt-5 text-center">
                    <button type="submit" name="new_member" class="btn btn-primary mb-2" style="margin-top: 2%;">
                        משתמש קורס חדש
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php
