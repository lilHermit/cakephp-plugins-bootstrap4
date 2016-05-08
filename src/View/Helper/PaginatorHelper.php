<?php
namespace lilHermit\Bootstrap4\View\Helper;

use Cake\View\View;

class PaginatorHelper extends \Cake\View\Helper\PaginatorHelper {

    private $bootstrapTemplates = [
        'prevDisabled' => '<li class="page-item disabled"><a class="page-link" href="" onclick="return false;" aria-label="{{text}}"><span aria-hidden="true"><i class="fa fa-angle-double-left"></i></span><span class="sr-only">{{text}}</span></a></li>',
        'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}" aria-label="{{text}}"><span aria-hidden="true"><i class="fa fa-angle-double-left"></i></span><span class="sr-only">{{text}}</span></a></li>',
        'nextDisabled' => '<li class="page-item disabled"><a class="page-link" href="" onclick="return false;" aria-label="{{text}}"><span aria-hidden="true"><i class="fa fa-angle-double-right"></i></span><span class="sr-only">{{text}}</span></a></li>',
        'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}" aria-label="{{text}}"><span aria-hidden="true"><i class="fa fa-angle-double-right"></i></span><span class="sr-only">{{text}}</span></a></li>',

        'first' => '<li class="first"><a href="{{url}}">{{text}}</a></li>',
        'last' => '<li class="last"><a href="{{url}}">{{text}}</a></li>',
        'number' => '<li class="page-item"><a href="{{url}}" class="page-link">{{text}}</a></li>',
        'current' => '<li class="page-item active"><a href="" class="page-link">{{text}} <span class="sr-only">(current)</span></a></li>',

        'sortAsc' => '<a href="{{url}}"><i class="fa fa-sort-asc fa-fw"></i>&nbsp;{{text}}</a>',
        'sortDesc' => '<a href="{{url}}"><i class="fa fa-sort-desc fa-fw"></i>&nbsp;{{text}}</a>',
    ];

    public function __construct(View $View, array $config = []) {
        $this->_defaultConfig['templates'] =
            array_merge($this->_defaultConfig['templates'], $this->bootstrapTemplates);
        parent::__construct($View, $config);
    }

}