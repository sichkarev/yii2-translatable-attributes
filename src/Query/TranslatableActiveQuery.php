<?php

namespace Sichkarev\Translatable\Query;

use Sichkarev\Translatable\Interfaces\TranslatableInterface;
use Sichkarev\Translatable\Traits\LanguageContextTrait;

/**
 * Class TranslatableActiveQuery
 *
 * @package Sichkarev\Translatable\Query
 */
class TranslatableActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * Поддержка перевода и доступ к моделям при статическом обращении к ActiveQuery
     */
    use LanguageContextTrait;

    /**
     * {@inheritDoc}
     */
    public function all($db = null)
    {
        if ($lang = $this->getContextLanguage()) {
            return array_map(function (TranslatableInterface $item) use ($lang) {
                $item->setContextLanguage($lang);
                return $item;
            }, parent::all($db));
        }

        return parent::all($db);
    }

    /**
     * {@inheritDoc}
     */
    public function one($db = null)
    {
        /**
         * @var TranslatableInterface $item
         */
        $item = parent::one($db);

        if ($lang = $this->getContextLanguage()) {
            $item->setContextLanguage($lang);
        }

        return $item;
    }

    /**
     * {@inheritDoc}
     */
    public function andFilterWhereTranslate($condition)
    {
        $property = $condition[1];

        if ($this->checkPropertyIsTranslatable($property)) {
            return $this->addWhereFromProperty($property, $condition);
        }

        return parent::andFilterWhere($condition);
    }

    /**
     * @param $property
     * @return bool
     */
    private function checkPropertyIsTranslatable($propertyCheck)
    {
        $variants = $this->getPropertyVariants();

        return in_array($propertyCheck, array_keys($variants), true);
    }

    /**
     * @param $property
     * @param $condition
     * @return \Sichkarev\Translatable\Query\TranslatableActiveQuery
     */
    private function addWhereFromProperty($property, $condition)
    {
        if (empty($condition[2])) {
            return $this;
        }
        /**
         * @var TranslatableInterface $model
         */
        $model = (new $this->modelClass);

        $variants = $this->getPropertyVariants();

        /**
         * @var \Sichkarev\Translatable\Interfaces\LanguageInterface $language
         */
        foreach ($variants as $_property => $language) {
            if ($property === $_property) {
                $nameParam = ':' . $property;

                $query = sprintf(
                    '%s->"$.%s.%s" %s %s',
                    $model->attributeNameForTranslate(),
                    rtrim($property, ucfirst($language->getCode())),
                    $language->getCode(),
                    $condition[0],
                    $nameParam
                );

                return $this->andWhere($query, [
                    $nameParam => $condition[2]
                ]);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    private function getPropertyVariants()
    {
        /**
         * @var TranslatableInterface $model
         */
        $model = (new $this->modelClass);

        $variants = [];

        foreach ($model->translateProperties as $property){
            /**
             * @var \Sichkarev\Translatable\Interfaces\LanguageInterface $language
             */
            foreach ($model->getTranslateLanguages() as $language){
                $variants[$property . ucfirst($language->getCode())] = $language;
            }
        }

        return $variants;
    }
}
