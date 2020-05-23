<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Courses_Members_List extends WP_List_Table
{

    protected $members_count = 0;
    public $last_query = '';

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Member'), //singular name of the listed records
            'plural' => __('Members'), //plural name of the listed records
            'ajax' => false //should this table support ajax?

        ]);
    }

    public function get_courses_members()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'foody_courses_members';
        $query = "SELECT * FROM {$table_name}";

        $members = $wpdb->get_results($query);
        $members_list = [];

        foreach ($members as $member) {
            $member_id = $member->member_id;
            $member_email = $member->member_email;
            $full_name = $member->first_name . ' ' . $member->last_name;
            $phone = $member->phone;
            $enable_marketing = $member->marketing_status;
            $course_name = $member->course_name;
            $price_paid = $member->price_paid;
            $organization = $member->organization;
            $payment_method = $member->payment_method;
            $transaction_id = $member->transaction_id;
            $coupon = $member->coupon;
            $purchase_date = $member->purchase_date;
            $note = $member->note;
            $status = $member->status;

            $members_list[$member_id] = array(
                'ID' => $member_id,
                'מייל' => $member_email,
                'שם' => $full_name,
                'טלפון' => $phone,
                'דיוור' => $enable_marketing ? __('אושר') : __('לא אושר'),
                'שם הקורס' => $course_name,
                'סכום ששולם' => $price_paid,
                'שיוך ארגוני' => $organization,
                'סוג תשלום' => $payment_method,
                'מס׳ טרנזקציה' => $transaction_id,
                'קופון' => $coupon,
                'תאריך רכישה' => $purchase_date,
                'זיכוי' => !empty($transaction_id) && $status == 'paid' ? '<div onclick="getRefund(\''. $transaction_id . '\');" style="cursor: pointer; text-decoration: underline; color: blue" >לחץ לזיכוי</div>' : __('לחץ לזיכוי'),
                'הערה' => $note,
                'סטאטוס' => $status,
                'עריכה' => '<div onclick="getUpdate(' . $member_id . ')" style="cursor: pointer; text-decoration: underline; color: blue" >לחץ לעריכה</div>'
            );
        }

        $this->members_count = count($members_list);
        return $members_list;
    }

    public function search_results_to_courses_members($search_results)
    {
        $members_list = [];

        foreach ($search_results as $search_result) {
            $member_id = $search_result['member_id'];
            $member_email = $search_result['member_email'];
            $full_name = $search_result['first_name'] . ' ' . $search_result['last_name'];
            $phone = $search_result['phone'];
            $enable_marketing = $search_result['marketing_status'];
            $course_name = $search_result['course_name'];
            $price_paid = $search_result['price_paid'];
            $payment_method = $search_result['payment_method'];
            $transaction_id = $search_result['transaction_id'];
            $coupon = $search_result['coupon'];
            $purchase_date = $search_result['purchase_date'];
            $organization = $search_result['organization'];
            $note = $search_result['note'];
            $status = $search_result['status'];


            $members_list[$member_id] = array(
                'ID' => $member_id,
                'מייל' => $member_email,
                'שם' => $full_name,
                'טלפון' => $phone,
                'דיוור' => $enable_marketing ? __('אושר') : __('לא אושר'),
                'שם הקורס' => $course_name,
                'סכום ששולם' => $price_paid,
                'שיוך ארגוני' => $organization,
                'סוג תשלום' => $payment_method,
                'מס׳ טרנזקציה' => $transaction_id,
                'קופון' => $coupon,
                'תאריך רכישה' => $purchase_date,
                'זיכוי' => !empty($transaction_id) && $status == 'paid' ? '<div onclick="getRefund(\''. $transaction_id . '\');" style="cursor: pointer; text-decoration: underline; color: blue" >לחץ לזיכוי</div>' : __('לחץ לזיכוי'),
                'הערה' => $note,
                'סטאטוס' => $status,
                'עריכה' => '<div onclick="getUpdate(' . $member_id . ')" style="cursor: pointer; text-decoration: underline; color: blue" >לחץ לעריכה</div>'
            );
        }

        $this->members_count = count($members_list);
        return $members_list;
    }


    public function record_count()
    {
        return $this->members_count;
    }

    /** Text displayed when no Taxis data is available */
    public function no_items()
    {
        _e('No members available.');
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'ID':
            case 'מייל':
            case 'שם':
            case 'טלפון':
            case 'תאריך רכישה':
            case 'דיוור':
            case 'שם הקורס':
            case 'סכום ששולם':
            case 'סוג תשלום':
            case 'מס׳ טרנזקציה':
            case 'קופון':
            case 'זיכוי':
            case 'שיוך ארגוני':
            case 'הערה':
            case 'סטאטוס':
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
            //'cb' => '<input type="checkbox" />',
            'ID' => __('ID'),
            'מייל' => __('מייל'),
            'שם' => __('שם'),
            'טלפון' => __('טלפון'),
            'תאריך רכישה' => __('תאריך רכישה'),
            'דיוור' => __('דיוור'),
            'שם הקורס' => __('שם הקורס'),
            'סכום ששולם' => __('סכום ששולם'),
            'שיוך ארגוני' => __('שיוך ארגוני'),
            'סוג תשלום' => __('סוג תשלום'),
            'מס׳ טרנזקציה' => __('מס׳ טרנזקציה'),
            'קופון' => __('קופון'),
            'זיכוי' => __('זיכוי'),
            'הערה' => __('הערה'),
            'סטאטוס' => __('סטאטוס'),
            'עריכה' => __('עריכה'),
        ];

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'ID' => array('ID', true),
            'מייל' => array('מייל', true),
            'שם' => array('קטגוריות', true),
            'טלפון' => array('טלפון', false),
            'תאריך רכישה' => array('תאריך רכישה', true),
            'דיוור' => array('דיוור', true),
            'שם הקורס' => array('שם הקורס', true),
            'סכום ששולם' => array('סכום ששולם', false),
            'שיוך ארגוני' => array('שיוך ארגוני', true),
            'סוג תשלום' => array('סוג תשלום', true),
            'מס׳ טרנזקציה' => array('מס׳ טרנזקציה', true),
            'קופון' => array('קופון', true),
            'הערה' => array('הערה', false),
            'סטאטוס' => array('סטאטוס', true),
            'עריכה' => array('עריכה', false),
        );

        return $sortable_columns;
    }

    function usort_reorder($a, $b)
    {
        // If no sort, default to title
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'תאריך רכישה';
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

        /* If the value is not NULL, do a search for it. */
        if (is_array($search)) {
            $members_list = $this->advanced_search($search);
        } elseif (isset($_POST['export_clicked']) && $_POST['export_clicked'] == 'true') {
            if (isset($_POST['search'])) {
                $export_query = $this->get_regular_search_query($_POST['search']);
            } else {
                $export_query = $this->get_advanced_search_query($_POST);
            }
            Foody_courses_members_exporter::generate_xlsx($export_query);
        } else {
            /** regular search */
            if (isset($_POST['s']) && !empty($search)) {
                $search_query = $this->get_regular_search_query($search);
                $members_list = $wpdb->get_results($search_query, ARRAY_A);
                $members_list = $this->search_results_to_courses_members($members_list);
            }
            /** get all members */
            else{
                // $this->_column_headers = array($columns, $hidden, $sortable);
                $members_list = $this->get_courses_members();
            }
        }

        if ($this->record_count() != 0) {
            usort($members_list, array(&$this, 'usort_reorder'));

            /** Process bulk action */
            $this->process_bulk_action();

            $per_page = 10;
            $current_page = $this->get_pagenum();
            $total_items = $this->record_count();

            $this->set_pagination_args([
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page //WE have to determine how many items to show on a page
            ]);

            $members_list = array_slice($members_list, (($current_page - 1) * $per_page), $per_page);
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $members_list;
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
            if($key == 'marketing_filter' && is_int($search[$key])){
                $current_filter = $search[$key];
            }
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
        $table_colomns = ['member_id', 'member_email', 'first_name', 'last_name', 'phone', 'marketing_status', 'course_name', 'price_paid', 'payment_method', 'transaction_id', 'coupon', 'purchase_date', 'status'];
        $table_name = $wpdb->prefix . 'foody_courses_members';

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
            if ($table_colomn == 'first_name' || $table_colomn == 'last_name') {
                if (empty($search_words)) {
                    // only one word
                    array_push($search_words, $search);
                }

                foreach ($search_words as $search_word) {
                    $search_query .= $table_colomn . " LIKE '%%%{$search_word}%%' OR ";
                }

            } else {

                if ($num_of_columns - 1 > $index) {
                    $search_query .= $table_colomn . " LIKE '%%%{$search}%%' OR ";
                } else {
                    $search_query .= $table_colomn . " LIKE '%%%{$search}%%'";
                }
            }
        }

        return $search_query;
    }
}


