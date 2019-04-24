<?php /** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/17/19
 * Time: 7:01 PM
 */
class Foody_WhiteLabelDuplicatorTask extends WP_Async_Task
{
    protected $action = 'wpmu_new_blog';

    protected $argument_count = 6;

    /**
     * Prepare data for the asynchronous request
     *
     * @throws Exception If for any reason the request should not happen
     *
     * @param array $data An array of data sent to the hook
     *
     * @return array
     */
    protected function prepare_data($data)
    {
        return ['blog_id' => $data[0]];
    }

    /**
     * Run the async task action
     */
    protected function run_action()
    {
        $blog_id = isset($_POST['blog_id']) ? $_POST['blog_id'] : 0;
        if (!empty($blog_id)) {
            $max_execution_time = ini_get('max_execution_time');
            ini_set('max_execution_time', 300);
            Foody_WhiteLabelDuplicator::whiteLabelCreate($blog_id);
            ini_set('max_execution_time', $max_execution_time);
        } else {
            Foody_WhiteLabelLogger::warning("content duplicator task called with invalid blog id", $_POST);
        }
    }
}

try {
    new Foody_WhiteLabelDuplicatorTask();
} catch (Exception $e) {
    Foody_WhiteLabelLogger::exception($e);
}