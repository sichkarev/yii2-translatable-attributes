<?php

/* @var $this yii\web\View */
/* @var $model Sichkarev\Translatable\Crud\Models\TestTranslatableModel */

$this->title = 'Update Translatable Model: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Translatable Models', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="Translatable-model-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
