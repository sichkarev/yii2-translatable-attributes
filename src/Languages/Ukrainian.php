<?php

namespace Sichkarev\Translatable\Languages;


use Sichkarev\Translatable\Interfaces\LanguageInterface;

/**
 * Class Russian
 *
 * @package Sichkarev\Translatable\Languages
 */
class Ukrainian implements LanguageInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return 'ua';
    }

    /**
     * {@inheritDoc}
     */
    public function getCodeAcceptLanguage()
    {
        return 'uk-UA';
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return 'Украинский';
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginalTitle()
    {
        return 'Український';
    }
}
