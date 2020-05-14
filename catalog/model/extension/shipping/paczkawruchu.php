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

        if (!$this->config->get('shipping_free_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

        if ($this->cart->getSubTotal() < $this->config->get('shipping_free_total')) {
			$status = false;
		}

        $method_data = array();

        if ($status) {
			$quote_data = array();

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shipping_paczkawruchu WHERE City = '" . ucfirst(strtolower($address['city'])) . "'");
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

            /**
            $modal = '<div id="paczkawruchuModalMap" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Zamknij"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Wybierz paczkomat odbioru</h4>
                        </div>
                        <div class="modal-body">
                            <div class="paczkawruchu-google-map" data-lat="" data-lng="" data-zoom=""></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">' . $this->language->get('button_close') . '</button>
                            <button type="button" class="btn btn-primary">' . $this->language->get('button_select') . '</button>
                        </div>
                    </div>
                </div>
            </div>';
            **/

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
