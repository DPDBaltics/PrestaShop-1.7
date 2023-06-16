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

namespace Invertus\dpdBaltics\Infrastructure\Exception;

use Invertus\dpdBaltics\Infrastructure\Utility\ExceptionUtility;

class DpdException extends \Exception
{
    private $context;

    final public function __construct(
        $internalMessage,
        $code,
        $previous = null,
        array $context = []
    ) {
        parent::__construct($internalMessage, $code, $previous);
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getExceptions()
    {
        $exceptions = [];
        $exception = $this;
        while ($exception) {
            $exceptions[] = ExceptionUtility::toArray($exception);

            $exception = $exception->getPrevious();
        }

        return $exceptions;
    }

    public static function unknownError(\Exception $exception)
    {
        return new static(
            'An unknown error error occurred. Please check system logs or contact Bolt support.',
            ExceptionCode::UNKNOWN_ERROR,
            $exception
        );
    }
}
