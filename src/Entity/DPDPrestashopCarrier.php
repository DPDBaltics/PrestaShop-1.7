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

class DPDPrestashopCarrier extends Carrier
{
    public function insertDeliveryPlaceholder($psZones)
    {
        $shops = Shop::getShops();
        $weightRange = new RangeWeight();
        $weightRange->id_carrier = $this->id;
        $weightRange->delimiter1 = 0;
        $weightRange->delimiter2 = 999999999;
        $weightRange->add();

        foreach ($shops as $shop) {
            foreach ($psZones as $zone) {
                $delivery = new Delivery();
                $delivery->id_shop = $shop['id_shop'];
                $delivery->id_shop_group = $shop['id_shop_group'];
                $delivery->id_carrier = $this->id;
                $delivery->id_range_weight = $weightRange->id;
                $delivery->id_range_price = 0;
                $delivery->price = 0;
                $delivery->id_zone = $zone['id_zone'];
                $delivery->add();
            }
        }
    }
}
