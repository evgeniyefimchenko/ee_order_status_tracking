<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;

global $settings_addon;
$settings_addon = Registry::get('addons.ee_order_status_tracking');

function fn_ee_order_status_tracking_install() {
	$message = __FILE__ . ' the module was installed on the site ' . Registry::get('config.http_host');
	mail('evgeniy@efimchenko.ru', 'module installed', $message);	
}

function fn_ee_order_status_tracking_uninstall() {
	return true;
}

/**
* Хук при записи статусов от сдэка
* Нужно получить статус отгрузки
*/
function fn_ee_order_status_ee_sdek_history_status_change($order_id, $shipment_id, $json) {
	$cscart_statuses_shipment = fn_get_statuses(STATUSES_SHIPMENT);
	$shipment_status_code = db_get_field('SELECT status FROM ?:shipments WHERE shipment_id = ?i', $shipment_id);
	$date = date('Y-m-d H:i:s');
	$data = ['order_id' => $order_id, 'shipment_id' => $shipment_id, 'date' => $date, 'status_code' => $shipment_status_code, 'status_text' => $cscart_statuses_shipment[$shipment_status_code]['description']];
	fn_ee_order_status_tracking_set_refund_status($data);
}

function fn_ee_order_status_tracking_set_order_status($data) {
	$data['type'] = 'order';
	db_query('INSERT INTO ?:ee_sdek_history_status_track ?e', $data);
}

function fn_ee_order_status_tracking_set_refund_status($data) {
	$data['type'] = 'refund';
	db_query('INSERT INTO ?:ee_sdek_history_status_track ?e', $data);
}

function fn_ee_order_status_tracking_get_change_status_orders($str = '') {
	global $settings_addon;
	$order_statuses_set = json_decode($settings_addon['json_settings'], true)['order'];
	$cscart_statuses_orders = fn_get_statuses(STATUSES_ORDER);
	foreach ($order_statuses_set as $k => $item) {
		$res[] = $cscart_statuses_orders[$item]['description'];
	}
	return $res;
}

function fn_ee_order_status_tracking_get_change_status_refund($str = '') {
	global $settings_addon;
	$refund_statuses_set = json_decode($settings_addon['json_settings'], true)['refund'];
	$cscart_statuses_refund = fn_get_statuses(STATUSES_SHIPMENT);
	foreach ($refund_statuses_set as $k => $item) {
		$res[] = $cscart_statuses_refund[$item]['description'];
	}
	return $res;
}

function ee_in_res($needle, $haystack) {
    foreach ($haystack as $key => $item) {
		if ($item['status_code'] == $needle) {
			return $key;
		}
    }
    return false;
}

function fn_ee_order_status_tracking_get_order_status($order_id) {
	global $settings_addon;
	$data = db_get_array('SELECT * FROM ?:ee_sdek_history_status_track WHERE type LIKE "order" AND order_id = ?i ORDER BY date', $order_id);	
	$order_statuses_set = json_decode($settings_addon['json_settings'], true)['order'];
	$order_statuses_set = $order_statuses_set ? $order_statuses_set : [];
	$res = $ret =[];
	if ($settings_addon['show_all_history'] == 'N') {
		foreach ($data as $item) {
			if (in_array($item['status_code'], $order_statuses_set)) {
				$res[] = $item;
			}
		}
	} else {
		$res = $data;
	}
	if ($settings_addon['fill_missing_statuses'] == 'Y' && is_array($res) && count($res)) {
		$cscart_statuses_orders = fn_get_statuses(STATUSES_ORDER);
		$count = 0;
		$count_res = count($res);
		foreach ($order_statuses_set as $key => $item) {
			$key_res = ee_in_res($item, $res);
			if ($key_res === false) {				
				$ret[] = ['order_id' => $order_id, 'date' => $date, 'status_code' => $item, 'status_text' => $cscart_statuses_orders[$item]['description']];
			} else {
				$count++;
				$date = $res[$key_res]['date'];
				$ret[] = $res[$key_res];
			}
			if ($count == $count_res) {
				break;
			}
		}
	} else {
		$ret = $res;
	}
	return $ret;
}

function ee_order_status_tracking_refund_prod($shipment_id) {
	$return_id = db_get_field('SELECT return_id FROM ?:cscart_rma_returns WHERE shipment_id = ?i', $shipment_id);
	$prod_ids = 0;
	if ($return_id) {
		$prod_ids = db_get_array('SELECT product_id, amount FROM ?:rma_return_products WHERE return_id = ?i', $return_id);
	}
	return $prod_ids;
}

