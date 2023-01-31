<button class="ty-tabs__a" type="button" id="adm_track">Трек лист</button>

<div style="display: none; position: absolute;
    top: 0%;
    left: 50%;
    width: 30%;
    background: aliceblue;
    padding: 5px;" id="ee_adm_popup">
	<div class="modal" tabindex="-1" role="dialog" aria-labelledby="comet_title" aria-hidden="true">
		<div class="modal-header">
			<h3 id="comet_title" style="widht: 100%;">Трек лист<span id="close_ee_adm_popup" style="float: right; cursor: pointer;">X</span></h3>
		</div>
		<div class="modal-body">
			{$arr_order_track = $_REQUEST.order_id|fn_ee_order_status_tracking_get_order_status}
			{$arr_refund_track = $_REQUEST.order_id|fn_ee_order_status_tracking_get_refund_status}
			{if $arr_order_track}					
					<h3 style="padding: unset; margin: unset;">Заказ:</h3>
					<ul>
					{foreach $arr_order_track as $item}
						<li>{$item.date} - {$item.status_text}</li>
					{/foreach}
					</ul>
					{if $arr_refund_track}<hr/>{/if}
			{else}
				Пусто тут! Что в принципе не реально.
				<hr/>
			{/if}				
			{if $arr_refund_track}
				{$count = 0}
				<h3 style="padding: unset; margin: unset;">Возврат:</h3>
				{foreach $arr_refund_track as $item}
					{$count = $count + 1}
					№ {$count}
					<ul>
					{foreach $item as $item_r}
						<li>{$item_r.date} - {$item_r.status_text}</li>
					{/foreach}
					</ul>
				{/foreach}
			{/if}
		</div>
	</div>	
</div>
<!-- Добавлен хук orders:rus_delivery_tracking в /design/themes/responsive/templates/addons/rus_delivery/overrides/views/orders/details.tpl -->