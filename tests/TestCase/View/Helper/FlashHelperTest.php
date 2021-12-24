<?php


namespace LilHermit\Bootstrap4\Test\TestCase\View\Helper;


use Cake\Core\Plugin;
use Cake\Core\PluginCollection;
use Cake\Http\BaseApplication;
use Cake\Http\ServerRequest;
use LilHermit\Bootstrap4\View\BootstrapView;
use LilHermit\Bootstrap4\View\Helper\FlashHelper;
use TestApp\Application;


/**
 * @property BootstrapView $View
 */
class FlashHelperTest extends \Cake\Test\TestCase\View\Helper\FlashHelperTest {

    /**
     * @var BaseApplication $app
     */
    protected $app;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp() : void {
        parent::setUp();

        // Save the session with all the flashes (set by parent)
        $session = $this->View->getRequest()->getSession();
        $this->app = $this->getMockForAbstractClass(BaseApplication::class, [dirname(dirname(__DIR__))]);

        // Use our View (Bootstrap)
        $this->View = new BootstrapView(new ServerRequest(['session' => $session]));
        $this->Flash = new FlashHelper($this->View);

        $this->app->addPlugin('LilHermit/Bootstrap4', ['path' => ROOT . DS]);
    }

    public function tearDown() : void {
        parent::tearDown();

        unset($this->View, $this->Flash);

        $this->app->getPlugins()->remove('LilHermit/Bootstrap4');
    }

    /**
     * testFlash method
     *
     * @return void
     */
    public function testFlash() {
        $result = $this->Flash->render();

        $this->assertHtml([
            ['div' => ['class' => 'alert alert-info alert-dismissible fade show', 'role' => 'alert']],
            ['button' => ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']],
            ['span' => ['aria-hidden' => 'true']],
            '&times;',
            ['/span' => true],
            ['/button' => true],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

        $expected = '<div id="classy-message">Recorded</div>';
        $result = $this->Flash->render('classy');
        $this->assertEquals($expected, $result);

        $result = $this->Flash->render('notification');
        $expected = [
            'div' => ['id' => 'notificationLayout'],
            '<h1', 'Alert!', '/h1',
            '<h3', 'Notice!', '/h3',
            '<p', 'This is a test of the emergency broadcasting system', '/p',
            '/div'
        ];
        $this->assertHtml($expected, $result);
        $this->assertNull($this->Flash->render('non-existent'));
    }

    /**
     * Test that when rendering a stack, messages are displayed in their
     * respective element, in the order they were added in the stack
     *
     * @return void
     */
    public function testFlashWithStack() {
        $result = $this->Flash->render('stack');
        $expected = [
            ['div' => ['class' => 'alert alert-info alert-dismissible fade show', 'role' => 'alert']],
            ['button' => ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']],
            ['span' => ['aria-hidden' => 'true']],
            '&times;',
            ['/span' => true],
            ['/button' => true],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true],

            ['div' => ['id' => 'notificationLayout']],
            '<h1', 'Alert!', '/h1',
            '<h3', 'Notice!', '/h3',
            '<p', 'This is a test of the emergency broadcasting system', '/p',
            '/div',

            ['div' => ['id' => 'classy-message']], 'Recorded', '/div'
        ];
        $this->assertHtml($expected, $result);
        $this->assertNull($this->View->getRequest()->getSession()->read('Flash.stack'));
    }

    /**
     * Tests all bootstrap alert variants
     *
     * @return void
     */
    public function testFlashVariants() {

        $this->View->getRequest()->getSession()->write('Flash.flash.0.element', 'flash/error');
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-danger alert-dismissible fade show', 'role' => 'alert']],
            ['button' => ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']],
            ['span' => ['aria-hidden' => 'true']],
            '&times;',
            ['/span' => true],
            ['/button' => true],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

        $this->View->getRequest()->getSession()->write('Flash.flash.0', [
            'key' => 'flash',
            'message' => 'This is a calling',
            'element' => 'flash/success',
            'params' => []
        ]);
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-success alert-dismissible fade show', 'role' => 'alert']],
            ['button' => ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']],
            ['span' => ['aria-hidden' => 'true']],
            '&times;',
            ['/span' => true],
            ['/button' => true],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

        $this->View->getRequest()->getSession()->write('Flash.flash.0', [
            'key' => 'flash',
            'message' => 'This is a calling',
            'element' => 'flash/warning',
            'params' => []
        ]);
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-warning alert-dismissible fade show', 'role' => 'alert']],
            ['button' => ['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']],
            ['span' => ['aria-hidden' => 'true']],
            '&times;',
            ['/span' => true],
            ['/button' => true],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);
    }

    /**
     * Tests the no-dismiss param (all variants)
     *
     * @return void
     */
    public function testFlashNoDismiss() {

        $this->View->getRequest()->getSession()->write('Flash.flash.0', [
            'key' => 'flash',
            'message' => 'This is a calling',
            'element' => 'flash/default',
            'params' => ['noDismiss' => true]
        ]);
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-info', 'role' => 'alert']],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

        $this->View->getRequest()->getSession()->write('Flash.flash.0', [
            'key' => 'flash',
            'message' => 'This is a calling',
            'element' => 'flash/info',
            'params' => ['noDismiss' => true]
        ]);
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-info', 'role' => 'alert']],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

        $this->View->getRequest()->getSession()->write('Flash.flash.0', [
            'key' => 'flash',
            'message' => 'This is a calling',
            'element' => 'flash/error',
            'params' => ['noDismiss' => true]
        ]);
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-danger', 'role' => 'alert']],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

        $this->View->getRequest()->getSession()->write('Flash.flash.0', [
            'key' => 'flash',
            'message' => 'This is a calling',
            'element' => 'flash/success',
            'params' => ['noDismiss' => true]
        ]);
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-success', 'role' => 'alert']],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

        $this->View->getRequest()->getSession()->write('Flash.flash.0', [
            'key' => 'flash',
            'message' => 'This is a calling',
            'element' => 'flash/warning',
            'params' => ['noDismiss' => true]
        ]);
        $result = $this->Flash->render();
        $this->assertHtml([
            ['div' => ['class' => 'alert alert-warning', 'role' => 'alert']],
            'preg:/[\s]*This is a calling[\s]*/',
            ['/div' => true]
        ], $result);

    }
}