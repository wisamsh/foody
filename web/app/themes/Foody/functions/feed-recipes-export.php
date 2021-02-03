<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Foody_feed_recipes_exporter
{
    public static function generate_xlsx($feed_id = '',$title='')
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

        $sheet->setCellValue('A1', 'Recipe Name');
        $sheet->setCellValue('B1', 'Recipe Link');



        $feed_recipes = self::get_all_feed_recipes($feed_id);

        
        $row = 2;

        foreach ($feed_recipes as $recipe){
            $sheet->setCellValue('A'.$row, get_the_title($recipe->meta_value) );
            $sheet->setCellValue('B'.$row, get_permalink($recipe->meta_value) );


            $row++;
        }


//set the header first, so the result will be treated as an xlsx file.
        header('Content-Type: application/vnd.ms-excel');
//make it an attachment so we can define filename
        header('Content-Disposition: attachment;filename="'.$title.'-feed.xls"');

//create IOFactory object
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
//save into php output
        ob_end_clean();
        $writer->save('php://output');

    }


    private static function get_all_feed_recipes($feed_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'postmeta';

        $query = "SELECT meta_value FROM {$table_name} where post_id ={$feed_id} and meta_key like 'blocks_%_items_%_post' and meta_value != ''";

        $posts = $wpdb->get_results($query);

        return $posts;
    }
}
?>