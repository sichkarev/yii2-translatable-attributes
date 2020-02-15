<?php
/* @var \yii\widgets\ActiveForm $form */
/* @var string $attribute */
/* @var string $label */
/* @var string $className */
?>

<?=$form->field($this->context->model, $attribute, ['options' => ['class' => $className]])
        ->textInput(['maxlength' => true])
        ->label($label);?>