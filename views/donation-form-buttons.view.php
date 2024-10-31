<div>
	<form class="newsmatch-donation-form form-inline level-<?php echo esc_attr( $data['level'] ); ?> service-<?php echo esc_attr( $data['form_type'] ); ?>" action="<?php echo esc_attr( esc_url( $data['url'] ) ); ?>" method="POST" data-orgid="<?php echo esc_attr( $data['org_id'] ); ?>">
		<div class="newsmatch-donate-label">I would like to donate:</div>
		<div class="form-group">
			<label class="newsmatch-donation-amount-label" for="newsmatch-donation-amount">I would like to donate:</label>
			<input type="number" name="newsmatch-donation-amount" class="newsmatch-donation-amount form-control" value="<?php echo esc_attr( $data['amount'] ); ?>" placeholder="Amount">
		</div>
		<div class="donation-frequency-group" role="group">
			<label class="donation-frequency" tabindex="0"><input type="radio" name="frequency" value="monthly">Per Month</label>
			<label class="donation-frequency selected" tabindex="0" ><input type="radio" name="frequency" value="yearly" checked>Per Year</label>
			<label class="donation-frequency" tabindex="0"><input type="radio" name="frequency" value="once">One Time</label>
		</div>
		<div class="error-message" role="alert" style="display: none;"></div>
		<div class="donation-level-message"></div>
		<button type="submit">Give Now</button>
		<input class="newsmatch-sf-campaign-id" name="newsmatch-sf-campaign-id" type="hidden" value="<?php echo esc_attr( $data['sf_campaign_id'] ); ?>">
	</form>
</div>
