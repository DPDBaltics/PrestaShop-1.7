<?php

namespace Invertus\dpdBaltics\Logger;

use Configuration;
use DPDLog;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\LogsService;
use PrestaShopDatabaseException;
use PrestaShopException;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{

    /**
     * @var LogsService
     */
    private $logsService;

    public function __construct(LogsService $logsService)
    {
        $this->logsService = $logsService;
    }

    const ERROR = 'error';
    const WARNING = 'warning';
    const CRITICAL = 'critical';
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        // TODO: Implement emergency() method.
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = [])
    {
        // TODO: Implement alert() method.
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function critical($message, array $context = [])
    {
        if (!Configuration::get(Config::TRACK_LOGS)) {
            return;
        }
        $log = new DPDLog();
        $log->response = $message;
        $log->request = !empty($context['request']) ? $this->logsService->hideUsernameAndPasswordFromRequest($context['request']) : null;
        $log->status = self::CRITICAL;
        $log->add();
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function error($message, array $context = [])
    {
        if (!Configuration::get(Config::TRACK_LOGS)) {
            return;
        }
        $log = new DPDLog();
        $log->response = $message;
        $log->request = !empty($context['request']) ? $this->logsService->hideUsernameAndPasswordFromRequest($context['request']) : null;
        $log->status = self::ERROR;
        $log->add();
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = [])
    {
        // TODO: Implement warning() method.
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = [])
    {
        // TODO: Implement notice() method.
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = [])
    {
        // TODO: Implement info() method.
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = [])
    {
        // TODO: Implement debug() method.
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        // TODO: Implement log() method.
    }
}
