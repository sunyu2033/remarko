<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Admin\AdminGroup;

/* @var $this yii\web\View */
/* @var $model common\models\Cart */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cart-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'password')->textInput() ?>

    <?= $form->field($model, 'groupId')->dropDownList(AdminGroup::getAdminGroups()) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'visitId')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'lastOnlineTime')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'status')->dropDownList(Admin::getStatusLabels()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
