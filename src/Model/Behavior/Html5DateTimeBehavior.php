<?php

namespace LilHermit\Bootstrap4\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\Validation\RulesProvider;
use Cake\Validation\Validator;

class Html5DateTimeBehavior extends Behavior {

    public function buildValidator(Event $event, Validator $validator, $name) {

        if (method_exists($validator, 'setProvider')) {
            return $validator->setProvider('bootstrap4', new RulesProvider('LilHermit\Bootstrap4\Validation\Validation'));
        } else {
            /** @noinspection PhpDeprecationInspection */
            return $validator->provider('bootstrap4', new RulesProvider('LilHermit\Bootstrap4\Validation\Validation'));
        }
    }
}