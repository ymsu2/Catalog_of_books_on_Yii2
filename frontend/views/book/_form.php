<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $form yii\widgets\ActiveForm */
/* @var $authors common\models\Author[] */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput([
        'type' => 'number',
        'min' => 1000,
        'max' => date('Y'),
    ]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'coverImageFile')->fileInput() ?>

    <?= $form->field($model, 'author_ids')->dropDownList(
        ArrayHelper::map($authors, 'id', 'name'),
        [
            'multiple' => true,
            'size' => 6,
            'class' => 'form-control',
            'style' => 'height: auto;'
        ]
    )->hint('Для выбора нескольких авторов удерживайте Ctrl (Windows) или Cmd (Mac)') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>