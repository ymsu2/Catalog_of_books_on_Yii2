<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Каталог книг';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Добро пожаловать в каталог книг!</h1>

        <p class="lead">Здесь вы можете просматривать книги и авторов, а также оформлять подписки.</p>

        <p>
            <?= Html::a('Просмотреть книги', ['book/index'], ['class' => 'btn btn-lg btn-primary']) ?>
            <?= Html::a('Просмотреть авторов', ['author/index'], ['class' => 'btn btn-lg btn-success']) ?>
        </p>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-4">
                <h2>Книги</h2>
                <p>Просмотр полного каталога книг с возможностью поиска и фильтрации.</p>
                <p><?= Html::a('Перейти к книгам &raquo;', ['book/index'], ['class' => 'btn btn-default']) ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Авторы</h2>
                <p>Список всех авторов с информацией об их книгах. Вы можете подписаться на уведомления о новых книгах.</p>
                <p><?= Html::a('Перейти к авторам &raquo;', ['author/index'], ['class' => 'btn btn-default']) ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Отчеты</h2>
                <p>Топ-10 авторов по количеству выпущенных книг за выбранный год.</p>
                <p><?= Html::a('Посмотреть отчет &raquo;', ['book/report'], ['class' => 'btn btn-default']) ?></p>
            </div>
        </div>
    </div>

</div>