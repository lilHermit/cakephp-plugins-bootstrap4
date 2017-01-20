<?php

namespace lilHermit\Bootstrap4\View\Helper;

class FlashHelper extends \Cake\View\Helper\FlashHelper {

    /**
     * {@inheritDoc}
     */
    public function render($key = 'flash', array $options = []) {

        if (!$this->request->session()->check("Flash.$key")) {
            return null;
        }

        $stack = $this->request->session()->consume("Flash.$key");

        if (!is_array($stack)) {
            throw new \UnexpectedValueException(sprintf(
                'Value for flash setting key "%s" must be an array.',
                $key
            ));
        }

        foreach ($stack as &$item) {
            if (strpos('lilHermit/Bootstrap4', $item['element']) === false) {
                $item['element'] = 'lilHermit/Bootstrap4.' . $item['element'];
            }
        }

        $this->request->session()->write("Flash.$key", $stack);

        return parent::render($key, $options);
    }
}