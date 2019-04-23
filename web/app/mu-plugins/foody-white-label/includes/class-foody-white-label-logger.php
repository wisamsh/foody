<?php /** @noinspection PhpUndefinedClassInspection */

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/18/19
 * Time: 4:09 PM
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Foody_WhiteLabelLogger
{

    const NAME = 'foody-log';

    /**
     * @var $log Monolog\Logger
     */
    private static $log;

    /**
     * Foody_WhiteLabelLogger constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (defined('WP_ENV') && WP_ENV != 'production') {
            if (self::$log == null) {
                throw new Exception('Foody_WhiteLabelLogger: instantiated before init() ');
            }
        }
    }

    public static function error($message, $context = [])
    {
        $processed = false;
        if (self::$log) {
            $processed = self::$log->error($message, $context);
        }

        return $processed;
    }

    public static function warning($message, $context = [])
    {
        $processed = false;
        if (self::$log) {
            $processed = self::$log->warning($message, $context);
        }
        return $processed;
    }

    public static function info($message, $context = [])
    {
        $processed = false;
        if (self::$log) {
            $processed = self::$log->info($message, $context);
        }
        return $processed;
    }

    /**
     * @param \Monolog\Handler\HandlerInterface|null $handler
     * @throws Exception
     */
    public static function init(\Monolog\Handler\HandlerInterface $handler = null)
    {
        if (empty($handler)) {
            $handler = new StreamHandler(FOODY_LOGGER_PATH . 'logs.log');
        }
        // create a log channel
        self::$log = new Logger(self::NAME);
        try {
            self::$log->pushHandler($handler);
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                throw $e;
            }
        }
    }


}