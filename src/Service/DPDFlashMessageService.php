<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Service;

use Context;
use Exception;

class DPDFlashMessageService
{
    private $context;

    /**
     * @var array
     */
    private $flashMessageTypes = ['success', 'info', 'warning', 'error'];

    /**
     * DPDFlashMessageService constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Add flash messages when redirecting form one controller to another
     *
     * @param string $type Can be success, error, warning or info
     * @param string|array $message
     *
     * @throws Exception
     */
    public function addFlash($type, $message)
    {
        if (!in_array($type, $this->flashMessageTypes)) {
            throw new Exception(sprintf(
                'Invalid flash message type "%s" supplied. Available types are: %s',
                $type,
                implode(',', $this->flashMessageTypes)
            ));
        }

        $messages = [];
        if (isset($this->context->cookie->{$type})) {
            $messages = $this->context->cookie->{$type};
            $messages = json_decode($messages, true);
        }

        if (is_array($message)) {
            foreach ($message as $msg) {
                $messages[] = $msg;
            }
        } else {
            $messages[] = $message;
        }

        $this->context->cookie->{$type} = json_encode($messages);
    }
}
