<?php

namespace Sichkarev\Translatable\Interfaces;

/**
 * Interface TranslatableInterface
 * Используется для сущностей, которым требуется перевод
 *
 * @package Sichkarev\Translatable\Interfaces
 */
interface TranslatableInterface
{
    /**
     * Возвращает атрибут (название колонки в таблице), в котором будет храниться перевод
     */
    public function attributeNameForTranslate();

    /**
     * Устанавливает контекст языка
     *
     * @param LanguageInterface $lang
     * @return mixed
     */
    public function setContextLanguage($lang);

    /**
     * @return LanguageInterface| null
     */
    public function getContextLanguage();
}
