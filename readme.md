# CMTT PHP

PHP-класс для работы с api сервисами Комитета [https://cmtt-ru.github.io/osnova-api/redoc.html](https://cmtt-ru.github.io/osnova-api/redoc.html)

## Установка


### Установка через Composer

Запустите

```
php composer.phar require benyazi/cmtt-php
```

или добавьте

```js
"benyazi/cmtt-php": "dev-master"
```

в секцию ```require``` вашего composer.json

## Реализованно

На данный момент реализована обертка для части функций:
- getUser - Получить информацию о пользователе
- getTimeline - Возвращает ленту записей
- getTimelineByHashtag - Получить ленту записей по хэштегу
- getEntryById - Получить запись по ID
- getPopularEntries - Получить популярные записи для определенной записи
- getEntryComments - Получить комментарии к записи
- getCommentLikes - Получить список лайкнувших комментарий
- getUserComments - Получить комментарии пользователя
- getUserEntries - Получить записи пользователя
- sendComment - Отправка комментария (без вложений)
- like - Лайк, дизлайк комментария или статьи

## Использование


```php
$client = new \Benyazi\CmttPhp\Api(\Benyazi\CmttPhp\Api::TJOURNAL);
```

Получения данных о пользователе:

```php
$userId = 27100;
$userData = $client->getUser($userId);

```

## Использование с токеном авторизации


```php
$client = new \Benyazi\CmttPhp\Api(\Benyazi\CmttPhp\Api::TJOURNAL, 'TOKEN_FOR_USER');
```

Отправка комментария:

```php
//ID статьи
$contentId = 99328;
//Текст комментария
$commentText = "Раз-раз, проверка";
$commentData = $client->sendComment($contentId, $commentText);

//ID комментария, на который отправляется ответ
$replyTo = 2472464;
$commentText = "Раз-раз, проверка, ответа на другой комментарий";
$commentData = $client->sendComment($contentId, $commentText, $replyTo);
```

Лайк комментария:

```php
//ID статьи или комментария
$commentId = 2471797;
$commentData = $client->like($commentId, \Benyazi\CmttPhp\Api::LIKE_TYPE_COMMENT);

//ID статьи или комментария для дислайка
$commentId = 2471797;
$commentData = $client->like($commentId, \Benyazi\CmttPhp\Api::LIKE_TYPE_COMMENT, \Benyazi\CmttPhp\Api::SIGN_DISLIKE);
```

## Использование вселения (авторизации из под подсайта)

Лайк комментария:

```php
$client = new \Benyazi\CmttPhp\Api(\Benyazi\CmttPhp\Api::TJOURNAL, 'TOKEN_FOR_USER');
```
```php
//ID статьи или комментария
$commentId = 2471797;
//ID site
$siteId = 1000;
$commentData = $client->authPossess($siteId)->like($commentId, \Benyazi\CmttPhp\Api::LIKE_TYPE_COMMENT);
```

После использования авторизации в подсайте, клиент будет помнить ее, пока не выйти из под подсайта.

```php
$client->logoutPossess();
```
## Автор

[Sergey Klabukov](https://github.com/benyazi/), e-mail: [yo@benyazi.ru](mailto:yo@benyazi.ru)
