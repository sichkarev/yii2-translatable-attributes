<?php

namespace Sichkarev\Translatable\Behaviors;

use Sichkarev\Translatable\Interfaces\LanguageInterface;
use Sichkarev\Translatable\Interfaces\TranslatableInterface;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class TranslatableBehavior
 *
 * @property TranslatableInterface|ActiveRecord $owner заполняет Yii2 Behavior
 *
 * @package Sichkarev\Translatable\Behaviors
 */
class TranslatableBehavior extends Behavior
{
    const EVENT_SET_CONTEXT = 'EVENT_SET_CONTEXT';

    const EVENT_CLEAR_CONTEXT = 'EVENT_CLEAR_CONTEXT';

    const EVENT_CLEAR_TRANSLATE = 'EVENT_CLEAR_TRANSLATE';

    /**
     * @var LanguageInterface[] $languages
     */
    public $languages = [];

    /**
     * @var LanguageInterface $defaultLanguage
     */
    public $defaultLanguage = null;

    /**
     * Список свойст для перевода
     *
     * @var string[] $translateProperties
     */
    public $translateProperties = [];

    /**
     * Поле в котором хранятся переводы
     *
     * @var string $translateAttribute
     */
    private $translateAttribute;

    /**
     * @var \ReflectionClass $currentClass
     */
    private $currentClass;

    /**
     * @var string[] $translatableSafeAttributes
     */
    public $translatableSafeAttributes = [];

    /**
     * @var \yii\db\ColumnSchema $translateAttributeDbSchema
     */
    private $translateAttributeDbSchema;

