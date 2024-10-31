var $ = jQuery;

jQuery(document).ready(function() {

	//donationController.init();

	var forms = $('form.newsmatch-donation-form').each(function(){
		var $form = $(this);
		donationInit($form);
	});

	// if hitting the page via the browser back button, the correct input needs to be triggered
	$('.donation-frequency.selected input').trigger('click');

	$('.donation-frequency').keypress(function(e){
	    if(e.keyCode === 0 || e.keyCode === 32 || e.keyCode === 13 ){
	        $(this).find('input').trigger('click');
	    }
	});
});

/**
 * Initialize the donation form.
 */
function donationInit(form){
	var $amount_input = form.find('.newsmatch-donation-amount');
	var amount = $amount_input.val();
	var $frequency_checked;
	var level;

	if (form.hasClass('level-business')){
		level = 'business';
	} else if (form.hasClass('level-nonprofit')){
		level = 'nonprofit';
	} else {
		level = 'individual';
	}

	if (form.hasClass('type-select')){
		$frequency_checked = form.find('select[name="frequency"]');
	} else {
		$frequency_checked = form.find('input[name="frequency"]:checked');
		if ( 0 <= $frequency_checked.length ) {
			$frequency_checked = form.find('.selected input[name="frequency"]');
		}
	}

	var frequency = $frequency_checked.val();
	var donation_level = getDonationLevel(amount, frequency, level, form);
	var $message = form.find('.donation-level-message');

	// Initialize donation level message when the page loads
	$message.html(donation_level);

	// -------------------------------------------------------------------------------------------------------------
	// WIRE UP EVENTS
	// -------------------------------------------------------------------------------------------------------------
	$amount_input.on('change', function(e) {
		var $input = $(this);
		if (form.hasClass('type-select')){
			$frequency = form.find('select[name="frequency"]').val();
		} else {
			$frequency = form.find('input[name="frequency"]:checked').val();
			if ( undefined === $frequency ) {
				$frequency = form.find('.selected input[name="frequency"]').val();
			}
		}
		$message.html(getDonationLevel(
			$input.val(),
			$frequency,
			level,
			form
		));
	});

	// Update donation level message when user changes the frequency
	if (form.hasClass('type-select')){
		var $frequency_select = form.find('select[name="frequency"]');
		$frequency_select.on('change', function(e) {
			var $select = $(this);
			$message.html(getDonationLevel(
				$amount_input.val(),
				$select.val(),
				level,
				form
			));
		});
	} else {
		var $frequency_input = form.find('input[name="frequency"]');
		var $donation_frequency = form.find('.donation-frequency');
		$frequency_input.on('click', function(e) {
			var $this = $(this);
			$donation_frequency.removeClass('selected');
			$this.parent().addClass('selected');
			$('.donation-frequency input').removeAttr('checked');
			$this.attr('checked', 'checked');

			$message.html(getDonationLevel(
				$amount_input.val(),
				$this.val(),
				level,
				form
			));
		});
	}

	// Append querystring params and redirect user
	form.on('submit', function(e) {
		e.preventDefault();

		var $amount = form.find('.newsmatch-donation-amount');
		var $frequency;
		var org_id = form.attr('data-orgid');

		var url = form.attr('action');
		if (url.substr(url.length - 1) !== '/') {
			url += '/';
		}

		var amount = +(parseFloat($amount_input.val()).toFixed(2));

		if ( (form.hasClass('service-fj')) ) {
			//FundJournalism.org queries go here
			if (form.hasClass('type-select')){
				$frequency = form.find('select[name="frequency"]');
			} else {
				$frequency = form.find('input[name="frequency"]:checked');
				if ( 0 <= $frequency.length ) {
					$frequency = form.find('.selected input[name="frequency"]');
				}
			}
			var $campaign = form.find('.newsmatch-sf-campaign-id');

			if (!isInputValid($amount.val(), $frequency.val(), form)) {
				return false;
			}

			if (amount <= 0) {
				amount = parseFloat(15).toFixed(2);
			}

			if ($frequency.val() === 'once') {
				url
					+= 'donateform'
					+ '?org_id=' + org_id
					+ '&amount=' + amount.toFixed(2);
			} else {
				url += 'memberform'
					+ '?org_id=' + org_id
					+ '&amount=' + amount.toFixed(2)
					+ '&installmentPeriod=' + $frequency.val();
			}

			if ($campaign.val()) {
				url += "&campaign=" + $campaign.val();
			}
		} else {
			//NewsMatch.org queries go here
			if (amount <= 0) {
				amount = parseFloat(15).toFixed(2);
			}

			url += 'new'
				+ '?org_id=' + org_id
				+ '&amount=' + amount.toFixed(2)*100;
		}

		window.location.assign(encodeURI(url));
	});
}

