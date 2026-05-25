<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace Invertus\dpdBaltics\Logger;

use Configuration;
use DPDLog;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\LogsService;
use PrestaShopDatabaseException;
use PrestaShopException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Logger implements LoggerInterface
{
    /** @var LogsService */
    private $logsService;

    public function __construct(LogsService $logsService)
    {
        $this->logsService = $logsService;
    }

    /**
     * System is unusable.
     *
     * @param \Stringable|string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
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
    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
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
    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
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
    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
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
    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
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
    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
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
    public function log($level, $message, array $context = []): void
    {
        if (!Configuration::get(Config::TRACK_LOGS)) {
            return;
        }

        $log = new DPDLog();
        $log->response = $this->encodeResponse($level, $message, $context);
        $log->request = !empty($context['request'])
            ? $this->encodeRequest($this->logsService->hideUsernameAndPasswordFromRequest($context['request']))
            : null;
        $log->status = $level;

        $log->add();
    }

    private function encodeRequest($raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_array($raw)) {
            return $this->jsonEncode($raw);
        }

        $rawString = (string) $raw;
        $parsed = parse_url($rawString);

        if (!$parsed || empty($parsed['scheme']) || empty($parsed['host'])) {
            return $this->jsonEncode(['raw' => $rawString]);
        }

        $endpoint = $parsed['scheme'] . '://' . $parsed['host'];
        if (isset($parsed['path'])) {
            $endpoint .= $parsed['path'];
        }

        $params = [];
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $params);
            if (array_key_exists('password', $params)) {
                $params['password'] = '***';
            }
            if (array_key_exists('username', $params) && $params['username'] !== '') {
                $params['username'] = '***';
            }
        }

        return $this->jsonEncode([
            'endpoint' => $endpoint,
            'params' => $params,
        ]);
    }

    private function encodeResponse($level, $message, array $context): ?string
    {
        $payload = [
            'level' => (string) $level,
        ];

        $decoded = is_string($message) ? json_decode($message, true) : null;
        if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
            $payload['body'] = $decoded;
        } else {
            $payload['message'] = is_scalar($message) || $message === null ? (string) $message : $message;
        }

        $extra = $context;
        unset($extra['request']);
        if (!empty($extra)) {
            $payload['context'] = $extra;
        }

        return $this->jsonEncode($payload);
    }

    private function jsonEncode($value): string
    {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
