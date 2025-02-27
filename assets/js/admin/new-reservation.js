jQuery(function ($) {
	'use strict';
	/* global jQuery */
	/* eslint-disable no-multi-assign */

	var MBH_New_Reservation = {
		init: function () {
			this.add_room();
			this.remove_room();
			this.datepicker();
		},

		clone_room_row: function (row) {
			var key = 1;
			var highest = 1;

			row.parent().find('.add-new-room-row').each(function () {
				var current = $(this).data('key');

				if (parseInt(current, 10) > highest) {
					highest = current;
				}
			});

			key = highest += 1;

			var clone = row.clone();

			clone.attr('data-key', key);
			clone.find('input').val(1);
			clone.find('input, select').each(function () {
				var input = $(this);
				var name = input.attr('name');

				if (name) {
					name = name.replace(/\[(\d+)\](?!.*\[\d+\])/, '[' + parseInt(key, 10) + ']');
					input.attr('name', name);
				}
			});

			return clone;
		},

		add_room: function () {
			$('form.add-new-reservation-form').on('click', '.add-new-room', function (e) {
				e.preventDefault();

				var button = $(this);
				var row = button.parent().find('.add-new-room-row').last();
				var clone = MBH_New_Reservation.clone_room_row(row);

				clone.insertAfter(row);

				$('button.remove-room').prop('disabled', false);
			});
		},

		remove_room: function () {
			$('form.add-new-reservation-form').on('click', '.remove-room', function (e) {
				e.preventDefault();

				var button = $(this);
				var row = button.parent();
				var rows = button.closest('td').find('.add-new-room-row');
				var count = rows.length;

				if (count > 1) {
					$('input', row).val('');
					row.fadeOut('fast').remove();
				}

				if (count === 2) {
					$('button.remove-room').prop('disabled', true);
				}
			});
		},

		datepicker: function () {
			var from_input = $('form.add-new-reservation-form').find('.date-from');
			var to_input = $('form.add-new-reservation-form').find('.date-to');

			from_input.datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 0,
				changeMonth: true,
				onClose: function () {
					var date = $(this).datepicker('getDate');

					if (date) {
						date.setDate(date.getDate() + 1);
						to_input.datepicker('option', 'minDate', date);
					}
				}
			});

			to_input.datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 1,
				changeMonth: true
			});
		}
	};

	$(document).ready(function () {
		MBH_New_Reservation.init();
	});
});
