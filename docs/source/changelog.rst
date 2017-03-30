Changelog
#########

2.1.6.5 (TBC)
-------------
.. raw:: html

    <span class="badge badge-primary">Supports Bootstrap 4.0.0-alpha6</span>

- Simplified Html5DateTime Validation with behavior
- Unpinned from CakePHP 3.3.x (Now supports 3.4.x)
- Added support for setting html5Render false at Form creation time

2.1.6.4 (05-Mar-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary">Supports Bootstrap 4.0.0-alpha6</span>
- New documentation
- Improved File Browser control
- Select controls now render as bootstrap
- Added more tests and Fixed some others

2.1.6.3 (27-Feb-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary">Supports Bootstrap 4.0.0-alpha6</span>

- Fixed non-dismissible alert css
- Excluded certain files from packagist dists
- Added tether javascript output
- Add tests for HtmlHelper::bootstrapScript and HtmlHelper::bootstrapCss

2.1.6.2 (13-Feb-2017)
---------------------
.. raw:: html

    <span class="badge badge-primary">Supports Bootstrap 4.0.0-alpha6</span>

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