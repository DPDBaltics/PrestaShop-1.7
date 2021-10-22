<?php
declare(strict_types=1);


namespace Invertus\dpdBaltics\Service\Label;


use Invertus\dpdBaltics\Adapter\LinkAdapter;

class LabelUrlFormatter
{
    private $linkAdapter;

    public function __construct(LinkAdapter $linkAdapter)
    {
        $this->linkAdapter = $linkAdapter;
    }

    public function formatJsLabelPrintUrl()
    {
        return $this->linkAdapter->getUrlSmarty([
            'entity' => 'sf',
            'route' => 'dpdbaltics_print_label_order_view',
            'sf-params' => [
                'shipmentId' => 'shipmentId_',
                'labelFormat' => 'labelFormat_',
                'labelPosition' => 'labelPosition_'
            ]
        ]);
    }

    public function formatJsLabelSaveAndPrintUrl()
    {
        return $this->linkAdapter->getUrlSmarty([
            'entity' => 'sf',
            'route' => 'dpdbaltics_save_and_download_printed_label_order_view',
            'sf-params' => [
                'orderId' => 'orderId_',
            ]
        ]);
    }
}
