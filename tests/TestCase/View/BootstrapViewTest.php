<?php

namespace LilHermit\Bootstrap4\Test\TestCase\View;

use Cake\TestSuite\TestCase;
use LilHermit\Bootstrap4\View\BootstrapView;

class BootstrapViewTest extends TestCase {

    /**
     * Test the correct (Bootstrap) Helpers are loaded
     *
     * @return void
     */
    public function testBootstrapHelpersLoaded() {
        $View = new BootstrapView();

        $this->assertInstanceOf('LilHermit\Bootstrap4\View\Helper\HtmlHelper', $View->Html);
        $this->assertInstanceOf('LilHermit\Bootstrap4\View\Helper\FlashHelper', $View->Flash);
        $this->assertInstanceOf('LilHermit\Bootstrap4\View\Helper\FormHelper', $View->Form);
        $this->assertInstanceOf('LilHermit\Bootstrap4\View\Helper\PaginatorHelper', $View->Paginator);
    }
}