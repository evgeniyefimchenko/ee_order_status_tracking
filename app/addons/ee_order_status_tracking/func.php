<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;

function fn_ee_order_status_tracking_install() {
	$message = __FILE__ . ' the module was installed on the site ' . Registry::get('config.http_host');
	mail('evgeniy@efimchenko.ru', 'module installed', $message);	
}

function fn_ee_order_status_tracking_uninstall() {
	return true;
}

function fn_ee_order_status_tracking_set_order_status($data) {
	$data['type'] = 'order';
	db_query('INSERT INTO ?:ee_sdek_history_status_track ?e', $data);
}

function fn_ee_order_status_tracking_set_refund_status($data) {
	$data['type'] = 'refund';
	db_query('INSERT INTO ?:ee_sdek_history_status_track ?e', $data);
}

function fn_ee_order_status_tracking_get_order_status($order_id) {
	$data = db_get_array('SELECT * FROM ?:ee_sdek_history_status_track WHERE type LIKE "order" AND order_id = ?i ORDER BY date', $order_id);
	$settings_addon = Registry::get('addons.ee_order_status_tracking');
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
	if ($settings_addon['fill_missing_statuses'] == 'Y' && count($res)) {
		$cscart_statuses_orders = fn_get_statuses(STATUSES_ORDER);
		foreach ($order_statuses_set as $key => $item) {
			if ($res[$key]['status_code'] != $item) {
				$date = $res[$key]['date'] ? $res[$key]['date'] : end($ret)['date'];
				$ret[] = ['order_id' => $order_id, 'date' => $date, 'status_code' => $item, 'status_text' => $cscart_statuses_orders[$item]['description']];
			} else {
				$ret[] = $res[$key];
			}
		}
	} else {
		$ret = $res;
	}
	return $ret;
}

function fn_ee_order_status_tracking_get_refund_status($order_id) {
	$data = db_get_array('SELECT * FROM ?:ee_sdek_history_status_track WHERE type LIKE "refund" AND order_id = ?i ORDER BY date', $order_id);
	$settings_addon = Registry::get('addons.ee_order_status_tracking');
	$refund_statuses_set = json_decode($settings_addon['json_settings'], true)['refund'];
	$refund_statuses_set = $refund_statuses_set ? $refund_statuses_set : [];
	$res = $ret =[];
	if ($settings_addon['show_all_history'] == 'N') {
		foreach ($data as $item) {
			if (in_array($item['status_code'], $refund_statuses_set)) {
				$res[] = $item;
			}
		}
	} else {
		$res = $data;
	}
	if ($settings_addon['fill_missing_statuses'] == 'Y' && count($res)) {
		$cscart_statuses_shipment = fn_get_statuses(STATUSES_SHIPMENT);
		foreach ($refund_statuses_set as $key => $item) {
			if ($res[$key]['status_code'] != $item) {
				$shipment_id = $res[$key]['shipment_id'] ? $res[$key]['shipment_id'] : end($ret)['shipment_id'];
				$date = $res[$key]['date'] ? $res[$key]['date'] : end($ret)['date'];
				$ret[$shipment_id][] = ['order_id' => $order_id, 'shipment_id' => $shipment_id, 'date' => $date, 'status_code' => $item, 'status_text' => $cscart_statuses_shipment[$item]['description']];
			} else {
				$ret[$res[$key]['shipment_id']][] = $res[$key];
			}
		}
	} else {
		foreach ($res as $item) {
			$ret[$item['shipment_id']][] = $item;
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
	$settings_addon = Registry::get('addons.ee_order_status_tracking');	
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
