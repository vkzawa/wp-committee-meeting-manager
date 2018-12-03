jQuery(document).ready(function($) {

	var siteURL = wordpressData.websiteURL;

	$('#calendar').fullCalendar({
		events: siteURL + '/calendar-feed/',
		theme: true,
		ignoreTimezone: true
	});

});
