<?php
$get_questions_auto = get_field('auto_related_quesrions', get_the_ID());
$related_questions_client = array();
switch ($get_questions_auto) {
        //if auto pick random questions from the same caterory.
    case 1:
        $args_related_quetions = array(
            'numberposts'      => 4,
            'category'         => $category,
            'orderby'          => 'rand',
            'post_type'        => 'questions',
            'post__not_in' => array(get_the_ID()),
            'suppress_filters' => false,

        );
        $related_Questions = get_posts($args_related_quetions);
        if (!empty($related_Questions)) {
            echo '<h2 class="title">שאלות קשורות</h2>';
            echo '<ul class="related_questions_queue">';
            foreach ($related_Questions as $related_Questions) {
                echo '<li><b><a href="/questions/' . $related_Questions->post_name . '">' . $related_Questions->post_title . '</a></b></li>';
            }
            echo '</ul';
        }
        break;

    case 0:
$picked_questions = get_field('related_questions', get_the_ID());
if (!empty($picked_questions)) {
    echo '<h2 class="title">שאלות קשורות</h2>';
    echo '<ul class="related_questions_queue">';
    foreach ($picked_questions as $picked_questions) {
        echo '<li><b><a href="/questions/' . $picked_questions['related_questions_rep']->post_name . '">' . $picked_questions['related_questions_rep']->post_title . '</a></b></li>';
    }
    echo '</ul';
}

        
        break;
}
