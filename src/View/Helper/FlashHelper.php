<?php

namespace LilHermit\Bootstrap4\View\Helper;

class FlashHelper extends \Cake\View\Helper\FlashHelper {

    /**
     * {@inheritDoc}
     */
    public function render(string $key = 'flash', array $options = []): ?string {

        $pluginOverrides = ['flash/default', 'flash/error', 'flash/info', 'flash/success', 'flash/warning'];

        $session = $this->_View->getRequest()->getSession();

        if (!$session->check("Flash.$key")) {
            return null;
        }

        $stack = $session->consume("Flash.$key");

        if (!is_array($stack)) {
            throw new \UnexpectedValueException(sprintf(
                'Value for flash setting key "%s" must be an array.',
                $key
            ));
        }

        foreach ($stack as &$item) {

            list($plugin, $element) = pluginSplit($item['element']);
            if ($plugin === null && in_array($item['element'], $pluginOverrides)) {
                $item['element'] = 'LilHermit/Bootstrap4.' . $element;
            }
        }
        $session->write("Flash.$key", $stack);

        return parent::render($key, $options);
    }
}
