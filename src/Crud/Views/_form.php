<?php

use Sichkarev\Translatable\Widgets\TranslateInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Sichkarev\Translatable\Crud\Models\TestTranslatableModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="Translatable-model-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=$form->field($model, 'name')->widget(TranslateInput::class, [
        'className' => 'col-md-4'
    ]);?>

    <?=$form->field($model, 'description')->widget(TranslateInput::class);?>

    <?=$form->field($model, 'text')->textarea() ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
