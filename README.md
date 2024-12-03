**Консольное приложение для формирования ежемесячного отчета с указанием затраченного времени на выполнение каждой задачи из weeek.net**

Console app for generation monthly report with elapsed time per tasks from weeek.net

Генерирует отчет за указанный период в формате XLSX

![image](https://github.com/user-attachments/assets/09585b7b-c914-44e5-a6e3-76abe8dc80aa)

**Как пользоваться**

Склонировать, создать файл .env, прописать в нём токен API WEEEK_WS_TOKEN, в консоли выполнить команду (см. примеры)

**Примеры команд**

Генерация отчета по умолчанию (за прошлый месяц)

`php console.php app:create-timereport ИЛИ php console.php tr`

Генерация отчета за прошлый месяц:

`php console.php tr prev `

Генерация отчета за текущий месяц:

`php console.php tr cur `

Генерация отчета за 6 месяц текущего года

`php console.php tr 6 `

Генерация отчета за 6 месяц 2020 года

`php console.php tr 6 2020`


Имейте в виду, что на текущий момент API weeek.net отдаёт затраченное время по задачам в минутах, т.е. секунды теряются и получается погрешность в минуты/десятки минут.
