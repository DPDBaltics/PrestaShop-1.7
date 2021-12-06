<?php

namespace Invertus\dpdBaltics\Service\Email\Handler;

use Invertus\dpdBaltics\Exception\ParcelEmailException;
use Invertus\dpdBaltics\Factory\ContextFactory;
use Invertus\dpdBaltics\Factory\ParcelTrackingUrlFactory;
use Mail;
use Module;
use Validate;

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

    public function __construct(
        ParcelTrackingUrlFactory $trackingUrlFactory,
        Module $module
    ) {
        $this->trackingUrlFactory = $trackingUrlFactory;
        $this->module = $module;
    }

    /**
     * @throws \SmartyException
     * @throws ParcelEmailException
     */
    public function handle($idOrder, $parcelNumbers)
    {
        if (!$idOrder) {
            throw new ParcelEmailException('Could not retrieve order');
        }
        $order = $this->getOrder($idOrder);

        if (empty($parcelNumbers)) {
            throw new ParcelEmailException('Could not retrieve parcel number, failed to format email');
        }
        $cart = $this->getCartByOrderId($idOrder);

        if (!Validate::isLoadedObject($cart)) {
            throw new ParcelEmailException('Could not retrieve order cart information');
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
