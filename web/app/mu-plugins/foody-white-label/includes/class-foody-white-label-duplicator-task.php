<?php /** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/17/19
 * Time: 7:01 PM
 */
class Foody_WhiteLabelDuplicatorTask extends WP_Async_Task
{
    protected $action = 'wp_dashboard_setup';

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
        return [];
    }

    /**
     * Run the async task action
     */
    protected function run_action()
    {
        if (is_multisite() && is_main_site()) {
            $max_execution_time = ini_get('max_execution_time');
            ini_set('max_execution_time', 300);
            Foody_WhiteLabelDuplicator::whiteLabelCreate(2);
            ini_set('max_execution_time', $max_execution_time);
        }
    }



}