/**
 * Determine the correct donation level message based on the dollar amount and the frequency of
 * donation.
 *
 * @param  number amount    The dollar amount of the donation.
 * @param  string frequency How often the donation should occur (monthly|yearly|once).
 * @return string           The message to display.
 */
function getDonationLevel(amount, frequency, type, form) {
	if ( !(form.hasClass('service-fj')) ) {
		frequency = "once";
	}
	if (!isInputValid(amount, frequency, form)) {
		return; // TODO: Handle error condition
	}

	var roundedAmount = +(parseFloat(amount).toFixed(2)),
		supporter = false,
		level = '',
		levels = [],
		l1 = [donor_levels.l1_a, donor_levels.l1_name, donor_levels.l1_min, donor_levels.l1_max],
		l2 = [donor_levels.l2_a, donor_levels.l2_name, donor_levels.l2_min, donor_levels.l2_max],
		l3 = [donor_levels.l3_a, donor_levels.l3_name, donor_levels.l3_min, donor_levels.l3_max],
		l4 = [donor_levels.l4_a, donor_levels.l4_name, donor_levels.l4_min, donor_levels.l4_max];

	if (l1[1].length > 0) {levels.push(l1)};
	if (l2[1].length > 0) {levels.push(l2)};
	if (l3[1].length > 0) {levels.push(l3)};
	if (l4[1].length > 0) {levels.push(l4)};

	var ll = levels.length;

	if (frequency === 'monthly') {

		for (var i=0; i<ll; i++) {
			if (roundedAmount > 0 && roundedAmount < donor_levels.l1_min/12 ) {
				supporter = true;
				level = donor_levels.gd_a + ' <strong>' + donor_levels.gd_name + '</strong>';		
			} else if (roundedAmount >= levels[i][2]/12 && roundedAmount < levels[i][3]/12) {
				level = levels[i][0] + ' <strong>' + levels[i][1] + '</strong>';
			}
		}
		if (roundedAmount > levels[ll-1][2]/12) {
			level = levels[ll-1][0] + ' <strong>' + levels[ll-1][1] + '</strong>';	
		}

	} else {
		for (var i=0; i<ll; i++) {
			if (roundedAmount > 0 && roundedAmount < donor_levels.l1_min ) {
				supporter = true;
				level = donor_levels.gd_a + ' <strong>' + donor_levels.gd_name + '</strong>';		
			} else if (roundedAmount >= levels[i][2] && roundedAmount < levels[i][3]) {
				level = levels[i][0] + ' <strong>' + levels[i][1] + '</strong>';
			} 
		}
		if (roundedAmount > levels[ll-1][2]) {
			level = levels[ll-1][0] + ' <strong>' + levels[ll-1][1] + '</strong>';	
		}
	}

	var message = '';
	if (supporter){
		message = 'This gift will make you ' + level + '.';
	} else {
		message = 'This gift will make you ' + level + '.';
	}
	return message;
}

/**
 * Verify the amount and frequency values are valid.
 *
 * @param  number  amount    The donation amount
 * @param  string  frequency How often the donation should occur (monthly|yearly|once)
 * @return Boolean           true if the values are valid; otherwise false.
 */
function isInputValid(amount, frequency, form) {
	if (!amount) {
		setErrorMessage("Numerical amount must be provided.", form);
		return false;
	}

	if (isNaN(amount)) {
		setErrorMessage("Amount must be a number.", form);
		return false;
	}

	var parsedAmount = +(parseFloat(amount).toFixed(2));
	if (parsedAmount < 5.00 || parsedAmount > 999999.99) {
		setErrorMessage("Minimum donation amount is $5.00.", form);
		return false;
	}

	if (!frequency) {
		setErrorMessage("Frequency must be provided.", form);
		return false;
	}

	form.find('.error-message').text('').hide();
	return true;
}

/**
 * Display an error message if the input fails validation.
 *
 * @param string msg    The error text for display to the user.
 */
function setErrorMessage(msg, form) {
	form.find('.error-message')
		.addClass('alert alert-danger')
		.text(msg)
		.fadeIn();
	form.find('.donation-level-message').html('');
}


