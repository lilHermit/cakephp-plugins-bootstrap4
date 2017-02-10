<?php

namespace lilHermit\Bootstrap4\Test\TestCase\View\Helper;

use Cake\Network\Request;
use lilHermit\Bootstrap4\View\Helper\HtmlHelper;


/**
 * HtmlHelperTest class
 *
 * @property HtmlHelper $Html
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
        // NOOP
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

    public function testBootstrapButtonTypes() {

        $result = $this->Html->button('test');
        $this->assertHtml([
            ['a' => ['href' => '/test', 'class' => 'btn btn-primary', 'role' => 'button']], 'preg:/\/test/', '/a'
        ], $result);

        $result = $this->Html->button('test', '#', ['type' => 'link']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary', 'role' => 'button']], 'test', '/a'
        ], $result);

        $result = $this->Html->button('test', null, ['type' => 'button']);
        $this->assertHtml([
            ['button' => ['class' => 'btn btn-primary', 'type' => 'button']], 'test', '/button'
        ], $result);

        $result = $this->Html->button('test', null, ['type' => 'submit']);
        $this->assertHtml([
            ['button' => ['class' => 'btn btn-primary', 'type' => 'submit']], 'test', '/button'
        ], $result);

        $result = $this->Html->button('test', null, ['type' => 'reset']);
        $this->assertHtml([
            ['button' => ['class' => 'btn btn-primary', 'type' => 'reset']], 'test', '/button'
        ], $result);
    }

    public function testBootstrapButtonSizes() {

        $result = $this->Html->button('text', '#', ['size' => 'normal']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['size' => 'small']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary btn-sm', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['size' => 'sm']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary btn-sm', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['size' => 'large']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary btn-lg', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['size' => 'lg']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary btn-lg', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['size' => 'invalid-size']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);
    }

    public function testBootstrapButtonCss() {

        $result = $this->Html->button('text', '#', ['class' => 'blue']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn blue btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['class' => ['blue', 'my-3']]);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'blue my-3 btn btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);
    }

    public function testBootstrapButtonOutlinePrimarySecondary() {

        $result = $this->Html->button('text', '#', ['outline' => false]);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['outline' => 'invalid']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['outline' => true]);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-outline-primary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['secondary' => false]);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['secondary' => 'invalid']);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-primary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['secondary' => true]);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-secondary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['secondary' => true, 'outline' => true]);
        $this->assertHtml([
            ['a' => ['href' => '#', 'class' => 'btn btn-outline-secondary', 'role' => 'button']], 'text', '/a'
        ], $result);

        $result = $this->Html->button('text', '#', ['secondary' => true, 'outline' => true, 'type' => 'button']);
        $this->assertHtml([
            ['button' => ['type' => 'button', 'class' => 'btn btn-outline-secondary']], 'text', '/button'
        ], $result);
    }

    /**
     * testProgressBasic method
     *
     * Tests basic value (include being greater than max and less than min)
     *
     * @return void
     */
    public function testProgressBasic() {

        $result = $this->Html->progress(90);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:90%',
                'aria-valuenow' => 90,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(101);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:100%',
                'aria-valuenow' => 100,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(-101);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:0%',
                'aria-valuenow' => 0,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50.555);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:51%',
                'aria-valuenow' => 51,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress('ss');
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:0%',
                'aria-valuenow' => 0,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testProgressMaxValue method
     *
     * Tests max value
     *
     * @return void
     */
    public function testProgressMaxValue() {

        $result = $this->Html->progress(5, ['max' => 10]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 5,
                'aria-valuemin' => 0,
                'aria-valuemax' => 10
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(150, ['max' => 200]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:75%',
                'aria-valuenow' => 150,
                'aria-valuemin' => 0,
                'aria-valuemax' => 200
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(0, ['max' => 1]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:0%',
                'aria-valuenow' => 0,
                'aria-valuemin' => 0,
                'aria-valuemax' => 1
            ]],
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testProgressAttributes method
     *
     * Tests custom attributes including merge of `class`
     *
     * @return void
     */
    public function testProgressAttributes() {

        $result = $this->Html->progress(50, ['class' => 'myclass']);
        $this->assertHtml([
            ['div' => ['class' => 'myclass progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['class' => 'myclass', 'data-ref' => 'my-progress']);
        $this->assertHtml([
            ['div' => ['class' => 'myclass progress', 'data-ref' => 'my-progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testProgressStripedOption method
     *
     * Tests the adding of striped option including the animated option
     *
     * @return void
     */
    public function testProgressStripedOption() {

        $result = $this->Html->progress(50, ['striped' => true]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar progress-bar-striped',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['striped' => true, 'animatedStripes' => true]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar progress-bar-striped progress-bar-animated',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['striped' => false, 'animatedStripes' => true]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar progress-bar-striped progress-bar-animated',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['animatedStripes' => true]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar progress-bar-striped progress-bar-animated',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['animatedStripes' => false]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

    }

    /**
     * testProgressLabelOption method
     *
     * Tests the adding of label option
     *
     * @return void
     */
    public function testProgressLabelOption() {

        $result = $this->Html->progress(50, ['label' => true]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '50%',
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['label' => 'Half way']);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            'Half way',
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['label' => '<b>Half way</b>']);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '&lt;b&gt;Half way&lt;/b&gt;',
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['label' => '<b>Half way</b>', 'escape' => false]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '<b',
            'Half way',
            '/b',
            '/div',
            '/div'
        ], $result);
    }
}
