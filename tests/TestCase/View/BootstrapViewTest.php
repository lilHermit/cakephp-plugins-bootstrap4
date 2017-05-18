<?php
namespace LilHermit\Bootstrap4\Test\TestCase\View;

class ViewTest extends \Cake\Test\TestCase\View\ViewTest {

    public function setUp()
    {
        parent::setUp();

        $this->View = $this->PostsController->createView('TestApp\View\AppView');
        $this->View->viewPath = 'Posts';

        $this->ThemeView = $this->ThemePostsController->createView('TestApp\View\AppView');
        $this->ThemeView->viewPath = 'Posts';
    }

}