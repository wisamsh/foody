<?php
if (isset($_GET['update']) && $_GET['update'] && isset($_GET['id'])) {
    global $wpdb;
    $id = $_GET['id'];
    $table_name = $wpdb->prefix . 'foody_courses_members';
    $result = $wpdb->get_results("SELECT * FROM $table_name WHERE member_id='$id'");
//    foreach ($result as $taxi) {
//        $id = $taxi->id;
//        $plate_id = $taxi->plate_id;
//        $color = $taxi->color;
//        $model = $taxi->model;
//        $year = $taxi->year;
//        $lat = $taxi->lat;
//        $lng = $taxi->lng;
//    }

    $organizations_list = get_orginazations_list();
    $courses_list = get_courses_list();

    ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        .required-astrix {
            color: red;
        }
    </style>

    <h2>עדכון משתמש קורס</h2>
    <div class="row" style="width: 20%; line-height: 35px; margin-right: 3%;">
        <form action="admin.php?page=foody-course-members%2Fcourse-members-manage.php" method="post">
            <div class="form-row pt-3">
                <div class="col-1 offset-md-1 p-3">
                    <label for="purchase-date" class="font-weight-bold pl-2">תאריך רכישה</label>
                    <input class="form-control" name="purchase_date" id="purchase-date" type="date">
                </div>
                <hr>
                <div class="col-1 offset-md-1 p-3">
                    <label for="member-email" class="font-weight-bold pl-2">מייל</label>
                    <input class="form-control" name="member_email" id="member-email" type="email">
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="first-name" class="font-weight-bold pl-2">שם פרטי</label>
                    <input class="form-control" name="first_name" id="first-name" type="text">
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="last-name" class="font-weight-bold pl-2">שם משפחה</label>
                    <input class="form-control" name="last_name" id="last-name" type="text">
                </div>
                <hr>

                <div class="col-1 p-3">
                    <label for="phone" class="font-weight-bold pl-2"></label>
                    <input class="form-control" name="phone" id="phone" type="tel">
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="course-name" class="font-weight-bold pl-2" style="margin-left: 2%">שם קורס</label>
                    <select id="course-name" name="course_name">
                        <?php
                        if (!empty($courses_list)) {
                            echo '<option selected value> -- בחר/י קורס -- </option>';
                            foreach ($courses_list as $course_name) {
                                echo '<option value="' . $course_name . '" >' . $course_name . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <hr>

                <div class="col-2 p-3">
                    <label for="course-price" class="font-weight-bold pl-2" style="margin-left: 2%">מחיר ששולם</label>
                    <input name="course_price" id="course-price" type="number" placeholder="0.0" step="0.01" min="0">
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="payment-method" class="font-weight-bold pl-2" style="margin-left: 2%">אמצעי
                        תשלום</label>
                    <select id="payment-method" name="payment_method">
                        <option disabled selected value> -- בחר/י שיטת תשלום --</option>
                        <option value="ביט"> ביט</option>
                        <option value="כרטיס אשראי"> כרטיס אשראי</option>
                        <option value="כרטיס אשראי"> ועד עובדים</option>
                        <option value="buy me">buy me</option>
                        <option value="כרטיס אשראי"> אחר</option>
                    </select>
                </div>
                <hr>
                <div class="col-2 p-3">
                    <label for="organization" class="font-weight-bold pl-2" style="margin-left: 2%">שם ארגון</label>
                    <select id="organization" name="organization">
                        <?php
                        if (!empty($organizations_list)) {
                            echo '<option selected value> -- בחר/י ארגון -- </option>';
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
                    <label for="note" class="font-weight-bold pl-2">הערה</label>
                    <input class="form-control" name="note" id="note" type="text">
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