<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?=form_open($action_url, '', $form_hidden)?>
<table class="mainTable padTable" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr class="even">
			<th style="width:50%;" class="">Preference</th><th>Setting</th>
		</tr>
	</thead>
	<tbody>
		<tr class="<?php echo alternator('even', 'odd');?>">
			<td>
				<strong><label for="custom_dataset_name"><?=lang('custom_dataset_name')?></label></strong>
				<div class="subtext"><?=lang('custom_dataset_name_desc')?></div>
			</td>
			<td>
				<input type="text" required="required" name="custom_dataset_name" id="custom_dataset_name" value="<?=$settings['custom_dataset_name']?>">&nbsp;
			</td>
		</tr>

		<tr class="<?php echo alternator('even', 'odd');?>">
			<td>
				<strong><label for="custom_dataset_type"><?=lang('custom_dataset_type')?></label></strong>
				<div class="subtext"><?=lang('custom_dataset_type_desc')?></div>
			</td>
			<td>
				<select name="metric_type" id="custom_dataset_type" required="required">
					<option value=""></option>
					<option value="app"><?=lang('app')?></option>
					<option value="server"><?=lang('server')?></option>
				</select>
			</td>
		</tr>

		<tr class="<?php echo alternator('even', 'odd');?> data-select app-data-select">
			<td>
				<strong><label for="custom_dataset_app_metric_name"><?=lang('app_metric_data')?></label></strong>
				<div class="subtext"><?=lang('app_metric_data_desc')?></div>
			</td>
			<td>
				<select name="custom_dataset_app_metric_name" id="custom_dataset_app_metric_name">
					<option value=""></option>
					<?php
						//print_r($metric_values_app);
						foreach ($metric_values_app as $idx => $value)
						{
							echo '<option value="'.$idx.'">';
							echo $idx;
							echo '</option>';
							echo "\n";
						}
					?>
				</select>
				<br/>
				<select name="custom_dataset_app_metric_value" id="custom_dataset_app_metric_value">
					<!-- Will be populated with JS -->
				</select>
			</td>
		</tr>

		<tr class="<?php echo alternator('even', 'odd');?> data-select server-data-select">
			<td>
				<strong><label for="custom_dataset_server_metric_name"><?=lang('server_metric_data')?></label></strong>
				<div class="subtext"><?=lang('server_metric_data_desc')?></div>
			</td>
			<td>
				<select name="custom_dataset_server_metric_name" id="custom_dataset_server_metric_name">
					<option value=""></option>
					<?php
						//print_r($metric_values_server);
						foreach ($metric_values_server as $idx => $value)
						{
							echo '<option value="'.$idx.'">';
							echo $idx;
							echo '</option>';
							echo "\n";
						}
					?>
				</select>
				<br/>
				<select name="custom_dataset_server_metric_value" id="custom_dataset_server_metric_value">
					<!-- Will be populated with JS -->
				</select>
			</td>
		</tr>
	</tbody>
</table>
<?=form_submit(array('name' => 'submit', 'value' => lang('save'), 'class' => 'submit'))?>
<?=form_close()?>

<div class="hidden">
	<script type="text/javascript">
		// data for each metric name
		var metric_values_app = <?php echo json_encode($metric_values_app); ?>;
		var metric_values_server = <?php echo json_encode($metric_values_server); ?>;

	</script>
</div>