<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$this->setFrameMode(true);
?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <button role="button" data-toggle="modal" data-target="#add-review-modal" class="btn btn-success">Оставить отзыв</button>
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
                    <?if (!empty($arResult['ERRORS'])):?>
                        <div class="alert alert-danger">
                        <?foreach ($arResult['ERRORS'] as $label => $text) :?>
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
                    <button type="submit" name="add_review" value="add_review" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?$this->addExternalJs($templateFolder . "/review_add.js")?>
<script>
    // add review parameters (arp)
    if (typeof window.arp) {
        window.arp = {};
    }
    
    window.arp = {
      messages: {
          registration: "Зарегистрироваться",
          authorize: "Авторизоваться"
      },
      raty: {
          init: <?if ($arParams['SHOW_RATING_FIELD'] === 'Y'):?>true<?else:?>false<?endif?>,
          score: <?= (int)$_POST['rating']?>,
          number: 5
      }
    };
</script>