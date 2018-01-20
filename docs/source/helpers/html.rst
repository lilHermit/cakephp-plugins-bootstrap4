Html
####

.. php:namespace:: LilHermit\Bootstrap4\View\Helper

.. php:class:: HtmlHelper(View $view, array $config = [])

The ``HtmlHelper`` builds upon the CakePHP core ``HtmlHelper`` and transparently
styles elements. Buttons, crumbs and progress bars are styled to Bootstrap4.


Bootstrap css
=============

.. php:method:: bootstrapScript($version)

The plugin provides markup for css from the official Bootstrap CDN

For adding the latest official css that the plugin is built (|bootstrap_ver|) for add in your layout::

    <?= $this->Html->bootstrapCss(); ?>

Something similar to the following html will be rendered to your view

.. code-block:: html

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

If you want an older css version then pass in a string as the first parameter. For example::

    <?= $this->Html->bootstrapCss('4.0.0-beta3'); ?>

If you want to use a version unknown to the plugin you can pass in an array, for example
to use ``bootstrap4.0.0-beta3``::

    <?= $this->Html->bootstrapCss([
        'url' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/css/bootstrap.min.css',
        'integrity' -> 'sha384-MIwDKRSSImVFAZCVLtU0LMDdON6KVCrZHyVQQj6e8wIEJkW4tvwqXrbMIya1vriY']
    ); ?>

Bootstrap Javascript
====================

.. php:method:: bootstrapScript($version = [])

The plugin provides markup for javascript from the official Bootstrap CDN

For adding the latest official bootstrap and `popper <http://popper.js.org/>`_ javascript that the plugin is build for (|bootstrap_ver|) add the following
to your layout. ::

    <?= $this->Html->bootstrapScript(); ?>

You will get something similar to in your view

.. code-block:: html

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

To disabled the inclusion of ``popper`` javascript then add use the following::

        <?= $this->Html->bootstrapScript(['popper' => false]); ?>


If you want an older javascript then pass in an array with `version` key as the first parameter. For example::

    <?= $this->Html->bootstrapScript([ 'version' => '4.0.0-beta3']); ?>

You can also add urls that the plugin does not currently support by providing the `url` and `integrity` keys. For example to use 4.0.0-alpha4::

    $this->Html->bootstrapScript([
        'url' => 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js',
        'integrity' => 'VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU'
        ]);



.. todo::
    Add progress method
    Add button method
    Add crumblist