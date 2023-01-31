<button class="btn btn-primary" type="button" id="adm_track">Трек лист</button>

<div style="display: none;" id="ee_adm_popup">
	<div class="ui-widget-overlay ui-front" style="z-index: 1100;"></div>
	<div class="modal" tabindex="-1" role="dialog" aria-labelledby="comet_title" aria-hidden="true" style="z-index: 1200;">
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