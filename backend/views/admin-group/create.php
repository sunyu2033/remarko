<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Admin\AdminGroup */

$this->title = 'Create Admin Group';
$this->params['breadcrumbs'][] = ['label' => 'Admin Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
