﻿
# Дополнительные поля для компонента Форма обратной связи

## Описание решения

Данный модуль позволяет добавлять дополнительные поля для стандартного компонента ["Форма обратной связи" bitrix:main.feedback](https://dev.1c-bitrix.ru/user_help/settings/settings/components_2/main_feedback.php). Так же модуль позволяет добавлять лиды в Bitrix24 или сохранять данные форм в файл.

**Модуль поддерживает разделение данных на уровне доменов, без необходимости настройки многосайтовости и покупки лицензий на дополнительные сайты.**

### Особенности

- данный модуль будет полезен для редакций не включающих модуль Веб-формы (Первый сайт и Старт) и упрощает интеграцию дизайна для разработчика, ускоряет реализацию различных форм - т.к. имеет более простые настройки и гибкие шаблоны
- позволяет реализовывать формы с неограниченным количеством полей с использованием стандартного компонента "Форма обратной связи"
- используется стандартный [api для импорта лидов в Bitrix24](https://dev.1c-bitrix.ru/community/blogs/chaos/crm-sozdanie-lidov-iz-drugikh-servisov.php)

## Описание установки и настройки решения

- в настройках задать список полей, которые будут добавляться в событие формы обратной связи
- добавить в шаблоне компонента теги для возможности ввода поля с кодом
- создать почтовый шаблон и добавить поля в шаблоне сообщения, например USER_PHONE из формы подставляется как #USER_PHONE#
- в настройках компонента выбрать нужный почтовый шаблон
- выбрать список полей которые будут сохранятся в csv
- для импорта лидов в Bitrix24 - задать список соответствий полей формы и полей лида, TITLE для лида берется из почтового шаблона формы.

### Пример добавления параметра списка полей формы в компонент bitrix:main.feedback

Добавить в шаблон компонента [.parameters.php](https://github.com/rivetweb/rodzeta.feedbackfields/blob/master/install/examples/.parameters.php)

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

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, запросы только по [e-mail](mailto:rivetweb@yandex.ru)

[Багрепорты и предложения](https://github.com/rivetweb/rodzeta.feedbackfields/issues)

## Ссылка на демо-версию

[http://villa-mia.ru/](http://villa-mia.ru/)
