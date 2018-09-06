<?php
/**
 * Created by PhpStorm.
 * User: liore
 * Date: 06/02/17
 * Time: 11:23
 */

namespace App\Converter;


use App\WordPressApi;
use Jenssegers\Mongodb\Connection as MongoDBConnection;

class CategoryConverter extends BaseConverter
{
    public $translations;
    public $source;
    private $id;

    /**
     * ArticleConverter constructor.
     * @param MongoDBConnection $originDB
     * @param WordPressApi $wp
     * @param array $category
     */
    public function __construct(MongoDBConnection $originDB, WordPressApi $wp, $category)
    {
        $this->originDB = $originDB;
        $this->wp = $wp;

        $source_id = $this->insertSource($category);
    }


    /**
     *
     *  Insert category of source language (he by default)
     *
     *  If there are no translations, insert only one value based on row in SubCategory table
     *
     * @param string $category
     * @return array|int|\WP_Error
     */
    private function insertSource($category)
    {

        $source_id = wp_insert_term($category, 'category')['term_id'];
        add_term_meta($source_id, 'active', true);
//        add_term_meta($source_id, 'archived', $source->scIsArchive);

        return $source_id;

    }

}