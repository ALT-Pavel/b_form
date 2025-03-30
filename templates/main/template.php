<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */
use Bitrix\Main\Page\Asset;
$asset = Asset::getInstance();
$asset->addCss(STP . "/css/form.css");
?>

<form id="feedback" class="form profile-main__form" method="POST" enctype="multipart/form-data">
    <div class="profile-main__form-row">
        <p id="form_errors"></p>
    </div>
    <div class="profile-main__form-row">
        <label class="profile-main__form-label" for="personal-phone">Телефон *</label>
        <input class="profile-main__form-input form-control phone-mask" id="personal-phone" name="phone" type="text" value="" placeholder="Введите ваш телефон" required="">
    </div>
    <div class="profile-main__form-row">
        <label class="profile-main__form-label" for="personal-email">Email *</label>
        <input class="profile-main__form-input form-control" id="personal-email" type="email" name="email" value="" placeholder="Введите ваш Email" required="">
    </div>
    <div class="profile-main__form-row row-textarea">
        <label class="profile-main__form-label" for="personal-connection-text">Обращение *</label>
        <textarea class="profile-main__form-input form-control" height="215" id="personal-connection-text" name="message" placeholder="Введите текст обращения" required=""></textarea>
    </div>
    <div class="profile-main__form-row">
        <label class="profile-main__form-label" for="personal-file">Добавить файл</label>
        <input class="profile-main__form-input form-control" id="personal-file" type="file" name="attachment">
    </div>
    <button class="btn btn-orange profile-main__form-btn" type="submit">Отправить</button>
</form>

<div id="modal-overlay" class="modal-overlay">
    <div class="modal-content-accept">
        <button type="button" class="close-modal">
            <img src="/local/img/esc.png" alt="exit">
        </button>

        <img src="/local/img/accept.png" alt="accept">

        <h2>Обращение отправлено</h2>

        <p>Текст</p>
    </div>
</div>