    /**
     * {@inheritDoc}
     */
    public function events()
    {
        return ArrayHelper::merge(parent::events(),[
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSaveModel',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSaveModel',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSaveModel',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSaveModel',
            ActiveRecord::EVENT_AFTER_FIND => 'loadFromJson',
            ActiveRecord::EVENT_AFTER_REFRESH => 'refreshFromProperties',
            self::EVENT_SET_CONTEXT => 'setLangContext',
            self::EVENT_CLEAR_CONTEXT => 'refreshFromProperties',
            self::EVENT_CLEAR_TRANSLATE => 'clearTranslateProperties',
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @param TranslatableInterface|\yii\db\ActiveRecord $owner
     * @throws \yii\base\InvalidConfigException
     */
    public function attach($owner)
    {
        if ($owner instanceof TranslatableInterface && \Yii::$app->get('translatable')) {
            parent::attach($owner);

            $this->setReflectionOwnerClass();
            $this->setTranslateAttribute();
            $this->createNewPropertiesInClass();
        }
    }

    /**
     * Создаем ReflectionClass
     */
    private function setReflectionOwnerClass()
    {
        try {
            $this->currentClass = new \ReflectionClass($this->owner);
            $this->getModelProperties();
        } catch (\ReflectionException $exception) {
            //nothing to do
        }
    }

    /**
     * устанавливаем атрибут в котором будет хранится перевод
     * и проверяем наличие свойства в модели
     */
    private function setTranslateAttribute()
    {
        $this->translateAttribute = $this->owner->attributeNameForTranslate();
        $this->translateAttributeDbSchema = $this->owner->getTableSchema()->getColumn($this->translateAttribute);

        if (!$this->owner->hasProperty($this->translateAttribute)) {
            throw new InvalidArgumentException(sprintf('Атрибут %s не найден', $this->translateAttribute));
        }
    }

    /**
     * Получаем свойства помеченные как нуждающиеся в переводе из комментариев (@translate)
     */
    private function getModelProperties()
    {
        $this->translateProperties = $this->checkProperties($this->currentClass);
    }

    /**
     * @param $class
     * @return array
     */
    private function checkProperties(\ReflectionClass $class)
    {
        if (preg_match_all('/(?P<property>\w+)\W+@translate/', $class->getDocComment(), $matches)) {
            return array_map(function ($line) {
                return $line;
            }, $matches['property']);
        } elseif ($class = $class->getParentClass()) {
            return $this->checkProperties($class);
        }

        return [];
    }

    /**
     * @param string            $property
     * @param LanguageInterface $lang
     * @param mixed|null        $value
     */
    private function setPropertyValue($property, LanguageInterface $lang, $value = null)
    {
        if ($lang->getCode() !== $this->defaultLanguage->getCode()) {
            $this->{$this->getPropertyName($property, $lang)} = $value;
        }
    }

    /**
     * @param string     $property
     * @param mixed|null $value
     */
    private function setBasePropertyValue($property, $value = null)
    {
        $this->{$property} = $value;
    }

    /**
     * @param string     $property
     * @param mixed|null $value
     */
    private function setOwnerPropertyValue($property, $value = null)
    {
        $this->owner->{$property} = $value;
    }

    /**
     * @param string                 $property
     * @param LanguageInterface|null $lang
     * @return mixed|null
     */
    private function getPropertyValue($property, $lang = null)
    {
        return $this->owner->{$this->getPropertyName($property, $lang)};
    }

    /**
     * Создаем свойства у модели
     */
    private function createNewPropertiesInClass()
    {
        array_walk($this->translateProperties, function ($propertyName) {
            array_walk($this->languages, function (LanguageInterface $lang) use ($propertyName) {
                $this->setPropertyValue($propertyName, $lang);
                $this->addAttributeToSafeAttributes($this->getPropertyName($propertyName, $lang));
            });
        });
    }

    /**
     * Действия перед сохранением модели
     */
    public function beforeSaveModel()
    {
        $this->fillContextPropertiesValues();

        if ($this->isSetContextLang()) {
            $this->replaceContextPropertiesValuesToOriginal();
        }

        $this->saveToJson();
    }

    /**
     * Действия после сохранения модели
     */
    public function afterSaveModel()
    {
        $this->loadFromJson();
        $this->setTranslatePropertiesByContextLang();
    }

    /**
     * Сохраняем атрибуты в json
     */
    public function saveToJson()
    {
        $data = [];

        foreach ($this->translateProperties as $property){
            foreach ($this->languages as $lang){
                if ($lang->getCode() !== $this->defaultLanguage->getCode() && $value = $this->getPropertyValue($property, $lang)) {
                    $data[$property][$lang->getCode()] = $this->getPropertyValue($property, $lang);
                }
            }
        }

        if ($data) {
            if ($this->translateAttributeDbSchema->dbType !== 'json') {
                $data = json_encode($data, JSON_OBJECT_AS_ARRAY);
            }

            $this->owner->{$this->translateAttribute} = $data;
        } else {
            $this->owner->{$this->translateAttribute} = null;
        }
    }

    /**
     * Извлекаем атрибуты из поля json
     */
    public function loadFromJson()
    {
        if ($this->owner->{$this->translateAttribute}) {
            $data = $this->owner->{$this->translateAttribute};

            if ($this->translateAttributeDbSchema->dbType !== 'json') {
                $data = json_decode($data);
            }

            foreach ($data as $property => $items){
                if (in_array($property, $this->translateProperties)) {
                    foreach ($items as $langCode => $value){
                        if ($lang = $this->getLangByCode($langCode)) {
                            $this->setPropertyValue($property, $lang, $value);
                            $this->setOwnerPropertyValue($this->getPropertyName($property, $lang), $value);
                        }
                    }
                }
            }
        }

        $this->setTranslatePropertiesByContextLang();
    }

    /**
     * @param string $code
     * @return LanguageInterface
     */
    private function getLangByCode($code)
    {
        if ($filter = array_filter($this->languages, function (LanguageInterface $language) use ($code) {
            return $language->getCode() === $code;
        })) {
            return current($filter);
        }
    }

    /**
     * Устанавливаем свойства согласно контекста
     */
    public function setLangContext()
    {
        $this->setTranslatePropertiesByContextLang();
    }

    /**
     * обновляем свойства модели
     */
    public function refreshFromProperties()
    {
        $this->setTranslatePropertiesByContextLang();
        $this->replaceContextPropertiesValuesToOriginal();
    }

    /**
     * Очищаем все переводы
     */
    public function clearTranslateProperties()
    {
        $this->owner->{$this->translateAttribute} = null;

        array_walk($this->translateProperties, function ($property) {
            array_walk($this->languages, function (LanguageInterface $language) use ($property) {
                if ($language->getCode() !== $this->defaultLanguage->getCode()) {
                    $this->setPropertyValue($property, $language);
                    $this->setOwnerPropertyValue($this->getPropertyName($property, $language));
                }
            });
        });
    }

    /**
     * Формируем название свойства с учётом кода языка
     *
     * @param string            $property
     * @param LanguageInterface $lang
     * @return string
     */
    private function getPropertyName($property, $lang = null)
    {
        return $property . ($lang ? ucfirst($lang->getCode()) : '');
    }

    /**
     * Устанавливаем свойства согласно контекста
     */
    private function setTranslatePropertiesByContextLang()
    {
        if ($this->isSetContextLang() && $language = $this->owner->getContextLanguage()) {
            $this->setBasePropertiesFromLang($language);
        }
    }

    /**
     * заполняем значения согласно установленного контекста
     */
    private function fillContextPropertiesValues()
    {
        if ($this->isSetContextLang()) {
            array_walk($this->translateProperties, function ($property) {
                //если пользователь менял данные
                if ($this->owner->getOldAttribute($property) !== $this->getPropertyValue($property)) {
                    $this->setPropertyValue($property, $this->owner->getContextLanguage(), $this->getPropertyValue($property));
                }
            });
        }
    }

    /**
     * Восстанавливаем значения свойств
     */
    private function replaceContextPropertiesValuesToOriginal()
    {
        array_walk($this->translateProperties, function ($property) {
            $this->setBasePropertyValue($property, $this->owner->getOldAttribute($property));
            $this->setOwnerPropertyValue($property, $this->owner->getOldAttribute($property));
        });
    }

    /**
     * переопределяем метод для установки кастомных свойств
     *
     * {@inheritDoc}
     */
    public function __set($name, $value)
    {
        if (array_filter($this->translateProperties, function ($property) use ($name) {
            return strpos($name, $property) === 0;
        })) {
            return $this->{$name} = $value;
        }

        $this->owner->__set($name, $value);
    }

    /**
     * @return bool
     */
    private function isSetContextLang()
    {
        $contextLanguage = $this->owner->getContextLanguage();

        return $contextLanguage !== null && $contextLanguage->getCode() !== $this->defaultLanguage->getCode();
    }

    /**
     * @param LanguageInterface $language
     */
    private function setBasePropertiesFromLang(LanguageInterface $language)
    {
        array_walk($this->translateProperties, function ($property) use ($language) {
            if (!$value = $this->getPropertyValue($property, $language)) {
                $value = $this->getPropertyValue($property);
            }
            $this->setBasePropertyValue($property, $value);
            $this->setOwnerPropertyValue($property, $value);
        });
    }

    /**
     * @param string $property
     */
    private function addAttributeToSafeAttributes($property)
    {
        $this->translatableSafeAttributes[] = $property;
    }
}
