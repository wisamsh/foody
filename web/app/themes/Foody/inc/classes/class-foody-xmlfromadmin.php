<?php

class RecipeXMLGenerator {

    private $recipes_per_file = 500;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=foody_recipe',
            'Generate Recipe XML',
            'Generate Recipe XML',
            'manage_options',
            'generate-recipe-xml',
            array($this, 'generate_xml_page')
        );
    }

    public function generate_xml_page() {
        echo '<h2>Foody Recipies  XML Export</h2>';
        echo '<hr></hr>';
        echo '<form method="post">'; // Use a form to prevent redirect
        echo '<input type="submit" name="generate_xml_table" id="generate_xml_table" value="Generate_XML_Table" class="button button-primary" 
        style="margin-right:20px;" >';
        echo '<input type="submit" name="generate_xml" id="generate_xml" value="Generate XML Files" class="button button-primary">';
        echo '</form>';

        if (isset($_POST['generate_xml'])) { // Check if the form is submitted
            $this->generate_recipe_xml_files();
        }
        if (isset($_POST['generate_xml_table'])) { // Check if the form is submitted
            echo $_POST['generate_xml_table'];
        }
    }

    public function generate_recipe_xml_files() 
    {
        
        if (!is_admin() || !current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
    
        // Offset and limit from GET, or use defaults
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : $this->limit;
    
        // Call the XML export directly
        $this->export_to_xml($offset, $limit);
        exit;
    }


   public function clean_for_xml($text) {
        // Convert known HTML entities to UTF-8 chars
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
        // Remove any remaining entities like &lsaquo; that are unknown in XML
        $text = preg_replace('/&[a-zA-Z0-9#]+;/', '', $text);
    
        // Escape special XML chars
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
    
    
    private function export_to_xml($offset, $limit) {
        $posts = get_posts([
            'post_type'      => 'foody_recipe',
            'post_status'    => 'publish',
            'numberposts'    => $limit,
            'offset'         => $offset,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
    
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="foody_recipes_' . $offset . '_to_' . ($offset + $limit) . '.xml"');
    
        $xml = new SimpleXMLElement('<recipes/>');
    
        foreach ($posts as $post) {
            $item = $xml->addChild('recipe');
            $item->addChild('ID', $post->ID);
            $item->addChild('title', ($post->post_title));
            $item->addChild('date', ($post->post_date));
    
            // if (function_exists('get_fields')) {
            //     $fields = get_fields($post->ID);
            //     if ($fields) {
            //         $acf_node = $item->addChild('acf_fields');
            //         foreach ($fields as $key => $value) {
            //             if (is_array($value)) {
            //                 $subnode = $acf_node->addChild($key);
            //                 foreach ($value as $subkey => $subvalue) {
            //                     if (is_array($subvalue)) {
            //                         $nested = $subnode->addChild('item');
            //                         foreach ($subvalue as $nk => $nv) {
            //                             $nested->addChild($nk, clean_for_xml($nv));
            //                         }
            //                     } else {
            //                         $subnode->addChild($subkey, clean_for_xml($subvalue));
            //                     }
            //                 }
            //             } else {
            //                 $acf_node->addChild($key, clean_for_xml($value));
            //             }
            //         }
            //     }
            // }
        }
    
        echo $xml->asXML();
    }
    


}


$recipe_xml_generator = new RecipeXMLGenerator();

?>