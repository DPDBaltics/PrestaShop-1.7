<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Validate\Zone;

use DPDBaltics;
use DPDZone;
use Exception;
use Invertus\dpdBaltics\ORM\EntityManager;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;

class ZoneDeleteValidate
{
    /**
    * @var DPDBaltics
    */
    private $module;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(DPDBaltics $module, EntityManager $em)
    {
        $this->module = $module;
        $this->em = $em;
    }

    /**
     * @ping todo this is a duplicate function, how could it have been reused?
     * @param array $array
     * @param $tagName
     * @param string $separator
     * @return string
     * @throws Exception
     */
    protected function implodeByTagName(array $array, $tagName, $separator = ', ')
    {
        if (empty($array)) {
            throw new Exception('array is empty');
        }
        $elements = [];
        foreach ($array as $element) {
            if (!isset($element[$tagName])) {
                throw new Exception('invalid tagName parameter');
            }
            $elements[] = $element[$tagName];
        }
        return implode($separator, $elements);
    }

    /**
     * @param $idZone
     * @return array
     * @throws Exception
     */
    public function zoneValidateDeletionReturnError($idZone)
    {
        $zone = new DPDZone($idZone);

        $errors = [];
        /** @var ProductRepository $productRepo */
        $productRepo = $this->module->getModuleContainer(ProductRepository::class);
        $carriers = $productRepo->getProductsByIdZone($idZone);

        if (!empty($carriers)) {
            $carrierNames = $this->implodeByTagName($carriers, 'name');
            $errors[] =
                sprintf(
                    $this->module->l('Unable to delete zone. Zone \'%s\' is used by \'%s\' carriers'),
                    $zone->name,
                    $carrierNames
                );

            return $errors;
        }

        /** @var PriceRuleRepository $priceRulesRepository */
        $priceRulesRepository = $this->module->getModuleContainer(PriceRuleRepository::class);
        $priceRules = $priceRulesRepository->getPriceRulesByIdZone($idZone);

        if (!empty($priceRules)) {
            $priceRuleNames = $this->implodeByTagName($priceRules, 'name');
            $errors[] =
                sprintf(
                    $this->module->l('Unable to delete zone. Zone \'%s\' is used by \'%s\' price rules'),
                    $zone->name,
                    $priceRuleNames
                );

            return $errors;
        }

        return [];
    }
}
