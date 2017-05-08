
## Описание установки и настройки решения

- в настройках задать список полей, которые будут добавляться в событие формы обратной связи;
- добавить в шаблоне компонента теги для возможности ввода поля с кодом;
- создать почтовый шаблон и добавить поля в шаблоне сообщения, например USER_PHONE из формы подставляется как #USER_PHONE#;
- в настройках компонента выбрать нужный почтовый шаблон;
- выбрать список полей которые будут сохранятся в csv;
- для импорта лидов в Bitrix24 - задать список соответствий полей формы и полей лида, TITLE для лида берется из почтового шаблона формы.

### Пример вставки полей "Номер телефона" и "Регион" в шаблоне компонента

    <input name="USER_REGION" 
        value="<?= htmlspecialchars($_POST["USER_REGION"]) ?>"
        placeholder="Регион">

    <input name="USER_PHONE" 
        value="<?= htmlspecialchars($_POST["USER_PHONE"]) ?>" 
        placeholder="Ваш номер телефона">

### Пример редиректа для конкретной формы в component_epilog.php

    if (strlen($arResult["OK_MESSAGE"]) > 0) {
       LocalRedirect("/thank_you/");
    }
