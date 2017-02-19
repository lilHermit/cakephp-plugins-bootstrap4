<?php
use lilHermit\Toolkit\Utility\Html;

if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}

$class = ['alert', 'alert-danger'];
if (!(isset($params['noDismiss']) && $params['noDismiss'] === true)) {
    $class = Html::addClass($class, ['alert-dismissible', 'fade', 'show'], ['useIndex' => false]);
}
?>
<div class="<?= implode(' ', $class) ?>" role="alert">
    <?php
    if (!(isset($params['noDismiss']) && $params['noDismiss'] === true)):?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    <?php endif; ?>
    <?= $message ?>
</div>