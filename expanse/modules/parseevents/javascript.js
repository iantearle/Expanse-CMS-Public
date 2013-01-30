function Expanse() {
	if(!$('online')) {
		return;
	}

	$(function () {
		$('#event_date_start')
		    .datepicker()
		    .on('changeDate', function(ev){
		        if (ev.date.valueOf() > endDate.valueOf()){
		            $('#alert').show().find('strong').text('The start date must be before the end date.');
		        } else {
		            $('#alert').hide();
		            startDate = new Date(ev.date);
		            $('#date-start-display').text($('#event_date_start').data('date'));
		        }
		        $('#devent_date_start').datepicker('hide');
		    });
		$('#event_date_end')
		    .datepicker()
		    .on('changeDate', function(ev){
		        if (ev.date.valueOf() < startDate.valueOf()){
		            $('#alert').show().find('strong').text('The end date must be after the start date.');
		        } else {
		            $('#alert').hide();
		            endDate = new Date(ev.date);
		            $('#date-end-display').text($('#event_date_end').data('date'));
		        }
		        $('#event_date_end').datepicker('hide');
		    });

		   $('.timepicker-default').timepicker();
	});
}