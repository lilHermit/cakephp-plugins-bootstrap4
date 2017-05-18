<?php

namespace LilHermit\Bootstrap4\Test\TestCase\View\Helper;

use Cake\Network\Request;
use LilHermit\Bootstrap4\Configure\Assets;
use LilHermit\Bootstrap4\View\Helper\HtmlHelper;


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
        $this->assertTrue(true);
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
                'style' => 'width:90.00%',
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
                'style' => 'width:100.00%',
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
                'style' => 'width:0.00%',
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
                'style' => 'width:51.00%',
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
                'style' => 'width:0.00%',
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
                'style' => 'width:50.00%',
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
                'style' => 'width:75.00%',
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
                'style' => 'width:0.00%',
                'aria-valuenow' => 0,
                'aria-valuemin' => 0,
                'aria-valuemax' => 1
            ]],
            '/div',
            '/div'
        ], $result);

        // Test multiple values and make sure `max` in values doesn't override global `max
        $result = $this->Html->progress([
            ['value' => 75],
            ['value' => 50, 'class' => 'bg-danger'],
            ['value' => 75, 'max' => 100]
        ],
            ['max' => 200]
        );
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:37.50%',
                'aria-valuenow' => 75,
                'aria-valuemin' => 0,
                'aria-valuemax' => 200
            ]],
            '/div',
            ['div' => [
                'class' => 'bg-danger progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 200
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:37.50%',
                'aria-valuenow' => 75,
                'aria-valuemin' => 0,
                'aria-valuemax' => 200
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
                'style' => 'width:50.00%',
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
                'style' => 'width:50.00%',
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
     * Tests the adding of striped option including the animated option, plus overriding on multiple
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
                'style' => 'width:50.00%',
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
                'style' => 'width:50.00%',
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
                'style' => 'width:50.00%',
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
                'style' => 'width:50.00%',
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
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        // Multiple values
        $result = $this->Html->progress([
            [
                'value' => 65,
                'animatedStripes' => true
            ],
            [
                'value' => 35
            ]
        ],
            ['striped' => true]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar progress-bar-striped progress-bar-animated',
                'role' => 'progressbar',
                'style' => 'width:65.00%',
                'aria-valuenow' => 65,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar progress-bar-striped',
                'role' => 'progressbar',
                'style' => 'width:35.00%',
                'aria-valuenow' => 35,
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
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '50.0%',
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress(50, ['label' => 'Half way']);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
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
                'style' => 'width:50.00%',
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
                'style' => 'width:50.00%',
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

    /**
     * testProgressMultipleRendering method
     *
     * Tests rendering of multiple progress-bars
     *
     * @return void
     */
    public function testProgressMultipleRendering() {


        $result = $this->Html->progress([50, 25, 25]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        // Values exceeding max
        $result = $this->Html->progress([50, 25, 35]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress([['value' => 50], ['value' => 25], ['value' => 25]]);
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testProgressMultipleWithLabelRendering method
     *
     * Tests rendering of multiple progress-bars with label, also makes sure that `options['label']`
     * overrides
     *
     * @return void
     */
    public function testProgressMultipleWithLabelRendering() {

        $result = $this->Html->progress([
                ['value' => 50],
                ['value' => 25, 'label' => 'quarter'],
                ['value' => 25]]
        );
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            'quarter',
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress([
            ['value' => 50],
            ['value' => 25, 'label' => 'quarter'],
            ['value' => 25]],
            [
                'label' => true
            ]
        );
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '50.0%',
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            'quarter',
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '25.0%',
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress([
            ['value' => 50],
            ['value' => 25, 'label' => false],
            ['value' => 25]],
            [
                'label' => true
            ]
        );
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '50.0%',
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '25.0%',
            '/div',
            '/div'
        ], $result);
    }

    /**
     * testProgressMultipleWithAttributesRendering method
     *
     * Tests rendering of multiple progress-bars with attributes are rendered correctly
     * overrides
     *
     * @return void
     */
    public function testProgressMultipleWithAttributesRendering() {

        $result = $this->Html->progress([
                ['value' => 50],
                ['value' => 25, 'class' => 'bg-success'],
                ['value' => 25, 'class' => 'bg-warning']
            ]
        );
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'bg-success progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'bg-warning progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);

        $result = $this->Html->progress([
                ['value' => 50],
                ['value' => 25, 'class' => 'bg-success'],
                ['value' => 25, 'id' => 'stage3']
            ]
        );
        $this->assertHtml([
            ['div' => ['class' => 'progress']],
            ['div' => [
                'class' => 'progress-bar',
                'role' => 'progressbar',
                'style' => 'width:50.00%',
                'aria-valuenow' => 50,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'bg-success progress-bar',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            ['div' => [
                'class' => 'progress-bar',
                'id' => 'stage3',
                'role' => 'progressbar',
                'style' => 'width:25.00%',
                'aria-valuenow' => 25,
                'aria-valuemin' => 0,
                'aria-valuemax' => 100
            ]],
            '/div',
            '/div'
        ], $result);
    }

    public function testBootstrapCssMethod() {

        $versions = Assets::css();
        $latestVersion = array_pop($versions);

        // Latest version
        $result = $this->Html->bootstrapCss();
        $this->assertHtml([
            'link' => [
                'rel' => 'stylesheet',
                'href' => $latestVersion['href'],
                'integrity' => $latestVersion['integrity'],
                'crossorigin' => 'anonymous'
            ]
        ], $result);

        // Specific version
        $result = $this->Html->bootstrapCss('4.0.0-alpha.5');
        $this->assertHtml([
            'link' => [
                'rel' => 'stylesheet',
                'href' => $versions['4.0.0-alpha.5']['href'],
                'integrity' => $versions['4.0.0-alpha.5']['integrity'],
                'crossorigin' => 'anonymous'
            ]
        ], $result);

        // Custom array
        $result = $this->Html->bootstrapCss([
            'href' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css',
            'integrity' => 'sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY'
        ]);
        $this->assertHtml([
            'link' => [
                'rel' => 'stylesheet',
                'href' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css',
                'integrity' => 'sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY',
                'crossorigin' => 'anonymous'
            ]
        ], $result);
    }

    public function testBootstrapScriptMethodVersions() {
        $versions = Assets::javascript();
        $latestVersion = array_slice($versions, -1, 1, true);
        $latestVersionNumber = key($latestVersion);
        $latestVersion = $latestVersion[$latestVersionNumber];

        $tetherVersions = Assets::tetherJavascript();
        $tetherVersion = $tetherVersions[$latestVersionNumber];

        // Latest version
        $result = $this->Html->bootstrapScript();
        $this->assertHtml([
            ['script' => [
                'src' => $tetherVersion['src'],
                'integrity' => $tetherVersion['integrity'],
                'crossorigin' => 'anonymous'

            ]],
            '/script',
            ['script' => [
                'src' => $latestVersion['src'],
                'integrity' => $latestVersion['integrity'],
                'crossorigin' => 'anonymous'

            ]],
            '/script'
        ], $result);

        // Specific version
        $result = $this->Html->bootstrapScript(['version' => '4.0.0-alpha.5']);
        $tetherVersion = $tetherVersions['4.0.0-alpha.5'];
        $this->assertHtml([
            ['script' => [
                'src' => $tetherVersion['src'],
                'integrity' => $tetherVersion['integrity'],
                'crossorigin' => 'anonymous'

            ]],
            '/script',
            ['script' => [
                'src' => $versions['4.0.0-alpha.5']['src'],
                'integrity' => $versions['4.0.0-alpha.5']['integrity'],
                'crossorigin' => 'anonymous'

            ]],
            '/script'
        ], $result);

        // Custom array
        $result = $this->Html->bootstrapScript([
            'src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js',
            'integrity' => 'sha384-ux8v3A6CPtOTqOzMKiuo3d/DomGaaClxFYdCu2HPMBEkf6x2xiDyJ7gkXU0MWwaD'
        ]);
        $this->assertHtml([
            'script' => [
                'src' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js',
                'integrity' => 'sha384-ux8v3A6CPtOTqOzMKiuo3d/DomGaaClxFYdCu2HPMBEkf6x2xiDyJ7gkXU0MWwaD',
                'crossorigin' => 'anonymous'
            ],
            '/script'
        ], $result);
    }

    public function testBootstrapScriptMethodOptions() {
        $versions = Assets::javascript();
        $latestVersion = array_slice($versions, -1, 1, true);
        $latestVersionNumber = key($latestVersion);
        $latestVersion = $latestVersion[$latestVersionNumber];

        $tetherVersions = Assets::tetherJavascript();
        $tetherVersion = $tetherVersions[$latestVersionNumber];

        // Latest version with own script
        $result = $this->Html->bootstrapScript(['own' => true]);
        $this->assertHtml([
            ['script' => ['src' => 'js/LilHermit/Bootstrap4.form-manipulation.js']],
            '/script',
            ['script' => [
                'src' => $tetherVersion['src'],
                'integrity' => $tetherVersion['integrity'],
                'crossorigin' => 'anonymous'

            ]],
            '/script',
            ['script' => [
                'src' => $latestVersion['src'],
                'integrity' => $latestVersion['integrity'],
                'crossorigin' => 'anonymous'
            ]],
            '/script'
        ], $result);

        // Reset the view because it won't include the plugin script multiple times
        $this->setUp();

        // Specific version with own script
        $result = $this->Html->bootstrapScript(['version' => '4.0.0-alpha.5', 'own' => true]);
        $tetherVersion = $tetherVersions['4.0.0-alpha.5'];
        $this->assertHtml([
            ['script' => ['src' => 'js/LilHermit/Bootstrap4.form-manipulation.js']],
            '/script',
            ['script' => [
                'src' => $tetherVersion['src'],
                'integrity' => $tetherVersion['integrity'],
                'crossorigin' => 'anonymous'

            ]],
            '/script',
            'script' => [
                'src' => $versions['4.0.0-alpha.5']['src'],
                'integrity' => $versions['4.0.0-alpha.5']['integrity'],
                'crossorigin' => 'anonymous'
            ]
        ], $result);

        // Reset the view because it won't include the plugin script multiple times
        $this->setUp();

        // Latest version with own script but without tether
        $result = $this->Html->bootstrapScript(['own' => true, 'tether' => false]);
        $this->assertHtml([
            ['script' => ['src' => 'js/LilHermit/Bootstrap4.form-manipulation.js']],
            '/script',
            ['script' => [
                'src' => $latestVersion['src'],
                'integrity' => $latestVersion['integrity'],
                'crossorigin' => 'anonymous'
            ]],
            '/script'
        ], $result);
    }
}
