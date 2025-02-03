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


namespace Invertus\dpdBaltics\Service\Email\Handler;

use Invertus\dpdBaltics\Exception\ParcelEmailException;
use Invertus\dpdBaltics\Factory\ContextFactory;
use Invertus\dpdBaltics\Factory\ParcelTrackingUrlFactory;
use Invertus\dpdBaltics\Logger\Logger;
use Mail;
use Module;
use Validate;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ParcelTrackingEmailHandler
{
    /**
     * @var ParcelTrackingUrlFactory
     */
    private $trackingUrlFactory;


    /**
     * @var Module
     */
    private $module;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        ParcelTrackingUrlFactory $trackingUrlFactory,
        Module $module,
        Logger $logger
    ) {
        $this->trackingUrlFactory = $trackingUrlFactory;
        $this->module = $module;
        $this->logger = $logger;
    }

    /**
     * @throws \SmartyException
     * @throws ParcelEmailException
     */
    public function handle($idOrder, $parcelNumbers)
    {
        if (!$idOrder) {
            $this->logger->log(Logger::ERROR, 'Could not retrieve order');
            return false;
        }
        $order = $this->getOrder($idOrder);

        if (empty($parcelNumbers)) {
            $this->logger->log(Logger::ERROR, 'Could not retrieve parcel number, failed to format email ID Order: '. $idOrder);
            return false;
        }

        $cart = $this->getCartByOrderId($idOrder);

        if (!Validate::isLoadedObject($cart)) {
            $this->logger->log(Logger::ERROR, 'Could not retrieve order cart information ID Order: '. $idOrder);
        }

        ContextFactory::getSmarty()->assign([
            'tracking_urls' =>  $this->trackingUrlFactory->createTrackingUrls($cart, $parcelNumbers)
        ]);

       $parcelUrlTemplate = ContextFactory::getSmarty()
           ->fetch(
           $this->module->getLocalPath() . 'views/templates/admin/email/parcel-tracking-links.tpl'
       );
        $customer = $order->getCustomer();

        $template_vars = [
           '{firstname}' => $customer->firstname,
           '{lastname}' => $customer->lastname,
           '{order_reference}' => $order->reference,
           '{tracking_links}' => $parcelUrlTemplate
       ];

        return Mail::send(
            1,
            'parcel-tracking',
            Mail::l('DPD parcel tracking link'),
            $template_vars,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            null,
            null,
            null,
            null,
            $this->module->getPathUri() . 'mails/'
        );
    }

    private function getCartByOrderId($idOrder)
    {
        return \Cart::getCartByOrderId((int) $idOrder);
    }

    private function getOrder($idOrder)
    {
        return new \Order((int) $idOrder);
    }
}
