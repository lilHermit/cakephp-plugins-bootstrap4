<?php

namespace LilHermit\Bootstrap4\View;

use Cake\View\View;

/**
 * BootstrapView: A customised version of View class for Bootstrap.
 *
 * It loads our Bootstrap helpers.
 *
 * @property \LilHermit\Bootstrap4\View\Helper\FormHelper      $Form
 * @property \LilHermit\Bootstrap4\View\Helper\HtmlHelper      $Html
 * @property \LilHermit\Bootstrap4\View\Helper\FlashHelper     $Flash
 * @property \LilHermit\Bootstrap4\View\Helper\PaginatorHelper $Paginator
 */
class BootstrapView extends View {

    use BootstrapViewTrait;

    public function initialize(): void  {
        parent::initialize();

        $this->initializeBootstrap();
    }
}
