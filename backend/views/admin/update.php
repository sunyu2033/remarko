<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cart */

$this->title = Yii::t('app', 'Update') . Yii::t('app', 'Admin') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Admin'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="admin-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
