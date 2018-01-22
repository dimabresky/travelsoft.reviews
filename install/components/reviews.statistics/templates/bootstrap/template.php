<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$this->setFrameMode(true);
if (!$arResult["STATISTICS"]["total_count"]) {
    return;
}
?>

<div class="text-center ">
    <div itemscope itemtype="http://schema.org/AggregateRating" class="reviews-rating panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <?
                    foreach ($arResult["STATISTICS"]["stars"] as $r_stars):
                        $isBest = "";
                        if ($r_stars["stars"] == Bitrix\Main\Config\Option::get("travelsoft.reviews", "MAX_RATING_VALUE")) {
                            $isBest = 'itemprop="bestRating"';
                        }
                        $isWorst = "";
                        if ($r_stars["stars"] == 1) {
                            $isWorst = 'itemprop="worstRating"';
                        }
                        ?>
                        <div class="row row-flex row-flex-wrap stat-row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 flex-col stars-box text-right">
                                <div data-stars="<?= $r_stars["stars"] ?>" class="stars"></div>
                                <div class="hidden" <?= $isBest ?><?= $isWarst ?>><?= $r_stars["stars"] ?></div>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 flex-col text-center rating-scale">
                                <div style="width: <?= $r_stars["percent"] ?>%" class="rating-fill"></div>
                                <div class="rating-value"><b><?= $r_stars["percent"] ?>%</b></div>
                            </div>
                        </div>
                    <? endforeach ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
                    <div itemprop="ratingValue" class="middle-rating-value">
                        <?= $arResult["STATISTICS"]["middle"] ?>
                    </div>
                    <div data-stars="<?= $arResult["STATISTICS"]["middle"] ?>" class="stars"></div>
                    <div class="total-reviews-count">Всего отзывов: <b itemprop="reviewCount"><?= $arResult["STATISTICS"]["total_count"] ?></b></div>
                </div>
            </div>
        </div>
    </div>
</div>
<? $this->addExternalJs($templateFolder . "/_script.js") ?>

<script>

    window.reviewsStatisticsJsParameters = {
        total_stars_count: <?= Bitrix\Main\Config\Option::get("travelsoft.reviews", "MAX_RATING_VALUE") ?>
    };

</script>
