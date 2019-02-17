<?php
/**
 * Created by PhpStorm.
 * User: liore
 * Date: 06/02/17
 * Time: 11:23
 */

namespace App\Converter;


use App\WordPressApi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\SqlServerConnection;

class TagConverter extends BaseConverter
{
    public $translations;
    public $source;
    private $id;

    /**
     * TagConverter constructor.
     * @param SqlServerConnection $originDB
     * @param WordPressApi $wp
     * @param int $ttID
     */
    public function __construct(SqlServerConnection $originDB, WordPressApi $wp, $ttID)
    {
        $this->originDB = $originDB;
        $this->wp = $wp;
        $this->translations = new Collection();
        $this->id = $ttID;

        $this->getTranslations(
            $this->originDB
                ->table('TagTranslation')
                ->selectRaw('ttID,
                        ttTagID,
                        ttName,
                        ttLangID,
                        ttIsActive,
                        ttIsArchive
                  ')
                ->where('ttTagID', intval($ttID))
                ->get()
        );


        $source_id = $this->insertSource();

        $this->insertTranslations($source_id);


    }

    /**
     *
     *  Get all translations for input from legacy DB rows
     *
     * @param $legacy_results
     */
    private function getTranslations($legacy_results)
    {


        foreach ($legacy_results as $result) {


            $this->translations->put($this->lang_code[$result->ttLangID], collect([
                'wp_term' => [
                    'name' => $result->ttName,
                    'type' => 'post_tag',
                ],
                'wp_term_meta' => [
                    'legacy_id' => $result->ttTagID,
                    'active' => $result->ttIsActive,
                    'archived' => $result->ttIsArchive
                ]
            ]));

        }

        //set source/translation objects
        $this->source = $this->translations->get('he');
        $this->translations->pull('he');

    }


    /**
     *
     *  Insert category of source language (he by default)
     *
     *  If there are no translations, insert only one value based on row in SubCategory table
     *
     * @return array|int|\WP_Error
     */
    private function insertSource()
    {
        $source_id = 0;
        if($this->source) {

            $source_id = wp_insert_term($this->source->get('wp_term')['name'], $this->source->get('wp_term')['type']);

            if (!$source_id instanceof \WP_Error) {

                $source_id = $source_id['term_id'];

                foreach ($this->source->get('wp_term_meta') as $key => $value) {

                    add_term_meta($source_id, $key, $value);

                }

            }
        } else {

            $source = $this->originDB
                ->table('Tag')
                ->selectRaw('tagID,
                        tagName,
                        tagIsActive,
                        tagIsArchive
                  ')
                ->where('tagID', intval($this->id))
                ->first();
            $source_id = wp_insert_term($source->scName, 'post_tag')['term_id'];
            add_term_meta($source_id, 'active', $source->scIsActive);
            add_term_meta($source_id, 'archived', $source->scIsArchive);

        }

        return $source_id;

    }

    /**
     *
     *  Insert translations (non-he) into WP DB and associate them with source
     *
     * @param $source_id
     */
    private function insertTranslations($source_id)
    {

        foreach ($this->translations as $lang_code => $translation) {


            $translation_id = wp_insert_term($translation->get('wp_term')['name'], $translation->get('wp_term')['type']);

            if ( ! $translation_id instanceof \WP_Error ) {

                $translation_id = $translation_id['term_id'];

                foreach ($translation->get('wp_term_meta') as $key => $value) {

                    add_term_meta($translation_id, $key, $value);

                }


                $get_language_args = array('element_id' => $source_id, 'element_type' => 'tax_post_tag');
                $source_lang_info = apply_filters('wpml_element_language_details', null, $get_language_args);

                $set_language_args = array(
                    'element_id' => $translation_id,
                    'element_type' => 'tax_post_tag',
                    'trid' => $source_lang_info->trid,
                    'language_code' => $lang_code,
                    'source_language_code' => $source_lang_info->language_code
                );

                do_action('wpml_set_element_language_details', $set_language_args);
            }
        }


    }


}