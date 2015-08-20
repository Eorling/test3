<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BooksSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="books-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'date')->widget(\kartik\daterange\DateRangePicker::classname(), [
        'convertFormat'=>true,
        'pluginOptions'=>[
            'format'=>'Y-m-d'
        ]
    ]) ?>

    <?= $form->field($model, 'author_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(\app\models\Authors::find()->all(),'id',
            function($model) {
                return $model->firstname . ' ' . $model->lastname;
            }
        )
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
