<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/25/19
 * Time: 11:29 AM
 */
class Foody_BitServerQueryProcess extends WP_Background_Process
{
    /**
     * @var string
     */
    protected $action = 'foody_bit_server_query_process';

    /**
     * /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over.
     *
     * @return mixed
     * @throws Exception
     */
    protected function task($item)
    {
        try {
            $this->handle_current_bit_status($item['payment_initiation_id'], $item['member_data'], $item['coupon_details']);

        } catch (Exception $e) {
            Foody_WhiteLabelLogger::exception($e);
        }

        return false;
    }

    protected function handle_current_bit_status($payment_initiation_id, $member_data, $coupon_details)
    {
//        $status = get_payment_status($payment_initiation_id, $member_data);
//        if (!is_array($status)) {
//            $continue_interval = bit_handle_status_code($status, $payment_initiation_id, $member_data, $coupon_details);
//            return $continue_interval;
//        }
        $number_of_seconds = 30;
        foody_setInterval(function () use ($payment_initiation_id, $member_data, $coupon_details) {
            $status = get_payment_status($payment_initiation_id, $member_data);
            if (!is_array($status)) {
                $continue_interval = bit_handle_status_code($status, $payment_initiation_id ,$member_data, $coupon_details);
                return $continue_interval;
            }
        }, $number_of_seconds*1000);
    }
}
