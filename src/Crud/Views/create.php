<?php
/* @var $this yii\web\View */
/* @var $model Sichkarev\Translatable\Crud\Models\TestTranslatableModel */

$this->title = 'Create Translatable Model';
$this->params['breadcrumbs'][] = ['label' => 'Translatable Models', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Translatable-model-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
