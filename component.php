<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Mail\Event;
use App\Helpers\Helper;

Loader::includeModule("form");

$request = Context::getCurrent()->getRequest();

if ($request->isPost()) {
    $postData = $request->getPostList()->toArray();

    /**
     * Функция для отправки JSON-ответа
     */
    function sendJsonResponse($response) {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json; charset=' . LANG_CHARSET);
        echo Json::encode($response, JSON_BIGINT_AS_STRING);
        die();
    }

    // Проверяем обязательные поля
    if (empty($postData['email']) || empty($postData['phone']) || empty($postData['message'])) {
        $response = ["status" => "error", "message" => "Заполните все обязательные поля!"];
        sendJsonResponse($response);
    }

    //Обработка загруженных файлов
    if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {

        $fileArray = [
            "name" => $_FILES['attachment']['name'],
            "size" => $_FILES['attachment']['size'],
            "tmp_name" => $_FILES['attachment']['tmp_name'],
            "type" => $_FILES['attachment']['type']
        ];

        // Ограничение по размеру файла (макс 10MB)
        if ($fileArray['size'] > 10 * 1024 * 1024) {
            $response = ["status" => "error", "message" => "Размер файла слишком большой!"];
            sendJsonResponse($response);
        }
        // Проверка по разрешенным MIME-типам
        $allowedMimeTypes = [
            'image/jpeg', 'image/png', 'application/pdf',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($fileArray['type'], $allowedMimeTypes)) {
            $response = ["status" => "error", "message" => "Недопустимый формат файла!"];
            sendJsonResponse($response);
        }
    }

    // Сохранение результата в веб-форме
    $formId = 4;
    $resultId = (new CFormResult)->Add($formId, [
        "form_text_34" => $postData['email'],
        "form_text_33" => $postData['phone'],
        "form_textarea_35" => Helper::toWindows1251($postData['message']),
        "form_file_36" => $_FILES['attachment']
    ]);

    if ($resultId) {
        $data = (new CFormResult)->GetDataByIDForHTML($resultId);
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        // Отправка почтового уведомления
        Event::send([
            "EVENT_NAME" => "FEEDBACK_FORM",
            "LID" => "s1",
            "C_FIELDS" => [
                "ID" => $resultId,
                "DATE" => date('d.m.Y H:i'),
                "LS" => $PERSONAL_ACCOUNT->GetNum(),
                "EMAIL" => $postData['email'],
                "PHONE" => $postData['phone'],
                "MESSAGE" => Helper::toWindows1251($postData['message']),
                "FILES" => $data['form_file_36'] ? $url . CFile::GetPath($data['form_file_36']) : '',
                "ADMIN_LINK" => $url . "/bitrix/admin/form_result_view.php?RESULT_ID=$resultId&WEB_FORM_ID=$formId"
            ]
        ]);

        $response = ["status" => "success", "message" => "Форма успешно отправлена!"];
    } else {
        $response = ["status" => "error", "message" => "Ошибка при сохранении формы"];
    }
    sendJsonResponse($response);
}

$this->IncludeComponentTemplate();
