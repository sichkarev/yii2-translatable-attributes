<?php

namespace Sichkarev\Translatable\Languages;

use Sichkarev\Translatable\Interfaces\LanguageInterface;

/**
 * Class Russian
 *
 * @package Sichkarev\Translatable\Languages
 */
class Russian implements LanguageInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return 'ru';
    }

    /**
     * {@inheritDoc}
     */
    public function getCodeAcceptLanguage()
    {
        return 'ru-RU';
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return 'Русский';
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalTitle()
    {
        return 'Русский';
    }


}
