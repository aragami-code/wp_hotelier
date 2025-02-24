jQuery(function ($) {
	'use strict';
	/* global jQuery */

	var MBH_Meta_Boxes = {
		init: function () {
			this.show_tooltip();
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
		MBH_Meta_Boxes.init();
	});
});
