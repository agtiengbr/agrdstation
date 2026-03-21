<?php

class AdminRdstationRequestController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap    = true;
        $this->table        = 'agrdstation_api_request';
        $this->identifier   = 'id_agrdstation_api_request';
        $this->className    = 'AgRdstationApiRequest';
        $this->list_no_link = true;
        $this->_defaultOrderBy = 'id_agrdstation_api_request';
        $this->_defaultOrderWay = 'DESC';
        parent::__construct();

        $this->fields_list = [
            'id_agrdstation_api_request' => [
                'title' => 'ID',
                'align' => 'center',
                'type' => 'int',
                'class' => 'fixed-width-xs',
            ],
            'http_code' => [
                'title' => 'Código HTTP',
                'type' => 'int',
                'class' => 'fixed-width-md'
            ],
            'method' => [
                'title' => 'Método',
                'type' => 'text',
                'class' => 'fixed-width-md'
            ],
            'endpoint' => [
                'title' => 'URL',
                'type' => 'text'
            ],
            'time_spent' => [
                'title' => 'Tempo Gasto',
                'type' => 'text',
                'suffix' => 's'
            ],
            'date_add' => [
                'title' => 'Data',
                'type' => 'datetime'
            ]
        ];
        $this->actions = ['view'];

    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::getIsSet('view' . $this->table)) {

            $request = $this->loadObject();
            $request->response = json_decode($request->response);

           
            $html  = $this->content;

            //contéudo geral da ação VER
            $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/api_requests/view.tpl');
            $tpl->assign(['obj' => $request]);
            $html .= $tpl->fetch();

            $this->content = $html;
            $this->context->smarty->assign(['content' => $html]);

            return;
        }
    }
}