<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Form to save a new custom data set -->
<div class="box">
	<?php if (isset($sections)){ $this->embed('ee:_shared/form'); } ?>
</div>
<div class="hidden">
	<script type="text/javascript">
		// data for each metric name
		var metric_values_app = <?php echo json_encode($metric_values_app); ?>;
		var metric_values_server = <?php echo json_encode($metric_values_server); ?>;

		
	</script>
</div>