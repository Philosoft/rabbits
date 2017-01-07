<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Url info');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="info-form js-form-container">
        <?= 
            Html::textarea(
                'info-form-area',
                '',
                [
                    'class' => 'form-control info-form__area js-form-area'
                ]
            ) 
        ?>
        <div class="info-form-line">
            <?= 
                Html::submitButton(
                    Yii::t('app', 'Check info'),
                    [
                        'class' => 'btn btn-primary info-form__button js-info-get',
                        'id' => 'info-form-button'
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'Clear form'),
                    [
                        'class' => 'btn info-form__button js-clear-form',
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'Delete copy'),
                    [
                        'class' => 'btn info-form__button js-check-form',
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'CSV export'),
                    [
                        'class' => 'btn btn-inverse btn-success info-form__button js-export-form',
                    ]
                ) 
            ?>
        </div>

        <div class="info-form-table__container">
            <table class="info-form-table js-form-table table table-striped table-hover">
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Title</th>
                        <th>H1</th>
                        <th>Description</th>
                        <th>Keywords</th>
                        <th>Canonical</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
