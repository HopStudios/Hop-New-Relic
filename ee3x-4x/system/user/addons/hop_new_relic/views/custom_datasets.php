<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?=form_open($form_url)?>
<?php $this->embed('ee:_shared/table', $table); ?>

<fieldset class="tbl-bulk-act hidden">
  <select>
    <option>-- <?=lang('with_selected')?> --</option>
    <option value="remove" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('remove')?></option>
  </select>
  <input class="btn submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
</fieldset>
<?=form_close()?>