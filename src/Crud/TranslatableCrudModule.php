<?php

namespace Sichkarev\Translatable\Crud;

/**
 * Module definition class
 */
class TranslatableCrudModule extends \yii\base\Module
{
    /**
     * @var string $controllerNamespace
     */
    public $controllerNamespace = 'Sichkarev\Translatable\Crud\Controllers';

    /**
     * @var string $controllerPath
     */
    public $controllerPath = 'Controllers';

    /**
     * @var string $defaultRoute
     */
    public $defaultRoute = 'translate/index';

    /**
     * @var string $viewPath
     */
     public $viewPath = 'Views';

}
