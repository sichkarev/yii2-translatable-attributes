<?php

namespace Sichkarev\Translatable\Traits;

use Sichkarev\Translatable\Behaviors\TranslatableBehavior;
use Sichkarev\Translatable\Query\TranslatableActiveQuery;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Trait TranslateActiveRecordTrait
 *
 * @property array $translatableSafeAttributes filled in TranslateActiveRecordBehavior
 *
 * @package common\models\base
 */
trait TranslateActiveRecordTrait
{
    /**
     * Support translate with context and access model to $this
     */
    use LanguageContextTrait;

    /**
     * Добавляет функциональность переводов
     *
     * @return \Sichkarev\Translatable\TranslatableComponent
     * @throws \yii\base\InvalidConfigException
     */
    public static function getTranslatableComponent()
    {
        return Yii::$app->get('translatable');
    }

    /**
     * Добавляет функциональность переводов
     *
     * @return array
     */
    private static function addTranslatableBehavior()
    {
        try {
            return [
                [
                    'class' => TranslatableBehavior::class,
                    'languages' => self::getTranslatableComponent()->getListLanguages(),
                    'defaultLanguage' => self::getTranslatableComponent()->getDefaultLanguage()
                ]
            ];
        }catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function find()
    {
        return new TranslatableActiveQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function safeAttributes()
    {
        return ArrayHelper::merge(parent::safeAttributes(), $this->translatableSafeAttributes);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        $this->setSafeAttributes();
    }

    private function setSafeAttributes()
    {
        $this->translatableSafeAttributes = ArrayHelper::merge(self::safeAttributes(), $this->translatableSafeAttributes);
    }
}
