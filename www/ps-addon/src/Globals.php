<?php

/*
 * Максимальная длина сообщения к изображению
 */
define('CROP_MSG_MAX_LEN', 240);

/*
 * Код api из VK
 */
define('CROP_VK_API_ID', 5283901);

/*
 * Страницы
 */
define('CROP_PAGE_ADD', 1000);

/*
 * 
 */
define('CROP_PAGE_CELL', 1001);

/*
 * 
 */
define('CROP_PAGE_INFO', 1002);

/*
 * Настройки для блока <a href="https://tech.yandex.ru/share/" target="_blank">YA SHARE</a>.
 */
define('CROP_YA_SHARE_ENABED', true);

/*
 * URL для соцсетей
 */
define('CROP_YA_SHARE_URL', 'http://uflow.ru');

/*
 * Заголовок для соцсетей
 */
define('CROP_YA_SHARE_TITLE', 'Публикатор мыслей');

/*
 * Описание ссылки для соцсетей
 */
define('CROP_YA_SHARE_DESCRIPTION', 'Поделись своими эмоциями со всем Миром');

/*
 * Картинка для соцсетей
 */
define('CROP_YA_SHARE_IMAGE', 'http://uflow.ru/i/socshare.png');

/*
 * Почтовый адрес техподдержки
 */
define('CROP_SUPPORT_MAIL', 'support@uflow.ru');

/*
 * Сервисы, подключаемые в Ya share: vkontakte, facebook, odnoklassniki, moimir, gplus, twitter, linkedin, lj, viber, whatsapp, blogger, pocket, qzone, reddit, evernote, renren, sinaWeibo, surfingbird, tencentWeibo, tumblr, digg
 */
define('CROP_YA_SHARE_SERVICES', 'vkontakte, facebook, odnoklassniki, moimir, gplus, twitter, linkedin, lj, viber, whatsapp, evernote, blogger, pocket, qzone, reddit');

/*
 * Количество соцсетей, отображаемых в виде кнопок. Используется если нужно встроить в блок много соцсетей, а также чтобы блок занимал мало места на странице. Не вошедшие в лимит соцсети будут доступны по нажатию кнопки.
 */
define('CROP_YA_SHARE_SERVICES_LIMIT', 8);

/*
 * Включена ли работа с эмоциями (выбор эмоции для сообщения, подсчёт статистики)
 */
define('CROP_USE_EMOTIONS', true);

/*
 * Показывать ли статистику распределения эмоций
 */
define('CROP_EM_STATISTIC_PIE', true);

/*
 * Включена ли возможность добавления новых сообщений
 */
define('CROP_ADD_CELL_ENABLED', true);

/*
 * Публичный ключ для рекапчи
 */
define('CROP_CAPTCHA_PUBLIC', '6LcOV7sSAAAAAH2aeJQF2FzYWRzfeNvOsKfkDqiS');

/*
 * Привытный ключ для рекапчи
 */
define('CROP_CAPTCHA_PRIVATE', '6LcOV7sSAAAAAC3cuTNS55MAbjpO8uioFZF8p-u2');

?>