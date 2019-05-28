# CMTT PHP

PHP-класс для работы с api сервисами Комитета [https://cmtt-ru.github.io/osnova-api/redoc.html](https://cmtt-ru.github.io/osnova-api/redoc.html)

## Установка


### Установка через Composer

Запустите

```
php composer.phar require benyazi/cmtt-php "~4"
```

или добавьте

```js
"benyazi/cmtt-php": "~4"
```

в секцию ```require``` вашего composer.json

## Использование


```php
$client = new \Benyazi\CmmtPhp\Api(\Benyazi\CmmtPhp\Api::TJOURNAL);
```

Получения данных о пользователе:

```php
$userId = 27100;
$userData = $client->getUser($userId);

```


## Автор

[Sergey Klabukov](https://github.com/benyazi/), e-mail: [yo@benyazi.ru](mailto:yo@benyazi.ru)
