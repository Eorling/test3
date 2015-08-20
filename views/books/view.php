<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Books */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="books-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'author_id',
                'value'     => $model->author->firstname . ' ' . $model->author->lastname
            ],
            'date',
            'date_create',
            'date_update',
            [
                'attribute' => 'preview',
                'value'     => Html::img(
                    '@web/previews/' . $model->preview,
                    ['width'=>'250px']
                ),
                'format'    => 'html',
            ],
        ],
    ]) ?>

</div>
