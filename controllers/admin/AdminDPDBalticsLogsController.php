<?php


use Invertus\dpdBaltics\Controller\AbstractAdminController;

require_once dirname(__DIR__).'/../vendor/autoload.php';

class AdminDPDBalticsLogsController extends AbstractAdminController
{
    public function __construct()
    {
        $this->className = 'DPDProduct';
        $this->table = DPDLog::$definition['table'];
        $this->identifier = DPDLog::$definition['primary'];
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
            'id_dpd_log' => [
                'title' => $this->l('ID'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'request' => [
                'title' => $this->l('request'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'response' => [
                'title' => $this->l('response'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'status' => [
                'title' => $this->l('status'),
                'type' => 'text',
                'havingFilter' => true
            ],
            'date_add' => [
                'title' => $this->l('Created date'),
                'type' => 'datetime',
                'havingFilter' => true
            ]
        ];
    }
}
