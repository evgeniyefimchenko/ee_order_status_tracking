<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks (
	'change_order_status_post',
	'ee_sdek_history_status_change'
);
