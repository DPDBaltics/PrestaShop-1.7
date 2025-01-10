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


namespace Invertus\dpdBaltics\Validate\Carrier;

use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\PudoRepository;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Exception;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PudoValidate
{

    /**
     * @var DPDBaltics
     */
    private $module;
    /**
     * @var PudoRepository
     */
    private $pudoRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(DPDBaltics $module, PudoRepository $pudoRepository, ProductRepository $productRepository)
    {
        $this->module = $module;
        $this->pudoRepository = $pudoRepository;
        $this->productRepository = $productRepository;
    }

    public function validatePickupPoints($cartId, $carrierReference)
    {
        if (!$this->productRepository->isProductPudo($carrierReference)) {
            return true;
        }
        $pudoId = $this->pudoRepository->getIdByCart($cartId);
        if (!$pudoId) {
            return false;
        }

        return true;
    }

    public function isPudoSelected($cartId, $carrierReference)
    {
        if (!$this->validatePickupPoints($cartId, $carrierReference)) {
            throw new Exception(
                'Pudo point is missing, please select valid terminal point',
                Config::ERROR_INVALID_PUDO_TERMINAL
            );
        }
    }
}