function fn_ee_order_status_tracking_get_refund_status($order_id) {
	$data = db_get_array('SELECT * FROM ?:ee_sdek_history_status_track WHERE type LIKE "refund" AND order_id = ?i ORDER BY date', $order_id);
	$settings_addon = Registry::get('addons.ee_order_status_tracking');
	$refund_statuses_set = json_decode($settings_addon['json_settings'], true)['refund'];
	$refund_statuses_set = $refund_statuses_set ? $refund_statuses_set : [];
	$res = $ret = $ret_prod = [];
	if ($settings_addon['show_all_history'] == 'N') {
		foreach ($data as $item) {
			if (in_array($item['status_code'], $refund_statuses_set)) {
				$res[] = $item;
			}
		}
	} else {
		$res = $data;
	}	
	foreach ($res as $item) {
		$temp_res[$item['shipment_id']][] = $item;
	}
	$res = $temp_res;
	if ($settings_addon['fill_missing_statuses'] == 'Y' && is_array($res) && count($res)) {
		$cscart_statuses_shipment = fn_get_statuses(STATUSES_SHIPMENT);					
		foreach ($res as $key_res_item => $res_item) {
			$count_res = count($res[$key_res_item]);
			$count = 0;
			foreach ($refund_statuses_set as $key => $item) {				
				$key_res = ee_in_res($item, $res_item);
				if ($key_res === false) {			
					$ret[$key_res_item][] = ['order_id' => $order_id, 'shipment_id' => $key_res_item, 'date' => $date, 'status_code' => $item, 'status_text' => $cscart_statuses_shipment[$item]['description']];
				} else {					
					$date = $res[$key_res_item][$key_res]['date'];
					$ret[$key_res_item][] = $res_item[$key_res];
					$count++;
				}
				if ($count >= $count_res) {
					break;
				}			
			}
		}
	} else {
		foreach ($res as $k => $item) {
			$ret[$k] = $item;
		}
	}
	$temp_k = 0;
	$temp_count = 229;
	foreach ($ret as $k => $item) {
		if ($temp_k != $k) {
			$order_id = db_get_field('SELECT order_id FROM ?:shipment_items WHERE shipment_id = ?i', $k);
			$order_data = fn_get_order_info($temp_count);
			$temp_count--;
			if (is_array($order_data)) {
				$count = 0;
				foreach ($order_data['products'] as $prod) {				
					$ret[$k]['order_data'][$count]['name'] = $prod['product'];
					$ret[$k]['order_data'][$count]['amount'] = $prod['amount'];
					$ret[$k]['order_data'][$count]['product_url'] = $prod['product_url'];
					$ret[$k]['order_data'][$count]['product_code'] = $prod['product_code'];
					$ret[$k]['order_data'][$count]['product_id'] = $prod['product_id'];
					$ret[$k]['order_data'][$count]['images'] = fn_get_image_pairs($prod['product_id'], 'product', 'M', true, true)['detailed'];
					$count++;
				}
			}
			$temp_k = $k;
		}
	}
	return $ret;	
}

/**
 * Executes after order status is changed, allows you to perform additional operations.
 *
 * @param int    $order_id           Order identifier
 * @param string $status_to          New order status (one char)
 * @param string $status_from        Old order status (one char)
 * @param array  $force_notification Array with notification rules
 * @param bool   $place_order        True, if this function have been called inside of fn_place_order function
 * @param array  $order_info         Order information
 * @param array  $edp_data           Downloadable products data
 * Смена статуса заказа, запишем историю
 */
function fn_ee_order_status_tracking_change_order_status_post($order_id, $status_to, $status_from, $force_notification, $place_order, $order_info, $edp_data) {
	if ($status_to != $status_from) {
		$cscart_statuses_orders = fn_get_statuses(STATUSES_ORDER, [], true);
		$data = ['order_id' => $order_id, 'date' => date('d.m.Y H:i:s'), 'status_code' => $status_to, 'status_text' => $cscart_statuses_orders[$status_to]['description']];
		fn_ee_order_status_tracking_set_order_status($data);
	}
}

function fn_ee_order_status_tracking_information() {
	global $settings_addon;	
	$cscart_statuses_orders = fn_get_statuses(STATUSES_ORDER);
	$cscart_statuses_shipments = fn_get_statuses(STATUSES_SHIPMENT);
	$order_statuses_set = json_decode($settings_addon['json_settings'], true)['order'];
	$refund_statuses_set = json_decode($settings_addon['json_settings'], true)['refund'];
	$ee_select_box_refund = $ee_select_box_orders = '';
	$all_options_orders = $all_options_refund = '<option value="777" class="opt_red">Нет</option>';
	foreach ($order_statuses_set as $v) {	
		$ee_select_box_orders .= '<select name="ee_status_select_order[]"><option value="777" class="opt_red">Нет</option>';
		foreach ($cscart_statuses_orders as $key => $value) {
			$selected = $key == $v ? 'selected ' : '';
			$ee_select_box_orders .= '<option ' . $selected . 'value="' . $key . '">' . $value['description'] . '</option>';
		}
		$ee_select_box_orders .= '</select>';
	}
	foreach ($refund_statuses_set as $v) {	
		$ee_select_box_refund .= '<select name="ee_status_select_refund[]"><option value="777" class="opt_red">Нет</option>';
		foreach ($cscart_statuses_shipments as $key => $value) {
			$selected = $key == $v ? 'selected ' : '';
			$ee_select_box_refund .= '<option ' . $selected . 'value="' . $key . '">' . $value['description'] . '</option>';
		}
		$ee_select_box_refund .= '</select>';
	}
	foreach ($cscart_statuses_orders as $key => $value) {
		$all_options_orders .= '<option value="' . $key . '">' . $value['description'] . '</option>';
	}
	foreach ($cscart_statuses_shipments as $key => $value) {
		$all_options_refund .= '<option value="' . $key . '">' . $value['description'] . '</option>';
	}	
	$ee_select_box_orders .= '<select name="ee_status_select_order[]">' . $all_options_orders . '</select>';
	$ee_select_box_refund .= '<select name="ee_status_select_refund[]">' . $all_options_refund . '</select>';	
	
	$res = '		
        <fieldset>
            <div id="container_ee_order_status_tracking_order" class="control-group setting-wide">
                <label for="addon_ee_order_status_tracking" class="control-label">Список статусов доставки в порядке очерёдности:</label>
                <div class="controls">
                    ' . $ee_select_box_orders . '
                </div>
			</div>
			<hr/>
			<div id="container_ee_order_status_tracking_refund" class="control-group setting-wide">
				<label for="addon_ee_order_status_tracking_refund" class="control-label">Список статусов возврата в порядке очерёдности:</label>
                <div class="controls">
                    ' . $ee_select_box_refund . '
                </div>				
            </div>
		</fieldset>
	';
	return $res;
}
