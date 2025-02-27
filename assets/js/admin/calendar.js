jQuery(function ($) {
	'use strict';
	/* global jQuery */

	var MBH_Calendar = {
		init: function () {
			this.datepicker();
			this.show_tooltip();
		},

		datepicker: function () {
			$('.bc-datepicker').datepicker({
				dateFormat: 'yy-mm-dd'
			});
		},

		show_tooltip: function () {
			$('.hastip').tipsy({
				live: true,
				delayIn: 200,
				delayOut: 200
			});
		}
	};

	$(document).ready(function () {
		MBH_Calendar.init();
	});
});
