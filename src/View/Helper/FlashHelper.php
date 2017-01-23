<?php

namespace lilHermit\Bootstrap4\View\Helper;

class FlashHelper extends \Cake\View\Helper\FlashHelper {

    /**
     * {@inheritDoc}
     */
    public function render($key = 'flash', array $options = []) {

        $pluginOverrides = ['Flash/default', 'Flash/error', 'Flash/info', 'Flash/success', 'Flash/warning'];

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

            list($plugin, $element) = pluginSplit($item['element']);
            if ($plugin === null && in_array($item['element'], $pluginOverrides)) {
                $item['element'] = 'lilHermit/Bootstrap4.' . $element;
            }
        }
        $this->request->session()->write("Flash.$key", $stack);

        return parent::render($key, $options);
    }
}