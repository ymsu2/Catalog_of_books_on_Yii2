<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $authors common\models\Author[] */
/* @var $year integer */

$this->title = 'Отчет: Топ-10 авторов за ' . $year . ' год';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-report">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-3">
            <?= Html::beginForm(['report'], 'get') ?>
            <div class="form-group">
                <?= Html::label('Год', 'year') ?>
                <?= Html::input('number', 'year', $year, ['class' => 'form-control', 'min' => 1000, 'max' => date('Y')]) ?>
            </div>
            <?= Html::submitButton('Показать', ['class' => 'btn btn-primary']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <br>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Автор</th>
                <th>Количество книг</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($authors as $index => $author): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= Html::encode($author->name) ?></td>
                <td><?= $author->getBooksCount() ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>