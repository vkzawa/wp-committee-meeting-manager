jQuery(document).ready(function($) {
	
	$(".event-date").datepicker({
	    dateFormat: 'DD, MM d, yy',
	    showOn: 'both',
	    numberOfMonths: 3
	    });
	
	$(".event-time").timepicker({
        timeFormat: 'h:mmTT',
		ampm: true,
		stepMinute: 15
	});

});