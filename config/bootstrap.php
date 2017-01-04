<?php
use Cake\Database\Type;
use Cake\Core\Configure;

// Allow skipping of html5-datetime-type
if (Configure::read('lilHermit-plugin-bootstrap4.skip-html5-datetime-type') !== true){
    Type::map('datetime', 'lilHermit\Bootstrap4\Database\Type\Html5DateTimeType');
}