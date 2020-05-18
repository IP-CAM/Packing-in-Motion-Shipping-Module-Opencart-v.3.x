<?php
/**
 *  Avatec Paczkawruchu Integration
 *  Copyright (c) 2020 Grzegorz Miskiewicz
 *  All Rights Reserved
 */


class ControllerExtensionShippingPaczkawruchu extends Controller {

    private $error = [];

    public function install()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "shipping_paczkawruchu (
    		`DestinationCode` varchar(15) NOT NULL,
    		`StreetName` varchar(30) NOT NULL,
    		`BuildingNumber` varchar(5) NOT NULL,
    		`City` varchar(20) NOT NULL,
    		`District` varchar(20) NOT NULL,
    		`Latitude` varchar(10) NOT NULL,
    		`Longitude` varchar(10) NOT NULL,
    		`Province` varchar(20) NOT NULL,
    		`CachOnDelivery` tinyint(1) NOT NULL,
    		`OpeningHours` varchar(50) NOT NULL,
    		`Location` varchar(255) NOT NULL,
    		`PSD` varchar(6) NOT NULL,
    		`Available` char(1) NOT NULL,
    		`PointType` varchar(3) NOT NULL,
    		KEY `DestinationCode` (`DestinationCode`)
    		) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;
        ");
    }

    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "shipping_paczkawruchu`");
    }

    public function index()
    {
        $this->load->language('extension/shipping/paczkawruchu');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_paczkawruchu', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        $data['user_token'] = $this->session->data['user_token'];

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
    			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
            ],
            [
                'text' => $this->language->get('text_extension'),
    			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
            ],
            [
                'text' => $this->language->get('heading_title'),
    			'href' => $this->url->link('extension/shipping/paczkawruchu', 'user_token=' . $this->session->data['user_token'], true)
            ]
        ];

        $data['action'] = $this->url->link('extension/shipping/paczkawruchu', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

        if (isset($this->request->post['shipping_paczkawruchu_api_id'])) {
			$data['shipping_paczkawruchu_api_id'] = $this->request->post['shipping_paczkawruchu_api_id'];
		} else {
			$data['shipping_paczkawruchu_api_id'] = $this->config->get('shipping_paczkawruchu_api_id');
		}

        if (isset($this->request->post['shipping_paczkawruchu_api_key'])) {
			$data['shipping_paczkawruchu_api_key'] = $this->request->post['shipping_paczkawruchu_api_key'];
		} else {
			$data['shipping_paczkawruchu_api_key'] = $this->config->get('shipping_paczkawruchu_api_key');
		}

        if (isset($this->request->post['shipping_paczkawruchu_total'])) {
			$data['shipping_paczkawruchu_total'] = $this->request->post['shipping_paczkawruchu_total'];
		} else {
			$data['shipping_paczkawruchu_total'] = $this->config->get('shipping_paczkawruchu_total');
		}

        $this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['shipping_paczkawruchu_geo_zone_id'])) {
			$data['shipping_paczkawruchu_geo_zone_id'] = $this->request->post['shipping_paczkawruchu_geo_zone_id'];
		} else {
			$data['shipping_paczkawruchu_geo_zone_id'] = $this->config->get('shipping_paczkawruchu_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_paczkawruchu_status'])) {
			$data['shipping_paczkawruchu_status'] = $this->request->post['shipping_paczkawruchu_status'];
		} else {
			$data['shipping_paczkawruchu_status'] = $this->config->get('shipping_paczkawruchu_status');
		}

		if (isset($this->request->post['shipping_paczkawruchu_sort_order'])) {
			$data['shipping_paczkawruchu_sort_order'] = $this->request->post['shipping_paczkawruchu_sort_order'];
		} else {
			$data['shipping_paczkawruchu_sort_order'] = $this->config->get('shipping_paczkawruchu_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $paczkawruchu_list = $this->db->query("SELECT * FROM `" . DB_PREFIX . "shipping_paczkawruchu` ORDER BY City");
        $history_total = $paczkawruchu_list->num_rows;
        $data['paczkawruchu_list'] = $paczkawruchu_list->rows;

        if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

        $url = '';
        if (isset($this->request->get['page'])) {
            $url = '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = (!empty( $page ) ? $page : 1);
        $pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/shipping/paczkawruchu', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true);

        if( empty( $page ) || $page == 1 ) {
            $from_row = 0;
            $to_row = $from_row + $pagination->limit - 1;
        } else {
            $from_row = ($page - 1) * $pagination->limit;
            $to_row = $from_row + $pagination->limit;
        }

        for($i=0; $i<$history_total; $i++ ) {

            if( $page == 1 && $i>$to_row ) {
                unset( $data['paczkawruchu_list'][$i] );
            }

            if( $page > 1 ) {
                if( $i < $from_row ) {
                    unset( $data['paczkawruchu_list'][$i] );
                }

                if( $i > $to_row ) {
                    unset( $data['paczkawruchu_list'][$i] );
                }
            }
        }

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('extension/shipping/paczkawruchu', $data));
    }

    public function refresh()
    {
        ini_set('display_errors' , 1);
        error_reporting(E_ALL);
        header('Content-type: application/json');

        $this->load->language('extension/shipping/paczkawruchu');

        $sql_columns = array('DestinationCode','StreetName','BuildingNumber','City','District','Latitude','Longitude','Province','CashOnDelivery','OpeningHours','Location','PSD','Available','PointType');

        $this->db->query("DELETE FROM `" . DB_PREFIX . "shipping_paczkawruchu`");

        $partner_id = $this->config->get('shipping_paczkawruchu_api_id');
		$partner_key = $this->config->get('shipping_paczkawruchu_api_key');

        //if( $this->config->get('shipping_paczkawruchu_testing') == 0 ) {
			$WSDL = 'https://api-test.paczkawruchu.pl/WebServicePwR/WebServicePwRTest.asmx?WSDL';
		//} else {
			//$WSDL = 'https://api.paczkawruchu.pl/WebServicePwRProd/WebServicePwR.asmx?wsdl';
		//}

        $client = new SoapClient($WSDL, ['partnerId' => $partner_id, 'partnerKey' => $partner_key ]);
        $locations = $client->__soapCall("GiveMeAllRUCHLocation", ['auth' => [
			'PartnerID' => $partner_id,
			'PartnerKey' => $partner_key
		]]);

        $locations = $locations->GiveMeAllRUCHLocationResult;
        $schema = $locations->schema;
		$any = $locations->any;

        $xml_any = str_replace(array("diffgr:","msdata:"),'', $any);
		$xml_any    = "<package>".$xml_any."</package>";
		$xml = simplexml_load_string( $xml_any );
        if( !empty( $xml->diffgram->NewDataSet->AllRUCHLocation )) {
            foreach( $xml->diffgram->NewDataSet->AllRUCHLocation as $i ) {
                foreach( $sql_columns as $col ) {
                    $query[] = "'" . $this->db->escape( $i->{$col}[0] ) . "'";
                }

                try {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "shipping_paczkawruchu` VALUES(" . implode("," , $query) . ")");
                } catch( Error $e ) {
                    die(json_encode(['error' => true, 'msg' => '<i class="fa fa-times fa-fw"></i> Wystąpił błąd w bazie danych: ' . $this->db->error]));
                }

                if( !empty( $this->db->errno )) {
                    die(json_encode(['error' => true, 'msg' => '<i class="fa fa-times fa-fw"></i> Wystąpił błąd w bazie danych: ' . $this->db->error]));
                }

                unset($query);
            }
        }

        die(json_encode(['success' => true, 'msg' => '<i class="fa fa-check fa-fw"></i> Import zakończony pomyślnie. Za chwilę zostanie załadowana lista punktów.']));
    }

    protected function validate()
    {
		if (!$this->user->hasPermission('modify', 'extension/shipping/paczkawruchu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
