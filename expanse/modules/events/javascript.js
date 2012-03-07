function expanse(){
if(!$('online')){
return;
}
insertAfter($('event_date').parentNode, domEl('a', 'Select a date', {id:'select_date', href:'javascript:;', name:'select_date'}), $('event_date'));
insertAfter($('event_date').parentNode, domEl('div', 'Select a date', {id:'calendar', style:'position:absolute;visibility:hidden;background-color:white;layer-background-color:white;'}), $('event_date'));
	addEvent($('select_date'), 'click', function(){
		var cal = new CalendarPopup("calendar");
		cal.select($('event_date'),'select_date','d MMM, y');
	});
}