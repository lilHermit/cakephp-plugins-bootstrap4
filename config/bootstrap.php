<?php
use Cake\Core\Configure;
use Cake\Database\Type;

// Allow disabling of html5-datetime-type
if (Configure::read('lilHermit-plugin-bootstrap4.disable-html5-datetime-type') !== true) {
    $immutable = Type::build('datetime')->getDateTimeClassName() === 'Cake\I18n\FrozenTime';
    Type::map('datetime', 'LilHermit\Bootstrap4\Database\Type\Html5DateTimeType');
    if ($immutable) {
        Type::build('datetime')->useImmutable();
    }
}