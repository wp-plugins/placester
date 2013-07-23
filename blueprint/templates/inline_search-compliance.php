<?php $compliance_message = wp_kses_post($_POST['compliance_message']); ?>
<?php $compliance_message = wp_parse_args($compliance_message, array(
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
?>
<div class="clear"></div>
<div class="compliance-wrapper">
	<?php if ($compliance_message['img']): ?>
		<img src="<?php echo $compliance_message['img'] ?>" alt="">
	<?php endif; ?>
	<?php if ($compliance_message['agent_name']): ?>
		<p class="inline-compliance">Listing Agent: <?php echo $compliance_message['agent_name'] ?></p>
    <?php endif; ?>
    <?php if (!empty($compliance_message['agent_license'])): ?>
        <p class="inline-compliance">DRE#: <?php echo $compliance_message['agent_license'] ?></p>
    <?php endif; ?>
    <?php if (!empty($compliance_message['office_name'])): ?>
        <p class="inline-compliance">Courtesy of: <?php echo $compliance_message['office_name'] ?></p>
    <?php endif; ?>
    <?php if (!empty($compliance_message['office_phone'])): ?>
        <p class="inline-compliance">Office Phone: <?php echo $compliance_message['office_phone'] ?></p>
    <?php endif; ?>
    <?php if (!empty($compliance_message['co_agent_name'])): ?>
        <p class="inline-compliance">Co-Listing Agent: <?php echo $compliance_message['co_agent_name'] ?></p>
    <?php endif; ?>
    <?php if (!empty($compliance_message['co_office_name'])): ?>
        <p class="inline-compliance">Co-Listing Office: <?php echo $compliance_message['co_office_name'] ?></p>
    <?php endif; ?>
	<?php if ($compliance_message['disclaimer']): ?>
		<p class="disclaimer"><?php echo $compliance_message['disclaimer'] ?></p>
	<?php endif; ?>
</div>
<div class="clear"></div>
