<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Foody_ingredients_exporter
{
    public static function generate_xlsx()
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

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Ingredient');

        $ingredients = self::get_all_new_ingredients();

        $row = 2;

        foreach ($ingredients as $ingredient){
            $sheet->setCellValue('A'.$row, $ingredient->ID);
            $sheet->setCellValue('B'.$row, $ingredient->post_title);
            $row++;
        }


//set the header first, so the result will be treated as an xlsx file.
        header('Content-Type: application/vnd.ms-excel');
//make it an attachment so we can define filename
        header('Content-Disposition: attachment;filename="new-ingredients.xls"');

//create IOFactory object
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
//save into php output
        ob_end_clean();
        $writer->save('php://output');

    }


    private static function get_all_new_ingredients()
    {
        global $wpdb;

        $query = "SELECT * FROM $wpdb->postmeta as postmeta 
JOIN $wpdb->posts as posts
where posts.ID = postmeta.post_id 
AND meta_key = 'nutrients'
AND (meta_value = '' or meta_value IS NULL or meta_value = \"\")
AND post_status = 'publish';";

        $posts = $wpdb->get_results($query);

        return $posts;
    }

}
?>