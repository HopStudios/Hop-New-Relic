<?php
// This view is used to display data inside New RelEEk Accessory
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>

<?php if($app != null) { ?>
<div class="nr-app-summary nr-data-row">
    <div class="nr-cell nr-cell-6">
        <h3>App</h3>
        <?php if (isset($app->application_summary)) { ?>
        <p class="nr-num-data"><span><?= $app->application_summary->response_time?></span> ms</p>
        <p class="nr-num-data"><span><?= $app->application_summary->throughput?></span> rpm</p>
        <p class="nr-num-data"><span><?= $app->application_summary->error_rate?></span> err%</p>
        <p class="nr-num-data">apdex <span><?= $app->application_summary->apdex_score?></span></p>
        <?php } else { ?>
        <p>No application summary data received from New Relic</p>
        <?php } ?>
    </div>
    <div class="nr-cell nr-cell-6">
        <h3>End-User</h3>
        <?php if (isset($app->end_user_summary)) { ?>
        <p class="nr-num-data"><span><?= $app->end_user_summary->response_time?></span> ms</p>
        <p class="nr-num-data"><span><?= $app->end_user_summary->throughput?></span> rpm</p>
        <p class="nr-num-data">apdex <span><?= $app->end_user_summary->apdex_score?></span></p>
        <?php } else { ?>
        <p>No End-User data received from New Relic</p>
        <?php } ?>
    </div>
</div>
<?php } else { ?>
<div class="nr-app-summary nr-data-row">
    <p class="nr-error">Error when loading app summary data</p>
</div>
<?php } ?>

<?php if($server != null && !is_int($server) ) { ?>
<div class="nr-app-summary nr-data-row">
    <div class="nr-cell nr-cell-6">
        <h3>Server</h3>
        <p class="nr-num-data"><span><?= $server->summary->cpu?></span> cpu%<?php if($server->summary->cpu_stolen > 0){ ?> (<?= $server->summary->cpu_stolen?> cpu stolen%)<?php } ?></p>
        <p class="nr-num-data"><span><?= $server->summary->memory?></span> mem% <?php if(isset($server_memory_used) && isset($server_memory_total)) {?> (<?=$server_memory_used?> / <?=$server_memory_total?>)<?php } ?></p>
        <p class="nr-num-data" title="Fullest Harddrive"><span><?= $server->summary->fullest_disk?></span> disk%</p>
    </div>
</div>
<?php } else if ($server == -1) { ?>
    <div class="nr-app-summary nr-data-row">
        <h3>Server</h3>
        <p>No server found for this application.</p>
    </div>
<?php } else { ?>
<div class="nr-app-summary nr-data-row">
    <p class="nr-error">Error when loading server summary data</p>
</div>
<?php } ?>
