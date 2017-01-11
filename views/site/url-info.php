<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Url info');
$this->params['breadcrumbs'][] = $this->title;

if (empty($dataName) === true) {
    $dataName = 'info';
}

if (empty($tableHeader) === true) {
    $tableHeader = ['Url', 'Title', 'H1', 'Description', 'Keywords', 'Canonical'];
}

if (empty($mainButtonHeader)) {
    $mainButtonHeader = 'Check info';
}
?>
<div class="page-content">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="<?= $dataName ?>-form js-form-container">
        <?= 
            Html::textarea(
                "{$dataName}-form-area",
                '',
                [
                    'class' => "form-control {$dataName}-form__area js-form-area"
                ]
            ) 
        ?>
        <div class="progress <?= $dataName ?>-progress">
            <div 
                class="progress-bar progress-bar-striped <?= $dataName ?>-progress-bar active js-progress-bar" 
                role="progressbar" 
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <span class="js-progress-bar-label"></span>
            </div>
        </div>        
        <div class="<?= $dataName ?>-form-line">
            <?= 
                Html::submitButton(
                    Yii::t('app', $mainButtonHeader),
                    [
                        'class' => "btn btn-primary {$dataName}-form__button js-{$dataName}-get",
                        'id' => "{$dataName}-form-button"
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'Clear form'),
                    [
                        'class' => "btn {$dataName}-form__button js-clear-form",
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'Delete copy'),
                    [
                        'class' => "btn {$dataName}-form__button js-check-form",
                    ]
                ) 
            ?>
            <?= 
                Html::button(
                    Yii::t('app', 'CSV export'),
                    [
                        'class' => "btn btn-inverse btn-success {$dataName}-form__button js-export-form",
                    ]
                ) 
            ?>
        </div>

        <div class="<?= $dataName ?>-form-table__container">
            <table class="<?= $dataName ?>-form-table js-form-table table table-striped table-hover">
                <thead>
                    <tr>
                        <?php foreach ($tableHeader as $thName):?>
                            <th><?= Yii::t('app', $thName) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
