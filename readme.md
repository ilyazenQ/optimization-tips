## Optimization tips

Проект создан с целью демонстрации распространенных ошибок оптимизации, методов их поиска и устранения.<br>
Репозиторий содержит ветки, main всегда будет содержать самый последний или самый актуальный код для проекта. Ветка basic-without-optimization содержит первичный код проекта до оптимизации. Остальные ветки содержат поэтапный рефакторинг соответствующий статьям.  

## Описание проекта

Рассматриваемый проект - веб-журнал посещений различных мест людьми и имеет функционал выгрузки и загрузки журнала в формате XML.<br> Пользователь может загрузить свой журнал посещений в формате XML через форму (/upload) и по информации из файла будет заполнена бд. На главной странице (/index) будет выводиться вся информации о посещениях. Экспорт из системы осуществляется через команду, которая преобразует информацю из системы в формат xml и формирует файл (data.xml) в корне проекта.

## Разворот

1. `git clone {project}`<br>
2. Скопировать `.env copy` -> `.env` в корне проекта и `.env.dist` -> `.env` в папке docker  <br>
3. `make dc_build`<br>
4. `make dc_up`<br>
5. `make app_bash`<br>
6. `composer install`<br>
7. `bin/console doctrine:migrations:migrate`<br>


## Полезные команды

1. `bin/console doctrine:fixtures:load` Заполнение бд тестовыми данными<br>
2. `bin/console index:export:xml` Экспортирует данные (в формате представленном на странице /index) в файл data.xml<br>

