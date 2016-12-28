<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="box">
	<div class="tbl-ctrls">
		<?php if (isset($app)) { ?>

			<?php if ( !is_int($app) ) { ?>
			<h1><span class="nc-server-name-<?=$app->health_status?>"><?=$app->name;?></span></h1>

			<div class="nr-app-summary nr-data-row">
				<div class="nr-cell nr-cell-6">
					<h3><?= lang('app')?></h3>
					<?php if (isset($app->application_summary)) { ?>
					<p class="nr-num-data"><span><?= $app->application_summary->response_time?></span> ms</p>
					<p class="nr-num-data"><span><?= $app->application_summary->throughput?></span> rpm</p>
					<p class="nr-num-data"><span><?= $app->application_summary->error_rate?></span> err%</p>
					<p class="nr-num-data">apdex <span><?= $app->application_summary->apdex_score?></span></p>
					<?php } else { ?>
					<p><?= lang('no_app_sum_received')?></p>
					<?php } ?>
				</div>
				<div class="nr-cell nr-cell-6">
					<h3><?= lang('end_user')?></h3>
					<?php if (isset($app->end_user_summary)) { ?>
					<p class="nr-num-data"><span><?= $app->end_user_summary->response_time?></span> ms</p>
					<p class="nr-num-data"><span><?= $app->end_user_summary->throughput?></span> rpm</p>
					<p class="nr-num-data">apdex <span><?= $app->end_user_summary->apdex_score?></span></p>
					<?php } else { ?>
					<p><?= lang('no_enduser_sum_received')?></p>
					<?php } ?>
				</div>
			</div>
			<?php } else { ?>
				<p class="nr-error">Couldn't fetch app summary data</p>
			<?php } ?>
			<?php if ( isset($server) && !is_int($server) && isset($server->summary) ) { ?>
			<div class="nr-app-summary nr-data-row">
				<div class="nr-cell nr-cell-6">
					<h3><?= lang('server')?></h3>
					<p class="nr-num-data"><span><?= $server->summary->cpu?></span> cpu%<?php if($server->summary->cpu_stolen > 0){ ?> (<?= $server->summary->cpu_stolen?> cpu stolen%)<?php } ?></p>
					<p class="nr-num-data"><span><?= $server->summary->memory?></span> mem% <?php if(isset($server_memory_used) && isset($server_memory_total)) {?> (<?=$server_memory_used?> / <?=$server_memory_total?>)<?php } ?></p>
					<p class="nr-num-data" title="Fullest Harddrive"><span><?= $server->summary->fullest_disk?></span> disk%</p>
				</div>
			</div>
			<?php } else if (!isset($server)) { ?>
				<div class="nr-app-summary nr-data-row">
					<div class="nr-cell nr-cell-6">
						<h3><?= lang('server')?></h3>
						<p><?= lang('no_server_found_for_app')?></p>
					</div>
				</div>
			<?php } else { ?>
				<p class="nr-error">Couldn't fetch server summary data</p>
			<?php } ?>
			<?php if (count($errors) != 0) { ?>
				<div class="error-messages">
					<?php foreach($errors as $error) { ?>
						<p><?=$error?></p>
					<?php } ?>
				</div>
			<?php } ?>
		<?php
			//If no $app is set
			} else { ?>
			<h3><?= lang('please_select_app')?></h3>
		<?php } ?>

		<?php if (isset($predefined_datasets)) { ?>
		<div class="chart-container">
			<h2>Charts</h2>
			<div class="form">
				<?=form_open($chart_form_action, 'class="chart-form"', '')?>
					<select class="" name="metric_simple_name">
						<?php
						$count = 0;
						$from_app = true;
						foreach ($predefined_datasets as $pred_name => $pred_dataset) {
							if ($count == 0)
							{
								echo '<optgroup label="App">';
								echo "\n";
							}
							if ($pred_dataset['source'] == 'server' && $from_app)
							{
								$from_app = false;
								echo '</optgroup>';
								echo "\n";
								echo '<optgroup label="Server">';
								echo "\n";
							}
							echo '<option value="'.$pred_name.'">'.$pred_dataset['title'].'</option>';
							echo "\n";
							$count++;
						}
						echo '</optgroup>';
						echo "\n";

						if (isset($user_predefined_datasets) && count($user_predefined_datasets) > 0)
						{
							echo '<optgroup label="Custom">';
							echo "\n";
							foreach ($user_predefined_datasets as $k => $dataset)
							{
								echo '<option value="custom_'.$k.'">'.$dataset['title'].'</option>';
							}
							echo '</optgroup>';
							echo "\n";
						}
						?>
					</select>
					
					<select class="" name="timerange">
						<?php
						foreach ($predefined_time_ranges as $time_range_sec => $time_range_details)
						{
							echo '<option value="'.$time_range_sec.'">'.$time_range_details['title'].'</option>';
							echo "\n";
						}
						?>
					</select>
					<input class="btn submit" value="Fetch" type="submit">
				<?=form_close()?>
			</div>
			<p id="nr-data-error" class="nr-error"></p>
			<div class="chart-canvas-container" style="">
				<canvas id="nr-chart" height="400" data-graph-dataset="cpu" ></canvas>
			</div>
		</div>
		<?php } //endif isset($predefined_datasets) ?>
	</div>
</div>
<script>


<?php if (isset($datasets)) {
	echo "var datasets = " . json_encode($datasets) . ";\n";
} ?>

</script>
