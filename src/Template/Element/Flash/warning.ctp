<div class="alert alert-warning alert-dismissible fade in" role="alert">
    <?php
    if (!(isset($params['noDismiss']) && $params['noDismiss'] === true) ):?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    <?php endif; ?>
    <?= h($message) ?>
</div>
