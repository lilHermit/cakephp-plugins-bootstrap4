<?php

namespace lilHermit\Bootstrap4\Test\TestCase\View\Helper;

use Cake\Network\Request;
use lilHermit\Bootstrap4\View\Helper\HtmlHelper;


/**
 * HtmlHelperTest class
 */
class HtmlHelperTest extends \Cake\Test\TestCase\View\Helper\HtmlHelperTest {

    public function setUp() {
        parent::setUp();

        // Switch the HtmlHelper to Plugin version
        $this->Html = new HtmlHelper($this->View);
        $this->Html->request = new Request();
        $this->Html->request->webroot = '';
        $this->Html->Url->request = $this->Html->request;
    }

    public function testGetCrumbFirstLink() {
        $result = $this->Html->getCrumbList([], 'Home');
        $expected = [
            'ol' => ['class' => 'breadcrumb'],
            ['li' => ['class' => 'breadcrumb-item first']],
            ['a' => ['href' => '/']], 'Home', '/a',
            '/li',
            '/ol'
        ];

        $this->assertHtml($expected, $result);

        $this->Html->addCrumb('First', '#first');
        $this->Html->addCrumb('Second', '#second');

        $result = $this->Html->getCrumbs(' - ', ['url' => '/home', 'text' => '<img src="/home.png" />', 'escape' => false]);
        $expected = [
            ['a' => ['href' => '/home']],
            'img' => ['src' => '/home.png'],
            '/a',
            ' - ',
            ['a' => ['href' => '#first']],
            'First',
            '/a',
            ' - ',
            ['a' => ['href' => '#second']],
            'Second',
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    public function testCrumbList() {

        $this->assertNull($this->Html->getCrumbList());

        $this->Html->addCrumb('Home', '/', ['class' => 'home']);
        $this->Html->addCrumb('Some page', '/some_page');
        $this->Html->addCrumb('Another page');
        $result = $this->Html->getCrumbList(['class' => 'breadcrumbs']);

        $expected = [
            ['ol' => ['class' => 'breadcrumbs']],
            ['li' => ['class' => 'breadcrumb-item first']],
            ['a' => ['class' => 'home', 'href' => '/']], 'Home', '/a',
            '/li',
            'li' => ['class' => 'breadcrumb-item'],
            ['a' => ['href' => '/some_page']], 'Some page', '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item active last']],
            'Another page',
            '/li',
            '/ol'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testCrumbListFirstLink() {

        $this->Html->addCrumb('First', '#first');
        $this->Html->addCrumb('Second', '#second');

        $result = $this->Html->getCrumbList([], 'Home');

        $expected = [
            'ol' => ['class' => 'breadcrumb'],
            ['li' => ['class' => 'breadcrumb-item first']],
            ['a' => ['href' => '/']], 'Home', '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item']],
            ['a' => ['href' => '#first']], 'First', '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item last']],
            ['a' => ['href' => '#second']], 'Second', '/a',
            '/li',
            '/ol'
        ];
        $this->assertHtml($expected, $result);
        $result = $this->Html->getCrumbList([], ['url' => '/home', 'text' => '<img src="/home.png" />', 'escape' => false]);
        $expected = [
            'ol' => ['class' => 'breadcrumb'],
            ['li' => ['class' => 'breadcrumb-item first']],
            ['a' => ['href' => '/home']], 'img' => ['src' => '/home.png'], '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item']],
            ['a' => ['href' => '#first']], 'First', '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item last']],
            ['a' => ['href' => '#second']], 'Second', '/a',
            '/li',
            '/ol'
        ];
        $this->assertHtml($expected, $result);
    }

    public function testCrumbListBootstrapStyle() {
        $this->markTestSkipped('Default style is bootstrap');
    }

    public function testCrumbListZurbStyle() {
        $this->Html->addCrumb('Home', '#');
        $this->Html->addCrumb('Features', '#');
        $this->Html->addCrumb('Gene Splicing', '#');
        $this->Html->addCrumb('Home', '#');
        $result = $this->Html->getCrumbList(
            ['class' => 'breadcrumbs', 'firstClass' => false, 'lastClass' => 'current', 'itemClass' => false]
        );

        $expected = [
            ['ol' => ['class' => 'breadcrumbs']],
            '<li',
            ['a' => ['href' => '#']], 'Home', '/a',
            '/li',
            '<li',
            ['a' => ['href' => '#']], 'Features', '/a',
            '/li',
            '<li',
            ['a' => ['href' => '#']], 'Gene Splicing', '/a',
            '/li',
            ['li' => ['class' => 'current']],
            ['a' => ['href' => '#']], 'Home', '/a',
            '/li',
            '/ol'
        ];
        $this->assertHtml($expected, $result);
    }
}
