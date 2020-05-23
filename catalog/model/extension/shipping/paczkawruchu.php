<?php
/**
 *  Avatec Paczkawruchu Integration
 *  Copyright (c) 2020 Grzegorz Miskiewicz
 *  All Rights Reserved
 */

class ModelExtensionShippingPaczkawruchu extends Model {

    public function getQuote( $address )
    {
        $this->load->language('extension/shipping/paczkawruchu');
	    
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping_paczkawruchu WHERE City = '" . ucfirst(strtolower($address['city'])) . "'");

        if (!$this->config->get('shipping_paczkawruchu_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

        /**
        if ($this->cart->getSubTotal() < $this->config->get('shipping_paczkawruchu_total')) {
			$status = false;
		}
        **/

        $method_data = array();

        if ($status) {
		$quote_data = array();

            foreach( $query->rows as $item ) {
                if( !empty( $item['DestinationCode'])) {
                    $paczkawruchu_id = 'paczkawruchu_' . $item['DestinationCode'];
                    $quote_data[$paczkawruchu_id] = array(
        				'code'         => 'paczkawruchu.' . $paczkawruchu_id,
        				'title'        => $item['StreetName'] . ', ' . $item['City'],
        				'cost'         => $this->config->get('shipping_paczkawruchu_total'),
        				'tax_class_id' => $this->config->get('shipping_paczkawruchu_tax_class_id'),
        				'text'         => $this->currency->format($this->tax->calculate($this->config->get('shipping_paczkawruchu_total'), $this->config->get('shipping_paczkawruchu_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
        			);
                }
            }

			$method_data = array(
				'code'       => 'paczkawruchu',
				'title'      => $this->language->get('text_title') . ' (' . $address['city'] . ')', // . ' <button id="showPaczkawruchuMap" type="button" data-toggle="modal" data-target="#paczkawruchuModalMap">zobacz mapę</button>' . $modal,
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_free_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
    }
}
