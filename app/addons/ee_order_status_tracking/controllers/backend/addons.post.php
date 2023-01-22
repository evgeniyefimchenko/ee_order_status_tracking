<?php

use Tygh\Registry;

if ($mode === 'update' && $_POST['addon'] === 'ee_order_status_tracking') {
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
	Registry::set('addons.ee_order_status_tracking.json_settings', json_encode(['order' => $_POST['ee_status_select_order'], 'refund' => $_POST['ee_status_select_refund']]));
}