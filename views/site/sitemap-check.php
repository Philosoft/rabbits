<?php

/**
 * @var \yii\web\View $this
 */

use \yii\helpers\Html;

$this->title = Yii::t("app", "Sitemap check");
$this->params["breadcrumbs"][] = $this->title;
\app\assets\LoadersAsset::register($this);
?>

<h1><?= Html::encode($this->title) ?></h1>

<form class="form-inline" id="check-sitemap-form">
  <div class="form-group">
    <label for="domain">Domain:</label>
    <input type="text" class="form-control" name="domain" id="domain" placeholder="https://example.com" value="http://techbox.one">
  </div>
  <button id="check-sitemap" type="submit" class="btn btn-default">Check</button>
</form>

<h3>Результаты: <small>запрещённые в robots.txt урлы</small></h3>
<table id="result-table" class="table table-stripped">
</table>

<div class="backdrop" style="display: none">
    <div class="loader">
        <div class="pacman">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>

<style>
    .backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 100;
        background-color: rgba(51, 51, 51, 0.7);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .loader {
        width: 5rem;
        height: 5rem;
        margin: 0 auto;
        display: flex;
        justify-content: center;
    }
</style>

<?php
$this->registerJs(<<<EOS
var megaInterval = undefined;

$("#check-sitemap-form").submit(function (e) {
    e.preventDefault();
    $(".backdrop").show();
    $.ajax({
        "type": "post",
        "url": "/sitemap/check",
        "data": $(this).serialize()
    })
    .done(function (data) {
        let table = "";
        if (data.length > 0) {
            table += "<tr><td class=\"bg-warning\">This urls are dissalowed by robots.txt</td></tr>";
            data.forEach(function (url) {
                table += "<tr><td>" + url + "</td></tr>";
            });
        } else {
            table = "<tr><td class=\"bg-success\">There is nothing wrong with this sitemap</td></tr>";
        }
        
        $("#result-table").html(table);
    })
    .always(function () {
        $(".backdrop").hide();
    });
});
EOS
);
