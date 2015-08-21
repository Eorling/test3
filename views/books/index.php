<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BooksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js',['position'=>$this::POS_HEAD]);
?>
<div class="books-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'preview',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(
                        Html::img(
                            '@web/previews/' . $data['preview'],
                            ['width'=>'80px']
                        ),
                        '@web/previews/' . $data['preview'],
                        ['rel'=>'prettyPhoto']
                    );

                },
            ],
            [
                'attribute' => 'author_id',
                'value' => function($data) {
                    $author = $data->author;
                    return $author->firstname.' '.$author->lastname;
                }

            ],
            'date:date',
            'date_create:date',

            [   'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'headerOptions' => ['class' => 'activity-view-link',],
                'contentOptions' => ['class' => 'padding-left-5px'],

                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>','#', [
                            'class' => 'activity-view-link',
                            'title' => Yii::t('yii', 'View'),
                            'data-toggle' => 'modal',
                            'data-target' => '#activity-modal',
                            'data-id' => $key,
                            'data-pjax' => '0',

                        ]);
                    },
                ],


            ],
        ],
    ]);

    \nirvana\prettyphoto\PrettyPhoto::widget([
        'target' => "a[rel^='prettyPhoto']",
        'pluginOptions' => [
            'opacity' => 0.60,
            'theme' => \nirvana\prettyphoto\PrettyPhoto::THEME_DARK_SQUARE,
            'social_tools' => false,
            'autoplay_slideshow' => false,
            'modal' => true
        ],
    ]);

    ?>
    <?php \yii\bootstrap\Modal::begin([
        'id' => 'activity-modal',
        'header' => '<h4 class="modal-title">Просмотр книги</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',

    ]); ?>

    <div class="well">


    </div>


    <?php \yii\bootstrap\Modal::end(); ?>

</div>

<script type="text/javascript">
    $(".activity-view-link").click(function() {
        id = $(this).closest('tr').data('key');
        $.get( "http://balachky.in.ua/test3/books/"+id, function( data ) {
            $('.modal-body').html(data);
            $('#activity-modal').modal();
        });
    });
</script>