Installation
############

Installing via composer
-----------------------

The plugin is available via `Composer <http://getcomposer.org>`_ dependency manager. Make sure
you have composer installed and in your shell path.

Add the plugin with the following command from your application root filter

.. code-block:: bash

    composer require lilhermit/cakephp-plugin-bootstrap4


Setting up your Application
---------------------------

Load the plugin in your `bootstrap.php`::

    Plugin::load('lilHermit/Bootstrap4', ['bootstrap' => true]);


Configuring AppView
-------------------

You have two options for configuring your `AppView`, `Extending AppView` (recommended)
or `BootstrapViewTrait`. The `AppView` configuration allows the plugin to load
the appropriate Helpers

Extending AppView
^^^^^^^^^^^^^^^^^

Make `src/View/AppView.php` extends `BootstrapView` like follows::

    namespace App\View;

    use lilHermit\Bootstrap4\View\BootstrapView;

    class AppView extends BootstrapView {

    }


Using the BootstrapViewTrait
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Make `src/View/AppView.php` use `BootstrapViewTrait` like follows::

    namespace App\View;

    use Cake\View\View;
    use lilHermit\Bootstrap4\View\BootstrapViewTrait;

    class AppView extends View {

        use BootstrapViewTrait;

        public function initialize() {
            parent::initialize();

            $this->initializeBootstrap();
        }
    }

Link plugin assets
------------------

Copy or symlink `Bootstrap4` plugin assets to your webroot

.. code-block:: bash

    bin/cake plugin assets symlink lilHermit/Bootstrap4
