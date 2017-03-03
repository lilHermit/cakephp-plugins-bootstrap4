<?php

namespace lilHermit\Bootstrap4\View;

use Cake\View\View;

/**
 * BootstrapView: A customised version of View class for Bootstrap.
 *
 * It loads our Bootstrap helpers.
 *
 * @property \lilHermit\Bootstrap4\View\Helper\FormHelper      $Form
 * @property \lilHermit\Bootstrap4\View\Helper\HtmlHelper      $Html
 * @property \lilHermit\Bootstrap4\View\Helper\FlashHelper     $Flash
 * @property \lilHermit\Bootstrap4\View\Helper\PaginatorHelper $Paginator
 */
class BootstrapView extends View {

    use BootstrapViewTrait;

    public function initialize() {
        parent::initialize();

        $this->initializeBootstrap();
    }
}