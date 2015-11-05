<?php $compliance = wp_kses_post($_POST['compliance_message']); ?>
<?php $compliance = wp_parse_args($compliance, array(
		'agent_name' => false,
		'office_name' => false,
		'office_phone' => false,
		'img' => false,
		'disclaimer' => false,
		'agent_license' => false,
		'co_agent_name' => false,
		'co_office_name' => false
		)
	);

	if (!empty($compliance['office_name'])) {
		$attribution_message = '<p class="pdp-attribution">Courtesy of ';

		if (!empty($compliance['agent_name'])) {
			$attribution_message .= '<span class="pdp-agent-attribution">';
			$attribution_message .= $compliance['agent_name'];
			$attribution_message .= ' of </span>';
		}

		$attribution_message .= $compliance['office_name'];

		if (!empty($compliance['office_phone'])) {
			$attribution_message .= '<span class="pdp-phone-attribution">';
			$attribution_message .= ", Phone:&nbsp;<span style='white-space: nowrap'>" . $compliance['office_phone'] . "</span>";
			$attribution_message .= '</span>';
		}

		if (!empty($compliance['agent_license'])) {
			$attribution_message .= '<span class="pdp-license-attribution">';
			$attribution_message .= ", DRE#:&nbsp;" . $compliance['agent_license'];
			$attribution_message .= '</span>';
		}

		$attribution_message .= '</p>';
	}
?>

<?php if (!empty($attribution_message)) : ?>
	<div class="compliance-wrapper"><?php echo $attribution_message; ?></div>
<?php endif; ?>
