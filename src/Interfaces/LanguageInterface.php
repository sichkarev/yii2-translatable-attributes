<?php

namespace Sichkarev\Translatable\Interfaces;

/**
 * Interface LanguageInterface
 * Используется для языков
 *
 * @package Sichkarev\Translatable\Interfaces
 */
interface LanguageInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getCodeAcceptLanguage();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getOriginalTitle();
}
