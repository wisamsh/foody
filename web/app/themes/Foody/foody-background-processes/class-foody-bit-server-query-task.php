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
            /// what supposed to happen if API have an error while fetching transaction status
            /// todo: add mail send t coursesManager
        }

        return false;
    }

    protected function handle_current_bit_status($payment_initiation_id, $member_data, $coupon_details)
    {
//        if (FOODY_BIT_FETCH_STATUS_PROCESS) {
            try {
                $status = get_payment_status($payment_initiation_id, $member_data);
                if (!is_array($status)) {
//                    if (FOODY_BIT_FETCH_STATUS_PROCESS) {
                        bit_handle_status_code($status, $payment_initiation_id, $member_data, $coupon_details);
//                    }
                }
            } catch (Exception $e) {
                throw $e;
            }
//        }
    }
}
