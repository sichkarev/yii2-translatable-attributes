<?php

namespace Sichkarev\Translatable\Widgets;

use Sichkarev\Translatable\Interfaces\TranslatableInterface;
use Yii;

/**
 * Class TranslateInput
 *
 * @package Sichkarev\Translatable\widgets
 */
class TranslateInput extends \yii\widgets\InputWidget
{
    /**
     * @var string $class
     */
    public $className = 'col-md-4';

    /**
     * @var \Sichkarev\Translatable\TranslatableComponent $component
     */
    private $component;

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public function run()
    {
        if ($component = Yii::$app->get('translatable')) {
            $this->component = $component;

            return $this->renderDiv();
        }

        return null;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function renderDiv()
    {
        $text = "";

        if (!$this->model instanceof TranslatableInterface || //у модели отсутствует нужный интерфейс
            !$this->model->hasProperty('translateProperties') //TranslatableBehavior не добавлен
        ) {
            throw new \Exception(sprintf('атрибут %s не может быть переведён', $this->attribute));
        }

        $defaultLanguage = $this->component->getDefaultLanguage();

        foreach ($this->component->getListLanguages() as $lang) {
            $attribute = $this->attribute;
            $label =  $this->model->getAttributeLabel($attribute);

            if ($lang->getCode() !== $defaultLanguage->getCode()){
                $attribute .= ucfirst($lang->getCode());

                if (!in_array($attribute, array_keys($this->model->attributeLabels()))) {
                    $label .= sprintf(' (%s)', $lang->getTitle());
                } else {
                    $label = $this->model->getAttributeLabel($attribute);
                }
            }

            $text .= $this->renderInput($attribute, $label);
        }

        return $this->render('template', [
            'input' => $text
        ]);
    }

    /**
     * @param string $attribute
     * @param string $label
     * @return string
     */
    private function renderInput($attribute, $label)
    {
        $form = $this->field->label(false)->form;

        return $this->render('input', [
            'form' => $form,
            'attribute' => $attribute,
            'label' => $label,
            'className' => $this->className
        ]);
    }

    public function getViewPath()
    {
        return '@vendor/sichkarev/yii2-translatable-attributes/src/Widgets/Views';
    }
}
