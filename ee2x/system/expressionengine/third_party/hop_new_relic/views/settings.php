<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?=form_open($action_url, '', $form_hidden)?>
<table class="mainTable padTable" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr class="even">
			<th style="width:50%;" class=""><?=lang('preference');?></th><th><?=lang('setting');?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="<?php echo alternator('even', 'odd');?>">
			<td>
				<strong><label for="nr_api_key">New Relic API Key</label></strong><div class="subtext">To get that API key, follow <a target="_blank" href="https://docs.newrelic.com/docs/apis/rest-api-v2/requirements/api-key#creating">instructions on New Relic website</a></div>
			</td>
			<td>
				<input type="text" name="nr_api_key" id="nr_api_key" value="<?=$settings['nr_api_key']?>">&nbsp;
				<?php if (isset($form_error_api_key)) echo $form_error_api_key;?>
			</td>
		</tr>
		<tr class="<?php echo alternator('even', 'odd');?>">
			<td>
				<strong><label for="nr_selected_app">New Relic App</label></strong>
				<div class="subtext">Choose the app corresponding to this EE install. <a href="<?=$refresh_app_list_link;?>">Refresh app list</a></div>
			</td>
			<td>
				<select name="nr_selected_app_id" id="nr_selected_app_id">
					<?php
						$apps_list = unserialize($settings['nr_apps_list']);
						foreach ($apps_list as $app)
						{
							echo '<option value="'.$app->id.'"';
							if (isset($nr_selected_app_id) && $app->id == $nr_selected_app_id)
							{
								echo 'selected';
							}
							echo'>'.$app->name;
							if ($app->health_status == "green")
							{
								echo ' (active)';
							}
							else if ($app->health_status == "orange")
							{
								echo ' (not healthy)';
							}
							else
							{
								echo ' (inactive)';
							}
							echo '</option>';
						}
					?>
				</select>
				<?php if (isset($form_error_selected_app)) echo $form_error_selected_app;?>
			</td>
		</tr>
		<tr class="<?php echo alternator('even', 'odd');?>">
			<td>
				<strong><label for="nr_selected_app_selected_server_id"><?= lang('new_relic_server')?></label></strong>
				<div class="subtext"><?= sprintf(lang('choose_server_instructions'), $refresh_server_list_link)?></div>
			</td>
			<td>
				<select name="nr_selected_app_selected_server_id" id="nr_selected_app_selected_server_id">
					<?php
						$servers_list = unserialize($settings['nr_selected_app_servers']);
						foreach ($servers_list as $server)
						{
							echo '<option value="'.$server->id.'"';
							if ( isset($nr_selected_app_selected_server_id) 
								&& $server->id == $nr_selected_app_selected_server_id )
							{
								echo 'selected';
							}
							echo'>'.$server->name;
							if ($server->reporting)
							{
								echo ' ('.lang('active').')';
							}
							else
							{
								echo ' ('.lang('inactive').')';
							}
							echo '</option>';
						}
					?>
				</select>
				<?php if (isset($form_error_selected_app)) echo $form_error_selected_app;?>
			</td>
		</tr>
	</tbody>
</table>
<?=form_submit(array('name' => 'submit', 'value' => lang('save'), 'class' => 'submit'))?>
<?=form_close()?>
