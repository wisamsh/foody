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
            if (self::$log == null){
                throw new Exception('Foody_WhiteLabelLogger: instantiated before init() ');
            }
        }
    }

    public static function error($message, $context = [])
    {
        if (self::$log) {
            self::$log->error($message, $context);
        }
    }

    public static function warning($message, $context = [])
    {
        if (self::$log) {
            self::$log->warning($message, $context);
        }
    }

    public static function info($message, $context = [])
    {
        if (self::$log) {
            self::$log->info($message, $context);
        }
    }

    public static function init(\Monolog\Handler\HandlerInterface $handler = null)
    {
        if (empty($handler)) {
            $handler = new StreamHandler(FOODY_LOGGER_PATH . 'logs.log', Monolog\Logger::WARNING);
        }
        // create a log channel
        self::$log = new Logger(self::NAME);
        try {
            self::$log->pushHandler($handler);
        } catch (Exception $e) {
        }
    }


}