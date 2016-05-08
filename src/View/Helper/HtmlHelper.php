<?php
namespace lilHermit\Bootstrap4\View\Helper;


use Cake\View\View;

class HtmlHelper extends \Cake\View\Helper\HtmlHelper {

    private $bootstrapTemplates = [
        'breadCrumbOl' => '<ol class="breadcrumb"{{attrs}}>{{content}}</ol>',
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
            $result .= $this->formatTemplate('li', [
                'content' => $elementContent,
                'attrs' => $this->templater()->formatAttributes($options)
            ]);
        }
        return $this->formatTemplate('breadCrumbOl', [
            'content' => $result,
            'attrs' => $this->templater()->formatAttributes($ulOptions)
        ]);
    }

}