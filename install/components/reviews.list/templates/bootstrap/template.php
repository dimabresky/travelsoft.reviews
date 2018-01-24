<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$this->setFrameMode(true);

if (!$arResult["ITEMS"]) {
    return;
}
?>
<div id="reviews-list">
    <?
    if (defined("TRAVELSOFT_REVIEWS_AJAX_CALL") && TRAVELSOFT_REVIEWS_AJAX_CALL === TRUE) {
        $APPLICATION->RestartBuffer();
    }
    foreach ($arResult["ITEMS"] as $arItem):
        $h = md5("ts_" . $arItem["ID"]);
        ?>
        <div class="panel panel-default">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-center">
                    <div class="avatar"><img src="<?= $arItem["USER"]["AVATAR"] ?>" alt="<?= $arItem["USER"]["EMAIL"] ?>"></div>
                    <div class="email"><?= $arItem["USER"]["EMAIL"] ?></div>
                    <div class="date-create"><?= $arItem["DATE_CREATE"] ?></div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">

                    <div class="user-toolbar">
                        <span id="review-rating-<?= $h ?>" data-stars="<?= $arItem["RATING"] ?>" class="review-rating"></span>
                        <? if (!empty($arItem["PICTURES"])) : ?>

                            <a data-fancybox="gallery-<?= $h ?>" id="gallery-<?= md5("ts_" . time() . $arItem["PICTURES"][0]["ID"]) ?>" class="review-gallery" href="<?= $arItem["PICTURES"][0]["SRC"] ?>">Галерея</a>
                            <span class="hidden">
                                <? if ($arItem["PICTURES"][1]): ?>
                                    <? for ($i = 1; $i < count($arItem["PICTURES"]); $i++): ?>
                                        <a data-fancybox="gallery-<?= $h ?>" id="gallery-<?= md5("ts_" . time() . $arItem["PICTURES"][$i]["ID"]) ?>" class="review-gallery" href="<?= $arItem["PICTURES"][$i]["SRC"] ?>"></a>
                                    <? endfor ?>
                                <? endif ?>
                            </span>

                        <? endif ?>
                    </div>
                    <div class="review-text"><?= $arItem["REVIEW_TEXT"]?></div>
                </div>
            </div>
        </div>
        <?
    endforeach;
    if (defined("TRAVELSOFT_REVIEWS_AJAX_CALL") && TRAVELSOFT_REVIEWS_AJAX_CALL === TRUE) {
        die;
    }
    ?>    
</div>

<? $this->addExternalJs($templateFolder . "/_script.js", true) ?>
<script>
window.reviewsListJsParameters = {
        total_stars_count: <?= Bitrix\Main\Config\Option::get("travelsoft.reviews", "MAX_RATING_VALUE") ?>
    };
</script>
