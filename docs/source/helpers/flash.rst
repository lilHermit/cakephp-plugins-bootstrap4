Flash
=====

The standard `default`, `error` and `success` flash messages are styled to Bootstrap Alerts. I have also implemented `warning` and `info` (`default` will render `info`).

Dismissable Alert
-----------------

Use the `FlashComponent` as you normally would::

    $this->Flash->set('This is an alert, which will render as info');
    $this->Flash->info('This is an info alert');
    $this->Flash->error('Something bad happened');
    $this->Flash->success('Everything worked as expected');
    $this->Flash->warning('Warning: Might not have saved!');

would render as

.. raw:: html

    <div class="bootstrap-example">

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        This is an alert, which will render as info
    </div>

    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        This is an info alert
    </div>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Something bad happened
    </div>

    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Everything worked as expected
    </div>

    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        Warning: Might not have saved!
    </div>

    </div>

Standard Alert
--------------
You can drop the dismiss icon by passing ``['noDismiss' => true]`` ::

    $this->Flash->error('Something bad happened (You can\'t dismiss this)', [
        'params' => ['noDismiss' => true]]
    );

would render as

.. raw:: html

    <div class="bootstrap-example">
        <div class="alert alert-danger" role="alert">Something bad happened (You can&#039;t dismiss this)</div>
    </div>

.. meta::
    :title lang=en: FlashHelper
    :description lang=en: The Bootstrap FlashHelper extends the core FlashHelper
    :keywords lang=en: flashhelper, flash, helper
