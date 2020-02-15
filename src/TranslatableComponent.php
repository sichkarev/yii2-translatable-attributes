<?php

namespace Sichkarev\Translatable;

use Sichkarev\Translatable\Interfaces\LanguageInterface;
use Sichkarev\Translatable\Languages\English;
use Sichkarev\Translatable\Languages\Russian;
use Sichkarev\Translatable\Languages\Ukrainian;

/**
 * Class TranslatableComponent
 *
 * @package Sichkarev\Translatable
 */
class TranslatableComponent extends \yii\base\Component
{
    /**
     * @var array $languages
     */
    public $languages;

    /**
     * @var LanguageInterface
     */
    public $defaultLanguage = null;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        if (!$this->languages) {
            $this->languages = [
                Russian::class,
                English::class,
                Ukrainian::class
            ];
        }
    }

    /**
     * @return LanguageInterface[]
     */
    public function getListLanguages()
    {
        return array_map(function ($_class) {
            return new $_class;
        }, $this->languages);
    }

    /**
     * @return LanguageInterface|null
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage ? new $this->defaultLanguage : null;
    }
}
