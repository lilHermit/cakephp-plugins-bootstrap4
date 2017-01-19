<?php
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php
    if (!(isset($params['noDismiss']) && $params['noDismiss'] === true) ):?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <?php endif; ?>
    <?= h($message) ?>
</div>