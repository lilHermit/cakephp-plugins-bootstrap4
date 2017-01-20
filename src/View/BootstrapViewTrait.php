<?php

namespace lilHermit\Bootstrap4\View;

trait BootstrapViewTrait {

    public function initializeBootstrap(array $options = []) {
        $this->loadHelper('Html', ['className' => 'lilHermit/Bootstrap4.Html']);
        $this->loadHelper('Flash', ['className' => 'lilHermit/Bootstrap4.Flash']);
        $this->loadHelper('Form', ['className' => 'lilHermit/Bootstrap4.Form']);
        $this->loadHelper('Paginator', ['className' => 'lilHermit/Bootstrap4.Paginator']);
    }
}