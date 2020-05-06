<?php


if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Coupons_List extends WP_List_Table
{

    protected $coupons_count = 0;

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Coupon'), //singular name of the listed records
            'plural' => __('Coupons'), //plural name of the listed records
            'ajax' => false //should this table support ajax?

        ]);
    }

    public function get_coupons()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'foody_courses_coupons';
        $query = "SELECT * FROM {$table_name}";

        $coupons = $wpdb->get_results($query);
        $coupons_list = [];

        foreach ($coupons as $coupon) {
            $coupon_id = $coupon->coupon_id;
            $coupon_name = $coupon->coupon;
            $coupon_type = $coupon->coupon_type;
            $course_name = $coupon->course_name;
            $creation_date = $coupon->creation_date;
            $expiration_date = $coupon->expiration_date;
            $coupon_value = $coupon->coupon_value;
            $organization = $coupon->organization;
            $max_amount = $coupon->max_amount;
            $used_amount = $coupon->used_amount;
            $invoice_desc = $coupon->invoice_desc;

            $coupons_list[$coupon_id] = array(
                'ID' => $coupon_id,
                'קופון' => $coupon_name,
                'סוג קופון' => $coupon_type,
                'שם קורס' => $course_name,
                'תאריך יצירה' => $creation_date,
                'תאריך תפוגה' => $expiration_date,
                'ערך קופון' => $coupon_value,
                'שיוך אגוני' => $organization,
                'מספר קופונים' => $max_amount,
                'נוצלו' => $used_amount,
                'תיאור לחשבונית' => $invoice_desc,
                'עריכה' => '<div onclick="getUpdate(' . $coupon_id . ')" style="cursor: pointer; text-decoration: underline; color: blue" >לחץ לעריכה</div>'
            );

            if ($coupon_type == 'חח״ע') {
                $coupons_list[$coupon_id]['ייצא קופונים'] = '<form method="post" id="export-unique-coupons-form"><input type="text" id="unique-coupons-id" name="unique_coupons_id" value="' . $coupon_id . '" hidden><input type="submit" id="export-unique-coupons-submit" class="button" value="ייצא קופונים"></form>';
            } else {
                $coupons_list[$coupon_id]['ייצא קופונים'] = '';
            }
        }

        $this->coupons_count = count($coupons_list);
        return $coupons_list;
    }

    public function search_results_to_coupons($search_results)
    {
        $coupons_list = [];

        foreach ($search_results as $search_result) {
            $coupon_id = $search_result['coupon_id'];
            $coupon_name = $search_result['coupon'];
            $coupon_type = $search_result['coupon_type'];
            $course_name = $search_result['course_name'];
            $creation_date = $search_result['creation_date'];
            $expiration_date = $search_result['expiration_date'];
            $coupon_value = $search_result['coupon_value'];
            $organization = $search_result['organization'];
            $max_amount = $search_result['max_amount'];
            $used_amount = $search_result['used_amount'];
            $invoice_desc = $search_result['invoice_desc'];


            $coupons_list[$coupon_id] = array(
                'ID' => $coupon_id,
                'קופון' => $coupon_name,
                'סוג קופון' => $coupon_type,
                'שם קורס' => $course_name,
                'תאריך יצירה' => $creation_date,
                'תאריך תפוגה' => $expiration_date,
                'ערך קופון' => $coupon_value,
                'שיוך אגוני' => $organization,
                'מספר קופונים' => $max_amount,
                'נוצלו' => $used_amount,
                'תיאור לחשבונית' => $invoice_desc,
                'עריכה' => '<div onclick="getUpdate(' . $coupon_id . ')" style="cursor: pointer; text-decoration: underline; color: blue" >לחץ לעריכה</div>'
            );

            if ($coupon_type == 'חח״ע') {
                $coupons_list[$coupon_id]['ייצא קופונים'] = '<form method="post" id="export-unique-coupons-form"><input type="text" id="unique-coupons-id" name="unique_coupons_id" value="' . $coupon_id . '" hidden><input type="submit" id="export-unique-coupons-submit" class="button" value="ייצא קופונים"></form>';
            } else {
                $coupons_list[$coupon_id]['ייצא קופונים'] = '';
            }
        }

        $this->coupons_count = count($coupons_list);
        return $coupons_list;
    }


    public function record_count()
    {
        return $this->coupons_count;
    }

    /** Text displayed when no Taxis data is available */
    public function no_items()
    {
        _e('No coupons available.');
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'ID':
            case 'קופון':
            case 'סוג קופון':
            case 'שם קורס':
            case 'תאריך יצירה':
            case 'תאריך תפוגה':
            case 'ערך קופון':
            case 'שיוך אגוני':
            case 'מספר קופונים':
            case 'נוצלו':
            case 'תיאור לחשבונית':
            case 'ייצא קופונים':
            case 'עריכה':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    function get_columns()
    {
        $columns = [
            'ID' => 'ID',
            'קופון' => __('קופון'),
            'סוג קופון' => __('סוג קופון'),
            'שם קורס' => __('שם קורס'),
            'תאריך יצירה' => __('תאריך יצירה'),
            'תאריך תפוגה' => __('תאריך תפוגה'),
            'ערך קופון' => __('ערך קופון'),
            'שיוך אגוני' => __('שיוך אגוני'),
            'מספר קופונים' => __('מספר קופונים'),
            'נוצלו' => __('נוצלו'),
            'תיאור לחשבונית' => __('תיאור לחשבונית'),
            'ייצא קופונים' => __('ייצא קופונים'),
            'עריכה' => __('עריכה'),
        ];

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'ID' => array('ID', true),
            'קופון' => array('קופון', true),
            'סוג קופון' => array('סוג קופון', true),
            'שם קורס' => array('שם קורס', true),
            'תאריך יצירה' => array('תאריך יצירה', true),
            'תאריך תפוגה' => array('תאריך תפוגה', true),
            'ערך קופון' => array('ערך קופון', true),
            'שיוך אגוני' => array('שיוך אגוני', true),
            'מספר קופונים' => array('מספר קופונים', true),
            'נוצלו' => array('נוצלו', true),
            'תיאור לחשבונית' => array('תיאור לחשבונית', false),
            'ייצא קופונים' => array('ייצא קופונים', false),
        );

        return $sortable_columns;
    }

    function usort_reorder($a, $b)
    {
        // If no sort, default to title
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'תאריך יצירה';
        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items($search = NULL)
    {
        global $wpdb;

        $columns = $this->get_columns();

        $hidden = array();

        $sortable = $this->get_sortable_columns();

        // $this->_column_headers = array($columns, $hidden, $sortable);
        $coupons_list = $this->get_coupons();

        if ($search) {
            /** regular search */
            $search_query = $this->get_regular_search_query($search);
            $coupons_list = $wpdb->get_results($search_query, ARRAY_A);
            $coupons_list = $this->search_results_to_coupons($coupons_list);
        }


        if ($this->record_count() != 0) {
            usort($coupons_list, array(&$this, 'usort_reorder'));

            /** Process bulk action */
            $this->process_bulk_action();

            $per_page = 10;
            $current_page = $this->get_pagenum();
            $total_items = $this->record_count();

            $this->set_pagination_args([
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page //WE have to determine how many items to show on a page
            ]);

            $members_list = array_slice($coupons_list, (($current_page - 1) * $per_page), $per_page);
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $coupons_list;
        }
    }

    public function process_bulk_action()
    {
    }

    private function advanced_search($search)
    {
        global $wpdb;

        $search_query = $this->get_advanced_search_query($search);

//        $this->last_query = $search_query;
        $members_list = $wpdb->get_results($search_query, ARRAY_A);
        $members_list = $this->search_results_to_courses_members($members_list);

        return $members_list;
    }

    private function get_advanced_search_query($search)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'foody_courses_members';

        $filters_list = ['payment_filter' => 'payment_method',
            'course_filter' => 'course_name',
            'marketing_filter' => 'marketing_status',
            'organization_filter' => 'organization',
            'coupon_filter' => 'coupon'
        ];
        $date_from = !empty($search['date_from']) ? $search['date_from'] : false;
        $date_to = !empty($search['date_to']) ? $search['date_to'] : false;

        $search_query = "SELECT * FROM {$table_name}";
        $added_and = ' AND ';
        $not_first = false;

        if ($date_from || $date_to) {
            if ($date_from && !$date_to) {
                $search_query .= " WHERE purchase_date >= '{$date_from}'";
            } elseif (!$date_from && $date_to) {
                $search_query .= " WHERE purchase_date <= '{$date_to}'";
            } else {
                $search_query .= " WHERE purchase_date BETWEEN '{$date_from}' AND '{$date_to}'";
            }
            $not_first = true;
        }

        foreach ($filters_list as $key => $filter) {
            $current_filter = !empty($search[$key]) ? $search[$key] : false;
            if ($current_filter) {
                if ($not_first) {
                    $search_query .= $added_and . "{$filter} = '{$current_filter}'";
                } else {
                    $search_query .= " WHERE {$filter} = '{$current_filter}'";
                    $not_first = true;
                }
            }
        }
        return $search_query;
    }

    private function get_regular_search_query($search)
    {
        global $wpdb;
        $table_colomns = ['coupon_id', 'coupon', 'coupon_type', 'course_name', 'creation_date', 'expiration_date', 'coupon_value', 'organization', 'max_amount', 'used_amount', 'invoice_desc'];
        $table_name = $wpdb->prefix . 'foody_courses_coupons';

        $search_words = [];
        $num_of_columns = count($table_colomns);
        // Trim Search Term
        $search = trim($search);
        if (strpos($search, ' ')) {
            $search_words = explode(' ', $search);
        }
        $search_query = "SELECT * FROM {$table_name} WHERE ";
        /* Notice how you can search multiple columns for your search term easily, and return one data set */
        foreach ($table_colomns as $index => $table_colomn) {
            if ($num_of_columns - 1 > $index) {
                $search_query .= $table_colomn . " LIKE '%%%{$search}%%' OR ";
            } else {
                $search_query .= $table_colomn . " LIKE '%%%{$search}%%'";
            }
        }

        return $search_query;
    }
}


