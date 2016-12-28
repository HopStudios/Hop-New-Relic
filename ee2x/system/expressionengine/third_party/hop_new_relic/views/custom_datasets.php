<p>
	<a class="submit" href="<?=$new_url?>">Create new dataset</a>
</p>

<?=form_open($action_url, '', $form_hidden)?>

<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		lang('id'),
		lang('name'),
		lang('metric_name'),
		lang('metric_value'),
		form_checkbox('select_all', 'true', FALSE, 'class="toggle_all" id="select_all"')
	);

	foreach ($user_datasets as $key => $user_dataset)
	{
		$this->table->add_row(
			$key,
			$user_dataset['title'],
			$user_dataset['names'][0],
			$user_dataset['values'][0],
			form_checkbox('datasets[]', $key)
		);
	}

echo $this->table->generate();

?>

<div class="tableFooter">
	<div class="tableSubmit">
		<?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit')).NBS.NBS.form_dropdown('action', $options)?>
	</div>
</div>

<?=form_close()?>