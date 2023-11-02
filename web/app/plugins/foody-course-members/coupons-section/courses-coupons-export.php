<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Foody_courses_coupons_exporter
{
    public static function generate_xlsx($id)
    {
        $coupons_prefix = '';
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

        $sheet->setCellValue('A1', 'Coupon ID');
        $sheet->setCellValue('B1', 'Coupon Code');
        $sheet->setCellValue('C1', 'Used');


        $coupons = self::get_all_unique_coupons_by_id($id);

        $row = 2;

        foreach ($coupons as $coupon){
            $coupon_used = $coupon->used == 1 ? __('used') : __('not used');

            $sheet->setCellValue('A'.$row, $coupon->coupon_id);
            $sheet->setCellValue('B'.$row, $coupon->coupon_prefix.'_'.$coupon->coupon_code);
            $sheet->setCellValue('C'.$row, $coupon_used);
            $row++;
            if(empty($coupons_prefix)){
                $coupons_prefix = $coupon->coupon_prefix;
            }
        }


//set the header first, so the result will be treated as an xlsx file.
        header('Content-Type: application/vnd.ms-excel');
//make it an attachment so we can define filename
        header('Content-Disposition: attachment;filename="'. $coupons_prefix .'-unique-coupons-list.csv"');

//create IOFactory object
        $writer = IOFactory::createWriter($spreadsheet, 'Csv');
//save into php output
        ob_end_clean();
        $writer->save('php://output');
die();
    }


    private static function get_all_unique_coupons_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'foody_unique_coupons_meta';

        $query = "SELECT * FROM {$table_name} where coupon_id = " . $id;

        return $wpdb->get_results($query);
    }
}
?>