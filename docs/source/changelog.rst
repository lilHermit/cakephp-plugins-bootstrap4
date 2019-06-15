Changelog
#########

4.0.0.4003 (15-June-2019)
-------------------------
.. raw:: html

    <span class="badge badge-primary pull-right pull-right">Supports Bootstrap 4.0.0</span>

- Fixed depreciated method in Bake (`Issue #6 <https://github.com/lilHermit/cakephp-plugins-bootstrap4/issues/6>`_ + `Issue #14 <https://github.com/lilHermit/cakephp-plugins-bootstrap4/issues/14>`_)
- Fixed templating issue (`Issue #8 <https://github.com/lilHermit/cakephp-plugins-bootstrap4/issues/8>`_)
- Fixed hidden field issue with CakePHP 3.7.x (`Issue #10 <https://github.com/lilHermit/cakephp-plugins-bootstrap4/issues/10>`_)
- Merged Immutable objects as now recommended (`PR #15 <https://github.com/lilHermit/cakephp-plugins-bootstrap4/pull/15>`_)
  Thanks to `Ali1 <https://github.com/Ali1>`_


4.0.0.4002 (18-July-2018)
-------------------------
.. raw:: html

    <span class="badge badge-primary pull-right pull-right">Supports Bootstrap 4.0.0</span>

- Fixed missing required css class (`Issue #3 <https://bitbucket.org/lilHermit/cakephp-plugins-bootstrap4/issues/3>`_)
- Fixed checkboxes and radio in grid layout (`Issue #5 <https://bitbucket.org/lilHermit/cakephp-plugins-bootstrap4/issues/5>`_)

4.0.0.4001 (5-June-2018)
------------------------
.. raw:: html

    <span class="badge badge-primary pull-right pull-right">Supports Bootstrap 4.0.0</span>

- Updates for CakePHP 3.6 deprecations

4.0.0.4000 (20-January-2018)
----------------------------
.. raw:: html

    <span class="badge badge-primary pull-right pull-right">Supports Bootstrap 4.0.0</span>

- Updated to Bootstrap 4.0.0 (Release)

4.0.0.2300 (03-January-2018)
----------------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-beta3</span>

- Updated to Bootstrap 4.0.0-beta3
    - Updates to custom checkbox/radio/multi-checkbox control
    - Updates to custom file control
    - Updates to prefix/suffix
- **Breaking changes:** prefix/suffix renamed to prepend/append to keep inline with Bootstrap terminology
- Added small size to prepend/append


4.0.0.2200 (20-Nov-2017)
------------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-beta2</span>

- Updated to Bootstrap 4.0.0-beta2
- Fixed some tests
- Updated all FormHelper::input to FormHelper::control calls

2.2.0.1 (29-Aug-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-beta</span>

- Fixes to Form feedback (due to changes since alpha6) (using server-side methods)

2.2.0.0 (24-Aug-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-beta</span>

- Updated to Bootstrap 4.0.0-beta
- Updated tether references to popper
- Fixed tests broken by upstream changes

2.1.6.10 (02-Aug-2017)
----------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- Improvements to datetime parsing on HTML5 datetime controls
- Fix to datetime control css class rendering

2.1.6.9 (11-June-2017)
----------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- Added support for bake templates

2.1.6.8 (18-May-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- ``Breaking change``: Namespace has been changed to PascalCase to comply with CakePHP conventions. This change is to facilitate the Bake Templates coming soon.

2.1.6.6 (16-May-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- Added support for :ref:`Block <block-layout>`, :ref:`Inline <inline-layout>` and :ref:`Grid <grid-layout>` layouts.
- Added global form control and label class setting (:ref:`Docs here <layout-classes>`)
- Added support for passing attributes to Prefix/Suffix containers

2.1.6.5 (30-Mar-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- Simplified Html5DateTime Validation with behavior
- Unpinned from CakePHP 3.3.x (Now supports 3.4.x)
- Added support for setting html5Render false at Form creation time
- User defined templates are no longer overridden by the plugin

2.1.6.4 (05-Mar-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- New documentation
- Improved File Browser control
- Select controls now render as bootstrap
- Added more tests and Fixed some others

2.1.6.3 (27-Feb-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- Fixed non-dismissible alert css
- Excluded certain files from packagist dists
- Added tether javascript output
- Add tests for HtmlHelper::bootstrapScript and HtmlHelper::bootstrapCss

2.1.6.2 (13-Feb-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary pull-right">Supports Bootstrap 4.0.0-alpha6</span>

- Fixed help container (now not rendered as empty when no help)
- Plugin javascript is no longer required
- Plugin javascript is defaulted to not included now with HtmlHelper::bootstrapScript
- Updated Prefix/Suffix in FormHelper
    - Supports attributes / css
    - Supports multiple
    - Supports non-escaping
    - Now Support button type
    - Now Supports large size
- Added Progress method to HtmlHelper
    - Supports multiple
    - Supports stripes (inc animated)
    - Supports label
- Now requires CakePHP 3.3.15 now (Requires modification we submitted)