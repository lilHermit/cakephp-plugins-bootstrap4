<?php

namespace lilHermit\Bootstrap4\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\Validation\RulesProvider;
use Cake\Validation\Validator;

class Html5DateTimeBehavior extends Behavior {

    public function buildValidator(Event $event, Validator $validator, $name) {
        return $validator->provider('bootstrap4', new RulesProvider('lilHermit\Bootstrap4\Validation\Validation'));
    }
}