<?php

/**
 * @var \yii\web\View $this
 */

use \yii\helpers\Html;

$this->title = Yii::t("app", "Sitemap check");
$this->params["breadcrumbs"][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<form class="form-inline" id="check-sitemap-form">
  <div class="form-group">
    <label for="domain">Domain:</label>
    <input type="text" class="form-control" name="domain" id="domain" placeholder="https://example.com" value="http://techbox.one">
  </div>
  <button id="check-sitemap" type="submit" class="btn btn-default">Check</button>
</form>

<table id="result-table" class="table table-stripped">
</table>

<?php
$this->registerJs(<<<EOS
var megaInterval = undefined;

$("#check-sitemap-form").submit(function (e) {
    e.preventDefault();
    $.ajax({
        "type": "post",
        "url": "/sitemap/check",
        "data": $(this).serialize()
    })
    .done(function (data) {
        let table = "";
        if (data.length > 0) {
            data.forEach(function (url) {
                table += "<tr><td>" + url + "</td></tr>";
            });
        } else {
            table = "<tr><td class=\"bg-success\">There is nothing wrong with this sitemap</td></tr>";
        }
        
        $("#result-table").html(table);
    });
});
EOS
);
