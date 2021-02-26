<?php


use Invertus\dpdBaltics\Controller\AbstractAdminController;

require_once dirname(__DIR__).'/../vendor/autoload.php';

class AdminDPDBalticsOrderReturnController extends AbstractAdminController
{
    public function __construct()
    {
        $this->className = 'DPDShipment';
        $this->table = DPDShipment::$definition['table'];
        $this->identifier = DPDShipment::$definition['primary'];
        $this->allow_export = true;

        parent::__construct();
        $this->initList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }


    private function initList()
    {
        $this->list_no_link = true;

        $this->fields_list = [
            'tracking_id' => [
                'title' => $this->l('Tracking id'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'date_print' => [
                'title' => $this->l('Printing date'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'date_add' => [
                'title' => $this->l('Create date'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'receiver_name' => [
                'title' => $this->l('Receiver'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'address1' => [
                'title' => $this->l('Address'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'city' => [
                'title' => $this->l('City'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'reference1' => [
                'title' => $this->l('Cust Ref 1'),
                'type' => 'string',
                'havingFilter' => true
            ],
            'num_of_parcels' => [
                'title' => $this->l('Parcel count'),
                'type' => 'int',
                'havingFilter' => true
            ],
            'printed_label' => [
                'title' => $this->l('Printed label'),
                'type' => 'bool',
                'havingFilter' => true
            ],
            'printed_manifest' => [
                'title' => $this->l('Printed manifest'),
                'type' => 'bool',
                'havingFilter' => true
            ],

        ];
    }

    public function renderList()
    {
        $this->_select = 'CONCAT(c.firstname, " ", c.lastname) AS receiver_name, ';
        $this->_select .= 'addr.address1, addr.city, addr.postcode';

        $this->_join = '
            LEFT JOIN ' . _DB_PREFIX_ . 'orders o
                ON o.id_order = a.id_order
        ';

        $this->_join .= '
            LEFT JOIN ' . _DB_PREFIX_ . 'customer c
                ON c.id_customer = o.id_customer
        ';

        $this->_join .= '
            LEFT JOIN ' . _DB_PREFIX_ . 'address addr
                ON addr.id_address = o.id_address_delivery
        ';

        $this->_where .= 'AND a.return_pl_number <> ""';

        $this->addRowAction('viewOrder');

        return parent::renderList();
    }

    public function displayViewOrderLink($token, $idDpdShipment)
    {
        unset($token);
        $shipment = new DPDShipment($idDpdShipment);
        $orderUrl = $this->context->link->getAdminLink(
            'AdminOrders',
            true,
            [
                'vieworder' => '1',
                'id_order' => (int)$shipment->id_order
            ]);

        $params = [
            'href' => $orderUrl,
            'action' => $this->l('View'),
            'icon' => 'icon-search-plus',
        ];

        return $this->renderListAction($params);
    }

    protected function renderListAction(array $params)
    {
        $this->context->smarty->assign($params);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/list-action.tpl');
    }
}
