<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Url response');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="response-form js-form-container">
        <?= 
            Html::textarea(
                'resonse-form-area',
                '',
                [
                    'class' => 'form-control response-form__area js-form-area'
                ]
            ) 
        ?>
        <div class="response-form-line">
            <?= 
                Html::submitButton(
                    Yii::t('app', 'Check response'),
                    [
                        'class' => 'btn btn-primary response-form__button js-response-get',
                        'id' => 'response-form-button'
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'Clear form'),
                    [
                        'class' => 'btn response-form__button js-clear-form',
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'Delete copy'),
                    [
                        'class' => 'btn response-form__button js-check-form',
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'CSV export'),
                    [
                        'class' => 'btn btn-inverse btn-success response-form__button js-export-form',
                    ]
                ) 
            ?>
        </div>

        <div class="response-form-table__container">
            <table class="response-form-table js-form-table table table-striped table-hover">
                <thead>
                    <tr>
                        <th>URL</th>
                        <th><?= Yii::t('app', 'Response') ?></th>
                        <th><?= Yii::t('app', 'Number of redirects') ?></th>
                        <th><?= Yii::t('app', 'Final address') ?></th>
                        <th><?= Yii::t('app', 'Final response') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
