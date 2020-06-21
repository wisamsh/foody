<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Foody_courses_members_exporter
{
    public static function generate_xlsx($query = '')
    {
        ob_end_clean();
        ob_start();
        //make a new spreadsheet object
        $spreadsheet = new Spreadsheet();

        //get current active sheet (first sheet)
        $sheet = $spreadsheet->getActiveSheet();

        //set default font
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(10);

        $sheet->setCellValue('A1', 'First Name');
        $sheet->setCellValue('B1', 'Last Name');
        $sheet->setCellValue('C1', 'Phone');
        $sheet->setCellValue('D1', 'Mail');
        $sheet->setCellValue('E1', 'Marketing Status');
        $sheet->setCellValue('F1', 'Course Name');
        $sheet->setCellValue('G1', 'Price Paid');
        $sheet->setCellValue('H1', 'Organization');
        $sheet->setCellValue('I1', 'Payment Method');
        $sheet->setCellValue('J1', 'Transaction Id');
        $sheet->setCellValue('K1', 'Coupon');
        $sheet->setCellValue('L1', 'Purchase Date');
        $sheet->setCellValue('M1', 'Note');
        $sheet->setCellValue('N1', 'status');

        $courses_members = self::get_all_courses_members($query);

        $row = 2;

        //member_id, member_email, first_name, last_name, phone, marketing_status, course_name, price_paid, organization, payment_method, transaction_id, coupon, purchase_date, note


        foreach ($courses_members as $courses_member){
            $sheet->setCellValue('A'.$row, $courses_member->first_name);
            $sheet->setCellValue('B'.$row, $courses_member->last_name);
            $sheet->setCellValue('C'.$row, $courses_member->phone);
            $sheet->setCellValue('D'.$row, $courses_member->member_email);
            $sheet->setCellValue('E'.$row, $courses_member->marketing_status);
            $sheet->setCellValue('F'.$row, $courses_member->course_name);
            $sheet->setCellValue('G'.$row, $courses_member->price_paid);
            $sheet->setCellValue('H'.$row, $courses_member->organization);
            $sheet->setCellValue('I'.$row, $courses_member->payment_method);
            $sheet->setCellValue('J'.$row, $courses_member->transaction_id);
            $sheet->setCellValue('K'.$row, $courses_member->coupon);
            $sheet->setCellValue('L'.$row, $courses_member->purchase_date);
            $sheet->setCellValue('M'.$row, $courses_member->note);
            $sheet->setCellValue('N'.$row, $courses_member->status);
            $row++;
        }


//set the header first, so the result will be treated as an xlsx file.
        header('Content-Type: application/vnd.ms-excel');
//make it an attachment so we can define filename
        header('Content-Disposition: attachment;filename="Courses-members.xlsx"');

//create IOFactory object
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
//save into php output
        ob_end_clean();
        $writer->save('php://output');

    }


    private static function get_all_courses_members($saved_query)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'foody_courses_members';

        if($saved_query == '') {
            $query = "SELECT * FROM {$table_name};";
        }
        else{
            $query = $saved_query;
        }

        $posts = $wpdb->get_results($query);

        return $posts;
    }
}
?>