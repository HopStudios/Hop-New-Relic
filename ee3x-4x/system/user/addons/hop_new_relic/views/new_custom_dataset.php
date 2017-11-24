<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Form to save a new custom data set -->
<?php if (version_compare(APP_VER, '4', '<') || isset($metric_names_app_error) || isset($metric_names_server_error)) { ?>
<div class="box box-new-dataset">
<?php } ?>

	<?php if (isset($metric_names_app_error)){ ?>
		<p class="nr-error"><?=$metric_names_app_error?></p>
	<?php } ?>
	<?php if (isset($metric_names_server_error)){ ?>
		<p class="nr-error"><?=$metric_names_server_error?></p>
	<?php } ?>
	<?php if (isset($sections)){ $this->embed('ee:_shared/form'); } ?>

<?php if (version_compare(APP_VER, '4', '<') || isset($metric_names_app_error) || isset($metric_names_server_error)) { ?>
</div>
<?php } ?>
<div class="hidden">
	<script type="text/javascript">
		// data for each metric name
		var metric_values_app = <?php echo json_encode($metric_values_app); ?>;
		var metric_values_server = <?php echo json_encode($metric_values_server); ?>;

		
	</script>
</div>