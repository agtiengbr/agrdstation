<?php
require_once 'vendor/autoload.php';

require_once _PS_MODULE_DIR_ . 'agcliente/lib/AgModule.php';
require_once _PS_MODULE_DIR_ . 'agcliente/lib/AgObjectModel.php';

require_once _PS_MODULE_DIR_ . 'agrdstation/classes/AgRdStationApiRequest.php';
require_once _PS_MODULE_DIR_ . 'agrdstation/classes/AgRdStationContacts.php';
require_once _PS_MODULE_DIR_ . 'agrdstation/classes/AgRdStationToken.php';

use AGTI\RdStation\Infrastructure\Installer\Installer;

class BaseAgRdStation extends AgModule{

    protected $hooks = [
        'actionCustomerAccountAdd',
        'actionCustomerAccountUpdate',
        'actionCartUpdateQuantityBefore',
        'actionOrderStatusPostUpdate'
    ];

    public function __construct()
    {
        $this->name                   = 'agrdstation';
        $this->version                = '1.0.7';
        $this->bootstrap              = true;
        $this->author                 = 'AGTI';
        $this->need_instance          = 1;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '9.99');

        parent::__construct();

        $this->displayName = "agrdstation";
    }

    public function install(){
        if (!Hook::getIdByName('actionCartUpdateQuantityBefore')) {
            $hook = new Hook;
            $hook->name = 'actionCartUpdateQuantityBefore';
            $hook->save();
        }
        
        if (
            !parent::install()||
            !$this->registerHook('actionCustomerAccountAdd') ||
            !$this->registerHook('actionCustomerAccountUpdate') ||
            !$this->registerHook('actionCartUpdateQuantityBefore') ||
            !$this->registerHook('actionOrderStatusPostUpdate')

        ) {
            return false;
        }
        Installer::install();
        return true;

    }

    public function hookActionCustomerAccountAdd($prop)
    {
        $newCustomer=$prop['newCustomer'];
        $em = $this->get('agti.rdstation.application.create_contact');
        $em->exec($newCustomer);
    }

    public function hookActionCustomerAccountUpdate($updtCustomer)
    {
        $em = $this->get('agti.rdstation.application.update_contact');
        $em->exec($updtCustomer['customer']);
    }

    public function hookActionCartUpdateQuantityBefore($params)
    {
        try {
                if($params['operator']=='up' && $params['cart']->getProducts()==[] ){
                    $em = $this->get('agti.rdstation.application.event_tag_opportunity');
                    $em->exec($this->context->customer);
                }

                if(isset($this->context->customer->id) && $params['operator']=='up' && Tools::getValue('id_product')){
                    $em = $this->get('agti.rdstation.application.update_tags');
                    $em->exec(new Product(Tools::getValue('id_product')),$this->context->customer);
                }
        }catch (Exception $e) {
            Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        try {
            if ($params["newOrderStatus"]->paid == 1) {

                    $bash=[];
                    $openCart = $this->get('agti.rdstation.application.event_close_order');
                    $openCartReturn=$openCart->exec(new Order($params['id_order']),$this->context->language->id);
                    $bash[]=$openCartReturn;

                    $itens = $this->get('agti.rdstation.application.event_cart_add_itens');
                    $bash=$itens->exec(new Order($params['id_order']),$bash);

                    $bashCart = $this->get('agti.rdstation.application.event_bash');
                    $bashCart = $bashCart->exec($bash);

                    sleep(5);
                    $opportunityWins = $this->get('agti.rdstation.application.event_opportunity_win');
                    $opportunityWins->exec(new Order($params['id_order']),(new Order($params['id_order']))->getCustomer());

            }
        }catch (Exception $e) {
            Logger::addLog('RdStation:'.$e->getMessage(), 3, null, null, null, true);
        }
    }
   
    public function getContent()
    {
        $metodsPaymentList=[];
      
        $sql ='select distinct(payment),payment AS payId from '.pSQL(_DB_PREFIX_ . 'orders').';';
        $metodsPaymentList = \Db::getInstance()->executeS($sql, true, false);

        if (Tools::isSubmit('agrdstation-btn-save-states')) {

            foreach ($metodsPaymentList as $metodPayment) {
                    $methodName=self::remove_accents(str_replace(' ','',$metodPayment['payment']));

                Configuration::updateValue(
                    'AGRDSTATION_MAPPING_PAYMENT_'.$methodName,
                    Tools::getValue('AGRDSTATION_MAPPING_PAYMENT_'.$methodName)
                );
                // dump('AGRDSTATION_MAPPING_PAYMENT_'.str_replace(' ','',$metodPayment['payment']));
            }

        }elseif (Tools::isSubmit('agrdstation-btn-save')) {
            Configuration::updateValue('AGRDSTATION_CLIENT_ID', Tools::getValue('AGRDSTATION_CLIENT_ID'));
            Configuration::updateValue('AGRDSTATION_CLIENT_SECRET', Tools::getValue('AGRDSTATION_CLIENT_SECRET'));
            Configuration::updateValue('AGRDSTATION_FUNNEL_NAME', Tools::getValue('AGRDSTATION_FUNNEL_NAME'));
        }
        $url='';

        $token=$this->get('agti.rdstation.infrastructure.utils.get_valid_token');
        if(Configuration::get('AGRDSTATION_CLIENT_ID') != NULL && Configuration::get('AGRDSTATION_CLIENT_ID') != ''){

            if(!$token->exec()){
                $service=$this->get('agti.rdstation.infrastructure.utils.auth_dialog');
                $url = "Clique <a target='_blank' href='".$service->exec(Configuration::get('AGRDSTATION_CLIENT_ID'))."'>AQUI</a> para autorizar o Módulo a acessas sua conta Rd Station";
            }else{
                $service=$this->get('agti.rdstation.infrastructure.utils.auth_dialog');
                $url = "Sua conta já está autorizada,mas se for preciso pode autorizar novamente <a target='_blank' href='".$service->exec(Configuration::get('AGRDSTATION_CLIENT_ID'))."'>AQUI</a>";
            }
        }


        $inputs=[];

        $paymentMetodsRd=[];

        $paymentMetodsRd[]=[
            'name'=>'Cartão de crédito',
            'code'=>'Credit Card'
        ];
        $paymentMetodsRd[]=[
            'name'=>'Cartão de débito',
            'code'=>'Debit Card'
        ];
        $paymentMetodsRd[]=[
            'name'=>'Fatura',
            'code'=>'Invoice'
        ];
        $paymentMetodsRd[]=[
            'name'=>'Outros',
            'code'=>'Others'
        ];

        foreach ($metodsPaymentList as $metodPayment) {
            $methodName=self::remove_accents(str_replace(' ','',$metodPayment['payment']));

            $inputs[]= [
                'type' => 'select',
                'label' => $metodPayment['payment'],
                'name' => 'AGRDSTATION_MAPPING_PAYMENT_'.$methodName,
                'options' => [
                    'id' => 'code',
                    'name' => 'name',
                    'query' => $paymentMetodsRd
                ]
            ];
        }

        $form2 = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Mapeamento de Métodos de Pagamento'),
                ],
                'input' => $inputs,
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'agrdstation-btn-save-states',
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
        $urlCallback = $this->context->link->getModuleLink($this->name, 'callback', array(), true);

        $form = [
            'form' => [
                'description' => $url,
                'warning' => $this->l('Na criação do aplicativo da RdStation, utilize a seguinte URL no campo URL de Callback :'.$urlCallback),
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Client ID'),
                        'name' => 'AGRDSTATION_CLIENT_ID',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Client secret'),
                        'name' => 'AGRDSTATION_CLIENT_SECRET',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Funnel Name'),
                        'name' => 'AGRDSTATION_FUNNEL_NAME',
                        'size' => 20,
                        'required' => true,
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'agrdstation-btn-save',
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');


        // $helper->fields_value['AGRDSTATION_MAPPING_CREDIT_CART'] = Configuration::get('AGRDSTATION_MAPPING_CREDIT_CART');
        // $helper->fields_value['AGRDSTATION_MAPPING_DEBIT_CART'] = Configuration::get('AGRDSTATION_MAPPING_DEBIT_CART');
        // $helper->fields_value['AGRDSTATION_MAPPING_INVOICE'] = Configuration::get('AGRDSTATION_MAPPING_INVOICE');

        foreach ($metodsPaymentList as $metodPayment) {
            $methodName=self::remove_accents(str_replace(' ','',$metodPayment['payment']));
            $helper->fields_value['AGRDSTATION_MAPPING_PAYMENT_'.$methodName] = Configuration::get('AGRDSTATION_MAPPING_PAYMENT_'.$methodName);
        }

        $helper->fields_value['AGRDSTATION_FUNNEL_NAME'] = Configuration::get('AGRDSTATION_FUNNEL_NAME');
        $helper->fields_value['AGRDSTATION_CLIENT_ID'] = Configuration::get('AGRDSTATION_CLIENT_ID');
        $helper->fields_value['AGRDSTATION_CLIENT_SECRET'] = Configuration::get('AGRDSTATION_CLIENT_SECRET');
        $helper->fields_value['AGRDSTATION_CODE'] = Configuration::get('AGRDSTATION_CODE');
        $helper->fields_value['AGRDSTATION_TOKEN'] = Configuration::get('AGRDSTATION_TOKEN');
    
        return $helper->generateForm([$form,$form2]);

    }

    public static function remove_accents($string) {
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;
    
        $chars = array(
        // Decompositions for Latin-1 Supplement
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
        chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
        chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
        chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
        chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
        chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
        chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
        chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
        chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
        chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
        chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
        chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
        chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
        chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
        chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
        chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
        chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
        chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
        chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
        chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
        chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
        chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
        chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
        chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
        chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
        chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
        chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
        chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
        chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
        chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
        chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
        chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
        chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
        chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
        chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
        chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
        chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
        chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
        chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
        chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
        chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
        chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
        chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
        chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
        chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
        chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
        chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
        chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
        chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
        chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );
    
        $string = strtr($string, $chars);
    
        return $string;
    }
}