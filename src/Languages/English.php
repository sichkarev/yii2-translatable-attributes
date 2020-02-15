<?php

namespace Sichkarev\Translatable\Languages;


use Sichkarev\Translatable\Interfaces\LanguageInterface;

/**
 * Class English
 *
 * @package Sichkarev\Translatable\Languages
 */
class English implements LanguageInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return 'en';
    }

    /**
     * {@inheritDoc}
     */
    public function getCodeAcceptLanguage()
    {
        return 'en-us';
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return 'Английский';
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalTitle()
    {
        return 'English';
    }
}
