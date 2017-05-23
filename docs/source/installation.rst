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

Load the plugin in your `bootstrap.php` before the ``Type::build`` statements ::

    ...

    Plugin::load('LilHermit/Bootstrap4', ['bootstrap' => true]);

    ...

    Type::build('time')
        ->useImmutable();
    Type::build('date')
        ->useImmutable();
    Type::build('datetime')
        ->useImmutable();
    Type::build('timestamp')
        ->useImmutable();

    ...

Configuring AppView
-------------------

You have two options for configuring your `AppView`, `Extending AppView` (recommended)
or `BootstrapViewTrait`. The `AppView` configuration allows the plugin to load
the appropriate Helpers

Extending AppView
^^^^^^^^^^^^^^^^^

Make `src/View/AppView.php` extends `BootstrapView` like follows::

    namespace App\View;

    use LilHermit\Bootstrap4\View\BootstrapView;

    class AppView extends BootstrapView {

    }


Using the BootstrapViewTrait
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Make `src/View/AppView.php` use `BootstrapViewTrait` like follows::

    namespace App\View;

    use Cake\View\View;
    use LilHermit\Bootstrap4\View\BootstrapViewTrait;

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

    bin/cake plugin assets symlink LilHermit/Bootstrap4

Building templates with bake
----------------------------

You can use bake to build bootstrap4 templates by adding the `theme` option as follows to
build templates for `users`

.. code-block:: bash

    bin/cake bake template -t LilHermit/Bootstrap4 Users

.. versionadded:: 2.1.6.10 Bake templates added
