Boardroom
=========
GeeksForLess тестовый php проект, переделанный на Yii 2. Учебный проект, выполненный с целью освоения Yii 2.
Система предназначена для бронирования переговорных комнат.
Функции:
- осуществляет менеджмент учетных записей пользователей;
- прием и модификацию бронирования переговорной комнаты.

Исходное задание содержало условие не использовать сторонних библиотек. Данный проект, помимо использования php фреймворка, содержит сторонние интерфейсные элементы (поле ввода даты и времени), а так же использует ajax и модальные элементы из Bootstrap, как более соответсвующие современным техникам создания пользовательского интерфейса.
Проект содержит определенные недоработки: не введены ограничения на доступ к учетным данным пользователей со стороны не обладающих административными правами пользователей, отсутствует функция удаления букинга. Цель проекта - освоение фреймворка, поэтому недостатки будут исправлены лишь при потребности (хотя, ограничение доступа следует все же доделать с целью освоения RBAC авторизации).
[Исходный прототип можно увидеть здесь](https://github.com/anddorua/boardroom)

СИСТЕМНЫЕ ТРЕБОВАНИЯ
--------------------

PHP 5.5.9., MySQL | MariaDB


ИНСТАЛЛЯЦИЯ
-----------

Далее идет инструкция для [CentOS 7](https://virtualboxes.org/images/centos/), для которой тестировалась инсталляция от имени root пользователя. Хороший туториал по начальной установке веб-компонентов и их настройке можно найти [здесь](http://i-leon.ru/ustanovka-i-nastrojka-apache-php-mysql-na-centos-pma-i-ftp/).

### Создаем каталог для сайта
Исходный каталог /home
~~~
[root@localhost home]# mkdir yiib
[root@localhost home]# cd yiib
~~~

### Копируем проект в текущий каталог
~~~
git clone https://github.com/anddorua/yii-boardroom .
~~~

### Создаем базу данных
На сервере MySQL создайте пустую базу данных и пользователя с правами на создание таблиц и работу с ними.
    Пример создания базы и пользователя:
    зайти в MySQL: ```mysql -u root -p<root_password>```
~~~
create database yiib;
create user 'yiib'@'localhost' identified by 'yiib';
grant all privileges on yiib.* to 'yiib'@'localhost';
flush privileges;
~~~

### Конфигурируем подключение к базе данных.

   Редактируем `config/db.php` с реальными данными для подключения:

   ```php
   return [
       'class' => 'yii\db\Connection',
       'dsn' => 'mysql:host=localhost;dbname=yiib',
       'username' => 'yiib',
       'password' => 'yiib',
       'charset' => 'utf8',
   ];
   ```

### Обновляем PHP
Некоторые компоненты требуют PHP >= 5.5.9, так-что если у вас более ранняя версия, потребуется ее обновить до 5.6. Проверить можно ```yum list installed | grep "php"```. Инструкцию по обновлению см. [здесь](http://devdocs.magento.com/guides/v2.0/install-gde/prereq/php-centos.html), только предварительно надо удалить старую версию php ```yum -y remove php php-common php-gd php-xml php-mbstring```


### Устанавливаем Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

### Инсталлируем плагин
~~~
php composer.phar global require "fxp/composer-asset-plugin:~1.1.1"
~~~

### Устанавливаем зависимости проекта
~~~
php composer.phar install
~~~

### Применяем миграцию к базе данных
~~~
php yii migrate
~~~

### Прописываем VirtualHost сервера
[см. здесь](http://i-leon.ru/ustanovka-i-nastrojka-apache-php-mysql-na-centos-pma-i-ftp/)

### Заходим на сервер
Логин _admin_, пароль пустой.


