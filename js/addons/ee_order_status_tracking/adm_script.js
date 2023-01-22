(function(_, $) {
	$("body").on("change", \'[name^="ee_status_select_order"]:last, [name^="ee_status_select_refund"]:last\', function() {
		if ($(this).val() != "0" && $(this).val() != "777") {
			$(this).clone().appendTo($(this).parent());
		}
	});
	$("body").on("change", \'[name^="ee_status_select_order"], [name^="ee_status_select_refund"]\', function() {
		var index = $(this).index() + 1;
		if ($(this).val() == "777" && index > 1) {
			$(this).remove();
		}
	});
}(Tygh, Tygh.$));