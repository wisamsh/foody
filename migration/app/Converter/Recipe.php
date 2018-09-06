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
use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Connection as MongoDBConnection;

class RecipeConverter extends BaseConverter
{

    private $id;
    private $recipe;
    private $debug;
    private $source;
    private $source_tags;
    private $wp_categories;
    private $source_categories;

    /**
     * RecipeConverter constructor.
     * @param MongoDBConnection $originDB
     * @param WordPressApi $wp
     * @param Collection $recipe
     * @param int|bool $debug
     */
    public function __construct(MongoDBConnection $originDB, WordPressApi $wp, $recipe, $debug = false)
    {
        if (!Cache::get($recipe->artID)) {

            Cache::put($recipe->artID, true);
            $this->wp = $wp;
            $this->recipe = $recipe;
            $this->debug = $debug;
            $this->id = $recipe->_id;
            $this->originDB = $originDB;
            $this->source_tags = collect();
            $this->source_categories = collect();
            $this->wp_categories = get_terms(array(
                'taxonomy' => 'category',
                'hide_empty' => false,
            ));

            $this->insertSource();


            Cache::forget($recipe->_id);
        } else {
            dump('In progress.');
        }

    }



    /**
     *
     *  Update post_name (slug) of newly created wp_posts to legacy artID
     *
     * @param $post_id
     */
    private function updatePostSlugToLegacyId($post_id)
    {

        wp_update_post(array(
            'ID' => $post_id,
            'post_name' => $this->id,
        ));

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

        if ($this->source) {

            $newly_inserted_source_post = wp_insert_post($this->source->get('post_data'));

            if (!$newly_inserted_source_post instanceof \WP_Error) {


                foreach ($this->source->get('meta_data') as $key => $value) {

                    add_post_meta($newly_inserted_source_post, $key, $value);

                }


                $get_language_args = array('element_id' => $newly_inserted_source_post, 'element_type' => 'post_post');
                $source_lang_info = apply_filters('wpml_element_language_details', null, $get_language_args);

                $trid = (!is_object($source_lang_info)) ? 0 : $source_lang_info->trid;
                $set_language_args = array(
                    'element_id' => $newly_inserted_source_post,
                    'element_type' => 'post_post',
                    'trid' => $trid,
                    'language_code' => $this->source->get('lang_code')
                );

                do_action('wpml_set_element_language_details', $set_language_args);


                $this->updatePostSlugToLegacyId($newly_inserted_source_post);

            }


        } else {

            /**
             *
             *  If there are no translations, create a default post based on Article
             *
             */
            $article = [
                'post_data' => [
                    'ID' => 0,
                    'post_author' => 1,
                    'post_date' => $this->recipe->artDateCreated,
                    // 'post_content' => $this->art->artShortDescription,
                    'post_title' => $this->recipe->artName,
                    'post_status' => ($this->recipe->artIsActive == 1) ? 'publish' : 'private',
                    'post_type' => 'post',
                    //  'post_name' => , // SLUG - important
                    'post_modified' => $this->recipe->artDateModified,
                    'post_parent' => 0,
                    'menu_order' => 0,
                    //    'post_category' => ,
                    //   'tax_input' => ,
                    //    'meta_input' => ,

                ],
                'meta_data' => [
                    'source' => $this->recipe->artSource,
                    'doctype' => $this->recipe->artDocType,
                    'malam_id' => $this->recipe->artMalamID,
                    'type' => $this->recipe->artType,
                    'legacy_id' => $this->id,
                    'view_counter' => $this->recipe->artViewCounter,
                    'active' => $this->recipe->artIsActive,
                    'archived' => $this->recipe->artIsArchive,
                ]
            ];

            $newly_inserted_source_post = wp_insert_post($article['post_data']);
            if (!$newly_inserted_source_post instanceof \WP_Error) {


                foreach ($article['meta_data'] as $key => $value) {

                    add_post_meta($newly_inserted_source_post, $key, $value);

                }

            }

            $this->updatePostSlugToLegacyId($newly_inserted_source_post);

        }


        /**
         *
         *  Attach the categories based on legacy_id metadata
         *
         */
        $this->originDB->table('ArticleToSubCategory')
            ->where('atsArticleID', $this->recipe->artID)
            ->get()
            ->pluck('atsSubCategoryID')->each(function ($scID) use ($newly_inserted_source_post) {
                $cat = $this->legacyCategoryToWordpress($scID);

                if ($cat) {
                    $this->source_categories->push($cat);
                    //   echo("Adding category $cat \n");
                    wp_set_post_categories($newly_inserted_source_post, $cat, true);
                }

            });

        /**
         *
         *  Attach the tags based on legacy_id metadata
         *
         */
        $this->originDB->table('ArticleTag')
            ->where('atagArtID', $this->recipe->artID)
            ->get()
            ->pluck('atagTagID')->each(function ($tagID) use ($newly_inserted_source_post) {
                //$tag = $this->legacyTaxonomyToWordpress($tagID, 'post_tag');
                $tag = $this->originDB->table('Tag')
                    ->where('tagID', $tagID)
                    ->first()->tagName;
                if ($tag) {
                    $this->source_tags->push($tag);
                    //   echo("Adding tag  $tag\n");
                    wp_set_post_tags($newly_inserted_source_post, $tag, true);
                }

            });


        return $newly_inserted_source_post;

    }

    /**
     *
     *  Loop through all wp_terms meta to find legacy categories by ID
     *
     * @param $id
     * @return mixed
     */
    private function legacyCategoryToWordpress($id)
    {

        foreach ($this->wp_categories as $term) {

            $legacy_id = get_term_meta($term->term_id, 'legacy_id', true);
            // dump($legacy_id);
            if ($legacy_id == $id) {
                // push the ID into the array

                return $term->term_id;
            }
        }

        return null;

    }



}