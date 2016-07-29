<?php
namespace lilHermit\Bootstrap4\View\Helper;


use Cake\View\View;

class HtmlHelper extends \Cake\View\Helper\HtmlHelper {

    private $bootstrapTemplates = [
        'breadCrumbOl' => '<ol class="breadcrumb"{{attrs}}>{{content}}</ol>',
        'breadCrumbLi' => '<li class="breadcrumb-item"{{attrs}}>{{content}}</li>',
    ];

    public function __construct(View $View, array $config = []) {
        $this->_defaultConfig['templates'] =
            array_merge($this->_defaultConfig['templates'], $this->bootstrapTemplates);

        parent::__construct($View, $config);
    }

    public function getCrumbList(array $options = [], $startText = false) {
        $defaults = ['firstClass' => false, 'lastClass' => 'active', 'separator' => '', 'escape' => true];
        $options += $defaults;
        $firstClass = $options['firstClass'];
        $lastClass = $options['lastClass'];
        $separator = $options['separator'];
        $escape = $options['escape'];
        unset($options['firstClass'], $options['lastClass'], $options['separator'], $options['escape']);

        $crumbs = $this->_prepareCrumbs($startText, $escape);
        if (empty($crumbs)) {
            return null;
        }

        $result = '';
        $crumbCount = count($crumbs);
        $ulOptions = $options;
        foreach ($crumbs as $which => $crumb) {
            $options = [];
            if (empty($crumb[1])) {
                $elementContent = $crumb[0];
            } else {
                $elementContent = $this->link($crumb[0], $crumb[1], $crumb[2]);
            }
            if (!$which && $firstClass !== false) {
                $options['class'] = $firstClass;
            } elseif ($which == $crumbCount - 1 && $lastClass !== false) {
                $options['class'] = $lastClass;
            }
            if (!empty($separator) && ($crumbCount - $which >= 2)) {
                $elementContent .= $separator;
            }
            $result .= $this->formatTemplate('breadCrumbLi', [
                'content' => $elementContent,
                'attrs' => $this->templater()->formatAttributes($options)
            ]);
        }
        return $this->formatTemplate('breadCrumbOl', [
            'content' => $result,
            'attrs' => $this->templater()->formatAttributes($ulOptions)
        ]);
    }

    /**
     * Creates a bootstrap button.
     *
     * If $url starts with "http://" this is treated as an external link. Else,
     * it is treated as a path to controller/action and parsed with the
     * UrlHelper::build() method.
     *
     * If the $url is empty, $title is used instead.
     *
     * ### Options
     *
     * - `size` Can be `small`, `normal` or `large` (default is `normal`)
     * - `class` Any additional css classes
     * - `secondary` Boolean true if you want secondary colour.
     * - `outline` Boolean true if you want button outlined.
     *
     * @param string $title The content to be used for the button.
     * @param string|array|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array $options Array of options and HTML attributes.
     * @return string the element.
     */
    public function button($title, $url = null, array $options = []) {
        $options = $options + [
                'size' => 'normal',
                'secondary' => false,
                'outline' => false,
                'class' => []
            ];

        // Convert and sanitise the css class
        if (is_array($options['class'])) {
            $options['class'][] = 'btn';
        } else if (is_string($options['class'])) {
            $options['class'] = ['btn', $options['class']];
        } else {
            $options['class'] = ['btn'];
        }

        if ($options['outline']) {
            $options['class'][] = $options['secondary'] ? 'btn-outline-secondary' : 'btn-outline-primary';
        } else {
            $options['class'][] = $options['secondary'] ? 'btn-secondary' : 'btn-primary';
        }

        switch ($options['size']) {
            case 'large':
            case 'lg':
                $options['class'][] = 'btn-large';
                break;
            case 'small':
            case 'sm':
                $options['class'][] = 'btn-sm';
                break;
        }

        unset($options['size'], $options['secondary']);
        return $this->link($title, $url, $options);
    }

}