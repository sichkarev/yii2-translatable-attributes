<?php

namespace Sichkarev\Translatable\Crud\Models;

use Sichkarev\Translatable\Interfaces\TranslatableInterface;
use Sichkarev\Translatable\Traits\TranslateActiveRecordTrait;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "test_translatable_model".
 *
 * @property int         $id
 * @property string|null $name        @translate
 * @property string|null $description @translate
 * @property string|null $text
 * @property array|null  $translations
 *
 * @property string|null $nameUa
 * @property string|null $nameEn
 * @property string|null $descriptionUa
 * @property string|null $descriptionEn
 */
class TestTranslatableModel extends \yii\db\ActiveRecord implements TranslatableInterface
{
    use TranslateActiveRecordTrait;

    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return 'test_translatable_model';
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge (
            parent::behaviors(),
            self::addTranslatableBehavior()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function attributeNameForTranslate()
    {
        return 'translations';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['translations'], 'safe'],
            [
                [
                    'name',
                    'nameUa',
                    'nameEn',
                    'description',
                    'descriptionUa',
                    'descriptionEn',
                    'text'
                ],
                'string',
                'max' => 255
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'description' => 'Описание',
            'text' => 'Текст',
            'translations' => 'Переводы',
        ];
    }
}
