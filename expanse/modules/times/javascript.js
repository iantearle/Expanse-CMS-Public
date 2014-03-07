function Expanse() {
	if(!$('online')) {
		return;
	}

	$(function () {
		$('#event_date').datepicker();
		$('.timepicker-default').timepicker({defaultTime:'value'});
	});
}