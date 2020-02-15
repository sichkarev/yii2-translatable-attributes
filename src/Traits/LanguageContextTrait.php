<?php

namespace Sichkarev\Translatable\Traits;


use Sichkarev\Translatable\Behaviors\TranslatableBehavior;
use Sichkarev\Translatable\Interfaces\LanguageInterface;

/**
 * Trait TranslateContextTrait
 * Use this trait in ORM models extened from ActiveRecord class
 *
 * @package Sichkarev\Translatable\Traits
 */
trait LanguageContextTrait
{
    /**
     * @var LanguageInterface|null $contextLanguage
     */
    public $contextLanguage = null;

    /**
     * @param LanguageInterface $lang
     * @return self
     */
    public function setContextLanguage($lang)
    {
        $this->contextLanguage = $lang;
        $this->trigger(TranslatableBehavior::EVENT_SET_CONTEXT);

        return $this;
    }

    /**
     * @return self
     */
    public function clearLangContext()
    {
        $this->contextLanguage = null;
        $this->trigger(TranslatableBehavior::EVENT_CLEAR_CONTEXT);

        return $this;
    }

    /**
     * @return self
     */
    public function clearTranslate()
    {
        $this->clearLangContext();
        $this->trigger(TranslatableBehavior::EVENT_CLEAR_TRANSLATE);

        return $this;
    }

    /**
     * @return LanguageInterface[]
     */
    public static function getTranslateLanguages()
    {
        return (self::getTranslatableComponent())->getListLanguages();
    }

    /**
     * @return LanguageInterface|null
     */
    public function getContextLanguage()
    {
        return $this->contextLanguage;
    }
}
