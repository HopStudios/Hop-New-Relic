<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="box">
	<div class="tbl-ctrls">
		<h1><?=lang('settings')?></h1>
		<?= ee('CP/Alert')->getAllInlines() ?>
		<?=form_open($action_url, '', $form_hidden)?>
		<fieldset class="col-group required <?php if (isset($form_error_email_address_sender)) echo 'invalid';?>">
			<div class="setting-txt col w-8">
				<h3><?= lang('new_relic_api_key')?></h3>
				<em><?= sprintf(lang('to_get_api_key_instructions'), 'https://docs.newrelic.com/docs/apis/rest-api-v2/requirements/api-key#creating')?></em>
			</div>
			<div class="setting-field col w-8 last">
				<input type="text" name="nr_api_key" id="nr_api_key" value="<?=$settings['nr_api_key']?>">
				<?php if (isset($form_error_email_address_sender)) $form_error_email_address_sender;?>
			</div>
		</fieldset>
		
		<fieldset class="col-group required <?php if (isset($form_error_email_address_sender)) echo 'invalid';?>">
			<div class="setting-txt col w-8">
				<h3><?= lang('new_relic_app')?></h3>
				<em><?= sprintf(lang('choose_app_instructions'), $refresh_app_list_link)?></em>
			</div>
			<div class="setting-field col w-8 last">
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
								echo ' ('.lang('active').')';
							}
							else if ($app->health_status == "orange")
							{
								echo ' ('.lang('not_healthy').')';
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
			</div>
		</fieldset>

		<fieldset class="col-group">
			<div class="setting-txt col w-8">
				<h3><?= lang('new_relic_server')?></h3>
				<em><?= sprintf(lang('choose_server_instructions'), $refresh_server_list_link)?></em>
			</div>
			<div class="setting-field col w-8 last">
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
			</div>
		</fieldset>
		
		<fieldset class="form-ctrls">
			<?=form_submit(array('name' => 'submit', 'value' => lang('save'), 'class' => 'btn submit'))?>
		</fieldset>
		<?=form_close()?>
	</div>
</div>
