function expanse(){
	if(!$('online')) {
		return;
	}

	$(function () {
		$('#event_date').datepicker();
	});
}