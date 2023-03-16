<button class="btn btn-primary" type="button" id="adm_track" onclick="scroll_load();">Трек лист</button>
<div style="display: none;" id="ee_adm_popup">
	<div class="ui-widget-overlay ui-front" style="align-items: center; z-index: 1100;"></div>
	<div class="ee_order_status_tracking_modal" tabindex="-1" role="dialog" aria-labelledby="comet_title" aria-hidden="true">
        <div class="ee_order_status_tracking_modal-dialog">
            <div class="ee_order_status_tracking_modal-content">
		        <div class="ee_order_status_tracking_modal-header">
                    <h3 id="comet_title">Трек-лист заказа №{$_REQUEST.order_id}</h3>
                    <span id="close_ee_adm_popup" class="ee_order_status_tracking_close">×</span>
                </div>
                <div id="ee_order_status_tracking_modal_body" class="ee_order_status_tracking_modal-body">
                    {$arr_order_track = $_REQUEST.order_id|fn_ee_order_status_tracking_get_order_status}
                    {$arr_refund_track = $_REQUEST.order_id|fn_ee_order_status_tracking_get_refund_status}
                    {$arr_change_status = ''|fn_ee_order_status_tracking_get_change_status_orders}
                    {$arr_change_status_refund = ''|fn_ee_order_status_tracking_get_change_status_refund}
                    
                    <div class="ee_order_status_tracking_order">
                        {if $arr_order_track}
                            {$index_temp = 0}
                            {foreach $arr_order_track as $item}
                                {if $item.status_text}
                                    {$index = array_search($item, $arr_order_track)}
                                    <div class="order-tracking completed">
                                        <span class="is-complete"></span>
                                        <p style="color: #4AA356;">{$item.date|strtotime|date_format:"%d.%m.%Y"}<span style="margin-left: 75px; white-space: nowrap;">{$arr_change_status[$index]}</span></p>
                                        {$index_temp = $index}
                                    </div>
                                    {if $index < count($arr_order_track)-1}
                                        <div class="ee_order_status_tracking_vertical"></div>
                                    {/if}
                                {/if}
                            {/foreach}
                            {foreach array_slice($arr_change_status, $index + 1) as $item_change_status}
                                <div class="ee_order_status_tracking_vertical"></div>
                                <div class="order-tracking">
                                    <span class="is-complete"></span>
                                    <p style="color: #27aa80;"><span style="color: #A4A4A4; margin-left: 163px; white-space: nowrap;">{$item_change_status}</span></p>
                                </div>
                            {/foreach}
                        {else}
                            Пусто тут! Что в принципе не реально.
                        {/if}				
                    </div>
                    <div class="ee_order_status_tracking_refund">
                        {if $arr_refund_track} 
                            {$refund_images = array()}                            
                            {$item_refund_track_index = 1}
                            {$count = 0}
                            <ul id="ee_order_status_tracking_refund_menu" onclick="get_order_item_id({$item_refund_track_index}, {count($arr_refund_track)})">
                                {foreach $arr_refund_track as $item}   
                                    {$count = $count + 1}
                                    <li>
                                        <a href="#" id="sw_id_{$count}">Возврат №{$count}</a>
                                    </li>      
                                {/foreach}
                            </ul>
                            {if $count > 4}
                                <a href="#" id="preSlideOrder" class="preSlideOrder" onclick="scroll_left_order()" style="visibility: hidden;">
                                    <div class="scroll_arrow_left_wrap_order">
                                        <div class="scroll_arrow_left_order"></div>
                                    </div>
                                </a>
                                <a href="#" id="nextSlideOrder" class="nextSlideOrder" onclick="scroll_right_order()">
                                    <div class="scroll_arrow_right_wrap_order">
                                        <div class="scroll_arrow_right_order"></div>
                                    </div>
                                </a>
                            {/if}
                            <hr style="	border: 1px solid #A3A3A3; width: 106%; margin: -.4% 0 0 -3%;"/>                     
                            <ul id="ee_order_status_tracking_refund_images">
                                {$image_item = 1}
                                <div id="refund_images_wrap">
                                    {foreach $arr_refund_track as $item}
                                        <div id="refund_image_id_{$image_item}" class="hidden" style="padding-top:0;">
                                            {foreach $item.order_data as $image}
                                                <li>
                                                    {if $image}
                                                        {$image_info = $item.order_data[$image_item - 1]}          
                                                        <a href="#" id="toggle_refund_image_index-{$image_item}" onclick="toggle_refund_info_click('{$image.name}', '{$image.amount}')">
                                                            <div class="eye_wrapper">
                                                                <img class="bottom" src="/design/backend/media/images/addons/ee_order_status_tracking/Simple_Icon_Eye.png" alt="eye">
                                                            </div>
                                                            <div class="select_wrapper">
                                                                <div class="bottom_select"></div>
                                                                <div class="bottom_border"></div>
                                                            </div>
                                                            <img class="top" src="{$image.images.image_path}" alt="Image №{$image_item}" width="100">
                                                        </a>
                                                    {/if}
                                                </li>
                                                {$image_item = $image_item + 1}
                                            {/foreach}
                                        </div>
                                    {/foreach}
                                </div>
                            </ul>
                            {if count($arr_refund_track) > 2}
                                <a href="#" id="preSlide" class="preSlide" onclick="scroll_left()" style="visibility: hidden;">
                                    <div class="scroll_arrow_left_wrap">
                                        <div class="scroll_arrow_left"></div>
                                    </div>
                                </a>
                                <a href="#" id="nextSlide" class="nextSlide" onclick="scroll_right()">
                                    <div class="scroll_arrow_right_wrap">
                                        <div class="scroll_arrow_right"></div>
                                    </div>
                                </a>
                                <hr style="	border: 1px solid #A3A3A3; width: 106%; margin: 23px 0 0 -3%;"/>
                                {else}
                                <hr style="	border: 1px solid #A3A3A3; width: 106%; margin: 0 0 0 -3%;"/>
                            {/if}
               
                            <div id="toggle_info" class="refund_wrap" style="display: none;">
                                <div class="refund_info">
                                    <div class="refund_info_text">
                                        <p class="refund_main_text"></p>
                                        <p class="refund_main_article"></p> 
                                    </div>
                                    <div class="vertical_line"></div>
                                    <div class="refund_info_count">
                                        <p class="refund_second_text"></p>
                                        <p class="refund_second_count"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="ee_order_status_refund">
                                {$index_temp = 0}
                                {$vertical_index = 0}
                                {foreach $arr_refund_track as $item}
                                    {$index_refund_item = array_search($item, $arr_refund_track)}
                                    {if $item_refund_track_index}
                                        <div id="id_{$index_temp+1}" class="hidden">
                                            {foreach $item as $item_r}
                                                {if $item_r.date}
                                                    {if $item_r.status_text}
                                                        <div class="order-tracking completed">
                                                            <span class="is-complete"></span>
                                                            <p style="color: #4AA356;">{$item_r.date|strtotime|date_format:"%d.%m.%Y"}<span style="margin-left: 75px; white-space: nowrap;">{$item_r.status_text}</span></p>
                                                            {$vertical_index = $vertical_index + 1}
                                                        </div>
                                                        {if $vertical_index < count($item)}
                                                            <div class="ee_order_status_tracking_vertical">
                                                            </div>
                                                        {/if}
                                                    {/if}
                                                {/if}
                                            {/foreach}
                                            {$index_temp = $index_temp + 1}
                                        </div>
                                    {/if}
                                {/foreach}
                                {foreach array_slice($arr_change_status_refund, $index_temp) as $item_change_status}
                                    <div class="ee_order_status_tracking_vertical"></div>
                                    <div class="order-tracking">
                                        <span class="is-complete"></span>
                                        <p style="color: #27aa80;"><span style="color: #A4A4A4; margin-left: 163px; white-space: nowrap;">{$item_change_status}</span></p>
                                    </div>
                                {/foreach}
                            </div>
                            </div>
                        </div>
                    {/if}
                 </div>
                </div>
            </div>
        </div>
	</div>	
</div>