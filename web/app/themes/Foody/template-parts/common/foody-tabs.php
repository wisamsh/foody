<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/2/18
 * Time: 12:02 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$tabs = $template_args;

?>
    <section class="foody-tabs">
        <ul class="nav nav-tabs" id="foody-tabs" role="tablist">

            <?php $i = 0;
            foreach ($tabs as $tab): ?>
                <?php
                $classes = 'nav-link';
                if (isset($tab['link_classes'])) {
                    $classes .= " {$tab['link_classes']}";
                }
                ?>
                <li class="nav-item">
                    <a class="<?php echo $classes ?>" id="<?php echo $tab['target'] ?>-tab-link" data-toggle="tab"
                       role="tab"
                       href="#<?php echo $tab['target'] ?>" aria-controls="<?php echo $tab['target'] ?>"
                       aria-selected="<?php echo ($i == 0) ? 'true' : 'false'; ?>">
                        <?php echo $tab['title'] ?>
                    </a>
                </li>
                <?php $i++; ?>
            <?php endforeach; ?>
        </ul>

        <section class="tabs-container tab-content container">

<!--            --><?php
//            echo get_sort_dropdown($tabs[0]['target']);
//            ?>

            <div class="tab-content col">


                <?php foreach ($tabs as $tab): ?>

                    <?php
                    $classes = 'tab-pane fade row gutter-3';
                    if (isset($tab['classes'])) {
                        $classes .= " {$tab['classes']}";
                    }
                    ?>

                    <div class="<?php echo $classes ?>" id="<?php echo $tab['target'] ?>" role="tabpanel"
                         aria-labelledby="<?php echo $tab['target'] ?>-tab-link">

                        <?php echo $tab['content'] ?>

                    </div>

                <?php endforeach; ?>

            </div>
        </section>

    </section>

<?php

function get_sort_dropdown($target)
{
    $select_args = array(
        'id' => 'sort-' . $target,
        'placeholder' => 'סדר על פי',
        'options' => array(
            array(
                'value' => 1,
                'label' => 'א-ת'
            ),
            array(
                'value' => -1,
                'label' => 'ת-א'
            )
        ),
        'return' => true
    );

    return '<div class="sort">' . foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $select_args) . '</div>';

}