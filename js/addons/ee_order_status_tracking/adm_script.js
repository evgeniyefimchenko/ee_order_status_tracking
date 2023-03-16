
(function(_, $) {
	$("body").on("change", '[name^="ee_status_select_order"]:last, [name^="ee_status_select_refund"]:last', function() {
		if ($(this).val() != "0" && $(this).val() != "777") {
			$(this).clone().appendTo($(this).parent());
		}	
	});
	$("body").on("change", '[name^="ee_status_select_order"], [name^="ee_status_select_refund"]', function() {
		var index = $(this).index() + 1;
		if ($(this).val() == "777" && index > 1) {
			$(this).remove();
		}
	});
	$('#adm_track').click(function() {
		$('#ee_adm_popup').show();
	});
	$('#close_ee_adm_popup').click(function() {
		$('#ee_adm_popup').hide();
	});
}(Tygh, Tygh.$));

	
var article_index = 0;

function toggle_refund_info_click(main_text, amount) {
	var element = document.activeElement;
	var a_value = element.id;
    
	var tmp = a_value.split('-').pop();
  		
	var refund_main_text = document.getElementsByClassName('refund_main_text')[0];
	var refund_main_article = document.getElementsByClassName('refund_main_article')[0];
	var refund_second_text = document.getElementsByClassName('refund_second_text')[0];
	var refund_second_count = document.getElementsByClassName('refund_second_count')[0];
		
	var toggle_info = document.getElementById('toggle_info');
	var display_resize_body = document.getElementById('ee_order_status_tracking_modal_body');
	
	var count = 0;
	var ul = document.getElementById('ee_order_status_tracking_refund_images');
	var k = 0;
	while(ul.getElementsByTagName('li') [k++]) count++;
			
	if (article_index != tmp) {
		toggle_info.style.display = 'block';
		for (var i = 0; i <= count; i++) {
			if (article_index != tmp) {
				if (i == tmp) {
					refund_main_text.innerHTML = main_text;
					refund_main_article.innerHTML = 'Арт. 6271E243EB780';
					refund_second_text.innerHTML = 'к возврату';
					refund_second_count.innerHTML = amount + ' шт.';
					article_index = i;
					display_resize_body.style.height = '61em';
				}
			}
		}
	} else {
		toggle_info.style.display = 'none';
		display_resize_body.style.height = '55em';
		article_index = 0;
	}         
}

function scroll_load() {
	const element = document.getElementById('ee_order_status_tracking_refund_images');
	element.scrollLeft = 0;
	document.getElementById('sw_id_1').focus();
	
	window.setTimeout(function () { 
		document.getElementById('sw_id_1').focus();
		document.getElementById('sw_id_1').style.borderBottom = '2px solid #26801b';
		document.getElementById('sw_id_1').style.fontWeight = '500';
		document.getElementById('sw_id_1').style.fontSize = '16px';
		document.getElementById('sw_id_1').style.color = '#292930';
        document.getElementById('refund_image_id_1').style.visibility = 'visible'; 

		get_order_item_id(1);
	}, 0);
}

function scroll_left() {
	const element = document.getElementById('ee_order_status_tracking_refund_images');
	var block_size = element.offsetWidth;
	var step = block_size / 5; 
	if (element.scrollLeft >= step) {
		element.scrollLeft -= step;
	}
	if (element.scrollLeft < step) {
		element.scrollLeft -= step;
		var hidden = document.getElementById('preSlide');
		hidden.style.visibility = 'hidden';
	}
	
	if (element.scrollLeft < block_size) {
		element.scrollLeft -= step;
		var hidden = document.getElementById('nextSlide');
		hidden.style.visibility = 'visible';
	}
}

function scroll_right() {
	const element = document.getElementById('ee_order_status_tracking_refund_images');
	var block_size = element.offsetWidth;
	var step = block_size / 5;
	if (element.scrollLeft >= 0) {
		element.scrollLeft += step;
		var hidden = document.getElementById('preSlide');
		hidden.style.visibility = 'visible';
	}
	if (element.scrollLeft >= block_size-3*step/2 - 1) {
		element.scrollLeft += step;
		var hidden = document.getElementById('nextSlide');
		hidden.style.visibility = 'hidden';
	}
}

function scroll_left_order() {
	const element = document.getElementById('ee_order_status_tracking_refund_menu');
	var block_size = element.offsetWidth;
	var step = block_size / 5; 
	if (element.scrollLeft >= step) {
		element.scrollLeft -= step;
	}
	if (element.scrollLeft < step) {
		element.scrollLeft -= step;
		var hidden = document.getElementById('preSlideOrder');
		hidden.style.visibility = 'hidden';
	}
	
	if (element.scrollLeft < block_size) {
		element.scrollLeft -= step;
		var hidden = document.getElementById('nextSlideOrder');
		hidden.style.visibility = 'visible';
	}
}

function scroll_right_order() {
	const element = document.getElementById('ee_order_status_tracking_refund_menu');
	var block_size = element.offsetWidth;
	var step = block_size / 5;
	if (element.scrollLeft >= 0) {
		element.scrollLeft += step;
		var hidden = document.getElementById('preSlideOrder');
		hidden.style.visibility = 'visible';
	}
	if (element.scrollLeft >= block_size-3*step/2 - 1) {
		element.scrollLeft += step;
		var hidden = document.getElementById('nextSlideOrder');
		hidden.style.visibility = 'hidden';
	}
}

function get_order_item_id(tmp, count) {
   	var toggle_info = document.getElementById('toggle_info');
    toggle_info.style.display = 'none';
  
	var element = document.activeElement;
	var a_value = element.innerHTML;
	tmp = a_value.split('№').pop();
 
    var visible_element = document.querySelectorAll('.visible');  
    var visible_image_element = document.getElementById('refund_image_id_' + tmp);  
    
    visible_element.forEach(element => {
        element.className = 'hidden';
	});    
  
    document.getElementById('refund_image_id_1').className = 'visible'; 
  
	var change_id = document.getElementById('id_' + tmp);

	if (count > 0) {
		for (var i = 1; i < count + 1; i++) {
			if (tmp != i) {
                document.getElementById('refund_image_id_' + i).className = 'hidden'; 
              
                document.getElementById('sw_id_' + i).style.borderBottom = '2px solid #eeeeee00';
				document.getElementById('sw_id_' + i).style.fontWeight = '400';
				document.getElementById('sw_id_' + i).style.fontSize = '14px';
				document.getElementById('sw_id_' + i).style.color = '#B3B3B3';
			}
			else {  
                document.getElementById('refund_image_id_' + i).className = 'visible'; 
              
				document.getElementById('sw_id_' + i).style.borderBottom = '2px solid #26801b';
				document.getElementById('sw_id_' + i).style.fontWeight = '500';
				document.getElementById('sw_id_' + i).style.fontSize = '16px';
				document.getElementById('sw_id_' + i).style.color = '#292930';
			}
		}
	}
	
	if (change_id === null)
	{
        visible_image_element.className = 'visible';
		visible_element.className = 'visible';    
	}
	else {
		change_id.setAttribute('class', change_id.className == 'visible' ? 'hidden' : 'visible'); 
	}
    
    return tmp;
}
