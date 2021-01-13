<?php

namespace Invertus\dpdBaltics\Service\Label;

use AdminDPDBalticsShipmentSettingsController;
use Configuration;
use Controller;
use DPDBaltics;
use DPDShipment;
use Invertus\dpdBaltics\Config\Config;
use Smarty;

class LabelPositionService
{
    const FILE_NAME = 'LabelPositionService';
    
    /** Label sizes */
    const A4_SIZE = 'A4';
    const A5_SIZE = 'A5';
    const A6_SIZE = 'A6';

    /** Label position */
    const LB_POSITION_1 = 'LeftTop';
    const LB_POSITION_2 = 'LeftDown';
    const LB_POSITION_3 = 'RightTop';
    const LB_POSITION_4 = 'RightDown';

    /** Label name */
    const LB_POSITION_NAME_1 = 'Left-Top';
    const LB_POSITION_NAME_2 = 'Left-Down';
    const LB_POSITION_NAME_3 = 'Right-Top';
    const LB_POSITION_NAME_4 = 'Right-Down';

    /** Label formats */
    const PDF_FORMAT_A4 = 'A4_PDF';
    const PDF_FORMAT_A5 = 'A5_PDF';
    const PDF_FORMAT_A6 = 'A6_PDF';
    const ZPL_FORMAT = 'ZPL';
    const EPL_FORMAT = 'EPL';

    /** Label formats */
    const PDF_FORMAT_NAME_A4 = 'A4 PDF';
    const PDF_FORMAT_NAME_A5 = 'A5 PDF';
    const PDF_FORMAT_NAME_A6 = 'A6 PDF';
    const ZPL_FORMAT_NAME = 'ZPL';
    const EPL_FORMAT_NAME = 'EPL';

    /** File extensions */
    const PDF_EXTENSION = 'pdf';
    const ZPL_EXTENSION = 'zpl';
    const EPL_EXTENSION = 'epl';

    private $module;
    /**
     * @var Smarty
     */
    private $smarty;

    public function __construct(DPDBaltics $module, Smarty $smarty)
    {
        $this->module = $module;
        $this->smarty = $smarty;
    }

    public function assignLabelPositions($shipmentId = false)
    {
        $options = [
            [
                'value' => self::LB_POSITION_1,
                'name' => $this->module->l('Left-Top', self::FILE_NAME),
                'image' => $this->module->getPathUri() . 'views/img/position/position-select-1.svg'
            ],
            [
                'value' => self::LB_POSITION_2,
                'name' => $this->module->l('Left-Down', self::FILE_NAME),
                'image' => $this->module->getPathUri() . 'views/img/position/position-select-2.svg'
            ],
            [
                'value' => self::LB_POSITION_3,
                'name' => $this->module->l('Right-Top', self::FILE_NAME),
                'image' => $this->module->getPathUri() . 'views/img/position/position-select-3.svg'
            ],
            [
                'value' => self::LB_POSITION_4,
                'name' => $this->module->l('Right-Down', self::FILE_NAME),
                'image' => $this->module->getPathUri() . 'views/img/position/position-select-4.svg'
            ],
        ];
        $optionsValueArray = [
            self::LB_POSITION_1,
            self::LB_POSITION_2,
            self::LB_POSITION_3,
            self::LB_POSITION_4
        ];
        $selectedOptionId = Configuration::get(Config::DEFAULT_LABEL_POSITION);
        if ($shipmentId) {
            $shipment = new DPDShipment($shipmentId);
            $selectedOptionId = $shipment->label_position;
        }

        if (!in_array($selectedOptionId, $optionsValueArray)) {
            $selectedOptionId = self::LB_POSITION_1;
        }

        $this->smarty->assign(
            [
                'dpdSelectOptions' => $options,
                'selectedOptionId' => $selectedOptionId,
                'default_label_position' => Config::DEFAULT_LABEL_POSITION,
                'labelPositionText' => $this->module->l("It's only relevant if you are using A4 format", self::FILE_NAME),
            ]
        );
    }

    public function assignLabelFormat($shipmentId)
    {
        $shipment = new DPDShipment($shipmentId);
        $options = $this->getLabelFormatList();

        $optionsValueArray = [
            self::PDF_FORMAT_A4,
            self::PDF_FORMAT_A5,
            self::PDF_FORMAT_A6,
            self::EPL_FORMAT,
            self::ZPL_FORMAT
        ];
        $selectedOptionId = $shipment->label_format;
        if (!in_array($selectedOptionId, $optionsValueArray)) {
            $selectedOptionId = self::LB_POSITION_1;
        }

        $this->smarty->assign(
            [
                'dpdSelectFormatOptions' => $options,
                'selectedFormatOptionId' => $selectedOptionId,
                'default_label_format' => Config::DEFAULT_LABEL_FORMAT
            ]
        );
    }

    public function getLabelPositionList()
    {
        return [
            [
                'name' => $this->module->l('Left-Top', self::FILE_NAME),
                'value' => self::LB_POSITION_1
            ],
            [
                'name' => $this->module->l('Left-Down', self::FILE_NAME),
                'value' => self::LB_POSITION_2
            ],
            [
                'name' => $this->module->l('Right-Top', self::FILE_NAME),
                'value' => self::LB_POSITION_3
            ],
            [
                'name' => $this->module->l('Right-Down', self::FILE_NAME),
                'value' => self::LB_POSITION_4
            ],
        ];
    }

    public function getLabelFormatList()
    {
        return [
            [
                'name' => $this->module->l('A4 PDF', self::FILE_NAME),
                'value' => self::PDF_FORMAT_A4
            ],
            [
                'name' => $this->module->l('A5 PDF', self::FILE_NAME),
                'value' => self::PDF_FORMAT_A5
            ],
            [
                'name' => $this->module->l('A6 PDF', self::FILE_NAME),
                'value' => self::PDF_FORMAT_A6
            ],
            [
                'name' => $this->module->l('ZPL', self::FILE_NAME),
                'value' => self::ZPL_FORMAT
            ],
            [
                'name' => $this->module->l('EPL', self::FILE_NAME),
                'value' => self::EPL_FORMAT
            ],
        ];
    }
}
