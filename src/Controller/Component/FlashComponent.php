<?php
namespace lilHermit\Bootstrap4\Controller\Component;

/**
 * Class FlashComponent
 *
 * use it in your AppController initialize method with:-
 *      $this->loadComponent('Bootstrap4.Flash');
 *
 * @method void info(string $message, array $options = []) Set a message using "info" element
 * @method void warning(string $message, array $options = []) Set a message using "warning" element
 */

class FlashComponent extends \Cake\Controller\Component\FlashComponent {

    public function __call($name, $args) {
        if (!isset($args[1]['plugin'])) {
            $args[1]['plugin'] = 'lilHermit/Bootstrap4';
        }
        parent::__call($name, $args);
    }
}

