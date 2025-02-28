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
        echo '<h2>Generate Recipe XML Files</h2>';
        echo '<p>Click the button below to generate the XML files.</p>';
        echo '<form method="post">'; // Use a form to prevent redirect
        echo '<input type="submit" name="generate_xml" value="Generate XML Files" class="button button-primary">';
        echo '</form>';

        if (isset($_POST['generate_xml'])) { // Check if the form is submitted
            $this->generate_recipe_xml_files();
        }
    }

    public function generate_recipe_xml_files() {
        global $wpdb; // Access the WordPress database object

        $offset = 0;
        $file_counter = 1;
        $generated_files = array();

        while (true) {
            $sql = "SELECT ID, post_title, post_content, post_name FROM {$wpdb->posts} WHERE post_type = 'foody_recipe' LIMIT {$this->recipes_per_file} OFFSET {$offset}"; // Raw SQL query
            $recipes = $wpdb->get_results($sql, ARRAY_A); // Get results as an associative array

            if (empty($recipes)) {
                break; // No more recipes
            }


            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><recipes></recipes>');

            foreach ($recipes as $recipe_data) { // Loop through raw data
                $recipe = $xml->recipes->addChild('recipe');

                $recipe->addChild('id', $recipe_data['ID']);
                $recipe->addChild('title', $recipe_data['post_title']);
                $recipe->addChild('content', $recipe_data['post_content']);
                $recipe->addChild('permalink', get_permalink($recipe_data['ID'])); // Generate permalink

                // Get custom fields using get_post_meta() with the ID
                $ingredients = get_field('ingredients', $recipe_data['ID']);
               // Replace with your custom field name
                $recipe->addChild('ingredients', $ingredients);

                $recipe_channel = get_field("recipe_channel",$recipe_data['ID']);
                // Replace with your custom field name
                $recipe->addChild('recipe_channel', $recipe_channel);


                // Get featured image using get_post_thumbnail_id() and wp_get_attachment_image_url()
                 $image_id = get_post_thumbnail_id($recipe_data['ID']);
                if ($image_id) {
                    $image_url = wp_get_attachment_image_url($image_id, 'full'); // Or any other size
                    $recipe->addChild('featured_image', $image_url);
                }


            }

            $filename = 'recipes_' . $file_counter . '.xml';
            $filepath = ABSPATH . 'wp-content/uploads/recipes/' . $filename;

            $result = $xml->asXML($filepath);

            if ($result) {
                echo '<p>XML file ' . $filename . ' generated successfully.</p>';
                $generated_files[] = content_url('/uploads/recipes/' . $filename);
            } else {
                echo '<p>Error generating XML file ' . $filename . '.</p>';
            }

            $offset += $this->recipes_per_file;
            $file_counter++;
        }

        if (!empty($generated_files)) {
            echo '<h2>Downloadable XML Files:</h2>';
            echo '<ul>';
            foreach ($generated_files as $file_url) {
                echo '<li><a href="' . $file_url . '" download>' . basename($file_url) . '</a></li>';
            }
            echo '</ul>';
        }
    }


}

$recipe_xml_generator = new RecipeXMLGenerator();

?>