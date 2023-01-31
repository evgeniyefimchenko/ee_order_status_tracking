<?php

use Tygh\Registry;
use Tygh\Settings;

if ($mode === 'update' && $_POST['addon'] === 'ee_order_status_tracking') {
	$_POST['ee_status_select_refund'] = $_POST['ee_status_select_refund'] ? $_POST['ee_status_select_refund'] : [];
	$_POST['ee_status_select_order'] = $_POST['ee_status_select_order'] ? $_POST['ee_status_select_order'] : [];
	while ($key = array_search('777', $_POST['ee_status_select_order'])) {
		unset($_POST['ee_status_select_order'][$key]);
	}
	while ($key = array_search('777', $_POST['ee_status_select_refund'])) {
		unset($_POST['ee_status_select_refund'][$key]);
	}
	if ($_POST['ee_status_select_order'][0] == 777) {
		$_POST['ee_status_select_order'] = [];
	}
	if ($_POST['ee_status_select_refund'][0] == 777) {
		$_POST['ee_status_select_refund'] = [];
	}
	$MY_MEGA_JSON = json_encode(['order' => $_POST['ee_status_select_order'], 'refund' => $_POST['ee_status_select_refund']]);
	$storefront_id = 0;
	if (fn_allowed_for('ULTIMATE')) {
		if (fn_get_runtime_company_id()) {
			$storefront_id = StorefrontProvider::getStorefront()->storefront_id;
		}
	}
	$settings_manager = Settings::instance(['storefront_id' => $storefront_id]);
	$settings_manager->updateValue('json_settings', $MY_MEGA_JSON);	
}