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

function fn_ee_order_status_tracking_information() {
	$cscart_statuses_orders = fn_get_statuses(STATUSES_ORDER, [], true);	
	foreach ($cscart_statuses_orders as $key => $value) {
		$ee_select_value .= '<option value="' . $key . '">' . $value['description'] . '</option>';
	}	
	$ee_select_box = '
		<select name="ee_status_select[]">
			' . $ee_select_value . '
		</select>
	';
	$res = '
        <fieldset>
            <div id="container_ee_order_status_tracking" class="control-group setting-wide  sw_checkbox_pd">
                <label for="addon_ee_order_status_tracking" class="control-label">Список статусов доставки в порядке очерёдности:</label>
                <div class="controls">
                    ' . $ee_select_box . '
                </div>
                <div class="controls">
                    ' . $ee_select_box . '
                </div>				
            </div>
		</fieldset>			
	';
}
