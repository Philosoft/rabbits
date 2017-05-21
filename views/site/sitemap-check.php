<?php

/**
 * @var \yii\web\View $this
 */

use app\controllers\SitemapController;
use \yii\helpers\Html;

$this->title = Yii::t("app", "Sitemap check");
$this->params["breadcrumbs"][] = $this->title;
\app\assets\LoadersAsset::register($this);
$domain = Yii::$app->request->get("domain", "");
?>

<h1><?= Html::encode($this->title) ?></h1>

<form class="form-inline" id="check-sitemap-form">
  <div class="form-group">
    <label for="domain">Domain:</label>
    <input type="text" class="form-control" name="domain" id="domain" placeholder="https://example.com" value="<?= $domain ?>">
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

<?php
$reasonRobots = SitemapController::REASON_ROBOTS;
$reasonMetaRobots = SitemapController::REASON_META_ROBOTS;
$reasonNot200 = SitemapController::REASON_NOT_200;
$this->registerJs(<<<EOS
var megaInterval = undefined;

function printReason(reason) {
    var reasons = {
        $reasonRobots: "robots.txt",
        $reasonMetaRobots: "meta robots",
        $reasonNot200: "response was not 200"
    };

    if (reasons[reason] != undefined) {
        return reasons[reason];
    }
}

$("#check-sitemap-form").submit(function (e) {
    e.preventDefault();
    $(".backdrop").show();
    $.ajax({
        "type": "post",
        "url": "/sitemap/check",
        "data": $(this).serialize()
    })
    .done(function (response) {
        let table = "";
        let firstRowClass = "bg-success";
        let firstRowContent = "";

        if (response.status == "ok") {
            firstRowContent = "Parsing is successful";
        } else if (response.message.length > 0) {
            firstRowClass = "bg-danger";
            firstRowContent = response.message;
        } else {
            firstRowClass = "bg-danger";
            firstRowContent = "Что-то пошло не так";
        }
        
        table += '<tr><td colspan="2" class="' + firstRowClass + '">'
                    + firstRowContent +
                '</td></tr>';

        if (response.data.length > 0) {
            table += '<tr><td colspan="2" class="bg-warning">These urls are dissalowed</td></tr><tr><th>url</th><th>reason</th></tr>';
            response.data.forEach(function (url) {
                table += "<tr><td>" + url.url + "</td><td>" + printReason(url.reason) + "</td></tr>";
            });
        } else {
            table += '<tr><td colspan="2" class="bg-info">В карте сайта нет запрещённых урлов</td></tr>';
        }
        
        $("#result-table").html(table);
    })
    .always(function (response) {
        $(".backdrop").hide();
        $("#tesult-table").html("<tr><td colspan=\"2\" class=\"bg-danger\">" + response.message + "</td></tr>");
    });
});
EOS
);
