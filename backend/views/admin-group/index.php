<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use backend\models\Admin\AdminGroup;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Admins');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-group-index">

    <?= Html::a(Yii::t('app', 'Create') . Yii::t('app', 'Admin'), ['create'], ['class' => 'btn btn-success']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'description',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
