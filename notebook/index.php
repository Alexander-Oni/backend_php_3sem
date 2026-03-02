<?php
  // флаг для проверки точки входа, защищает файлы от прямого вызова через браузер
  define(constant_name: 'APP', value: true);

  require_once 'config.php'; // логика работы с бд

  require_once 'menu.php'; // генерация внешнего вида меню

  // пишем логику опрделения того, что хочет от нас получить пользователь
  $page = isset($_GET['page']) ? $_GET['page'] : 'view';

  // список доступных пользователю страниц
  $allowed = ['view', 'add', 'edit', 'delete'];

  // проверка принадлежности запрашиваемой страницы к доступным страницам
  if (!in_array($page, $allowed, true))
  {
    $page = 'view';
  }
?>
<!-- общая разметка для всех страниц -->
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Записная книжка</title>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
  <h1>Записная книжка</h1>
  <main>
    <!-- здесь будет логика перехода по страницам -->
    <?php
    echo get_menu();

    echo '<hr>';

    switch ($page) 
    {
      case 'add':
        require 'add.php';
        break;
    
      case 'edit':
        require 'edit.php';
        break;
      
      case 'delete':
        require 'delete.php';
        break;
      /*
      default:
        require_once 'viewer.php';
        break;
      */
    }
    ?>
  </main>
</body>
</html>


