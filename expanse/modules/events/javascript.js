function Expanse() {
	if(!$('online')) {
		return;
	}

	$(function () {
		$('#event_date').datepicker();
	});
}