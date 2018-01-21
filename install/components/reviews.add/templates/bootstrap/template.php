<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$this->setFrameMode(true);
?>

<?if (isset($_SESSION['__TRAVELSOFT']['REVIEWS_MESS_OK'])):?>
<div id="add-review-success-message" class="alert alert-success"><?= GetMessage($_SESSION['__TRAVELSOFT']['REVIEWS_MESS_OK'])?></div>
<?endif?>

<div class="row">
    <div class="text-center col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <button role="button" data-toggle="modal" data-target="#add-review-modal" id="add-review-btn" class="btn btn-primary">Оставить отзыв</button>
    </div>
</div>

<div class="modal fade" id="add-review-modal" tabindex="-1" role="dialog" aria-labelledby="add-review" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="add-review">Оставить отзыв</h4>
            </div>
            <form role="form" action="<?= $APPLICATION->GetCurPage(false) ?>" method="post">
                <?= bitrix_sessid_post()?>
                <div class="modal-body">
                    <?if (!empty($arResult['ERRORS'])): ?>
                        <div class="alert alert-danger">
                        <?foreach ($arResult['ERRORS'] as $label) :?>
                            <p><?= GetMessage($label)?></p>
                        <?endforeach;?>
                        </div>
                    <?endif?>
                    <? if (!$USER->IsAuthorized()): ?>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input value="<?= htmlspecialchars($_POST['email']) ?>" name="email" type="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль</label>
                            <input name="password" type="password" class="form-control">
                        </div>
                        <div class="form-group hidden">
                            <label for="password">Подтверждение пароля</label>
                            <input disabled="" name="confirm_password" type="password" class="form-control">
                        </div>
                        <div class="text-right">
                            <a href="javascript:void(0)" id="toggle-ar" data-action="registration">Зарегистрироваться</a>
                        </div>
                        <div class="form-group">
                            <label for="captcha_word">Введите слово с картинки</label>
                            <div class="captcha-img-box">
                                <input type="hidden" name="captcha_sid" value="<?= $arResult['CAPTCHA_CODE'];?>">
                                <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult['CAPTCHA_CODE'];?>" alt="CAPTCHA">
                            </div>
                            <input type="text" class="form-control" name="captcha_word">
                        </div>
                    <? endif ?>
                    <?if ($arParams['SHOW_RATING_FIELD'] === 'Y'):?>
                    <label for="rating">Оценка</label>
                    <div class="form-group">
                        <div id="raty-ar"></div>
                    </div>
                    <?endif?>
                    <? if ($arParams['SHOW_ADVANTAGES_FIELD'] === 'Y'): ?>
                        <div class="form-group">
                            <label for="advantages">Достоинства</label>
                            <input class="form-control" name="advantages" value="<?= htmlspecialchars($_POST['advantages']) ?>" type="text">
                        </div>
                    <? endif ?>
                    <? if ($arParams['SHOW_LIMITATIONS_FIELD'] === 'Y'): ?>
                        <div class="form-group">
                            <label for="limitations">Недостатки</label>
                            <input class="form-control" name="limitations" value="<?= htmlspecialchars($_POST['limitations']) ?>" type="text">
                        </div>
                    <? endif ?>
                    
                    <div class="form-group">
                        <label for="review">Отзыв</label>
                        <textarea name="review" class="form-control"><?= htmlspecialchars($_POST['review']) ?></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_review" value="add_review" class="btn btn-success">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?$this->addExternalJs($templateFolder . "/_script.js")?>
<script>
    
    window.reviewsAddJsParameters = {
      messages: {
          registration: "Зарегистрироваться",
          authorize: "Авторизоваться"
      },
      raty: {
          init: <?if ($arParams['SHOW_RATING_FIELD'] === 'Y'):?>true<?else:?>false<?endif?>,
          score: <?= (int)$_POST['rating']?>,
          number: <?= Bitrix\Main\Config\Option::get("travelsoft.reviews", "MAX_RATING_VALUE")?>,
      },
      triggerReviewModal: <?if (!empty($arResult['ERRORS'])):?>true<?else:?>false<?endif?>,
      scrollToSuccessMessage: <?if ($_SESSION['__TRAVELSOFT']['REVIEWS_MESS_OK']):?>true<?else:?>false<?endif?>
    };
    
</script>

<?unset($_SESSION['__TRAVELSOFT']['REVIEWS_MESS_OK']); ?>