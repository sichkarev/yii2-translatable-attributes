<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel Sichkarev\Translatable\Crud\Models\TestTranslatableModelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Translatable Models';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Translatable-model-index">

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'nameUa',
            'nameEn',
            'description',
            'text',

            [
                'class' => \yii\grid\ActionColumn::class,
                'contentOptions' => ['style' => 'width:4%;'],
                'template' => '{update}&nbsp;&nbsp;{delete}',
            ]
        ],
    ]); ?>


</div>
