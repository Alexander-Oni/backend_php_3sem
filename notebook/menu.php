<?php
  // защита от прямого доступа в браузере, выдаем ошибку запрета доступа и прерываем выполнение файла
  if (!defined('APP'))
  {
    http_response_code(403); 
    exit;
  }

  function get_menu()
  {
    // проверкуа на активную страницу
    $page = isset($_GET['page']) ? $_GET['page'] : 'view';

    // проверка параметров сортировки
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created';

    // массив пунктов меню
    $pages = [
      'view' => 'Просмотр',
      'add' => 'Добавление записи',
      'edit' => 'Редактирование записи',
      'delete' => 'Удаление записи'
    ];

    // проверка существования страницы, если нет - подставляем стандартную ('view')
    if (!in_array($page, array_keys($pages), true))
    {
      $page = 'view';
    }

    // начало буферизации вывода
    ob_start();

    echo '<div class="menu">';

    foreach ($pages as $key => $label)
    {
      $class = 'menu-link'; // базовый класс ссылки меню

      if ($page === $key)
      {
        $class .= ' active';
      }

      // вывод ссылки на страницу 
      echo '<a class="' . $class . '" href="index.php?page=' . $key . '">' . htmlspecialchars($label) . '</a>';
    }

    echo '</div>';

    if ($page === 'view')
    {
      $sortItems = [
        'created' => 'По дате добавления',
        'last_name' => 'По фамилии',
        'birthdate' => 'По дате рождения',
      ];

      echo '<div class="submenu">';

      foreach ($sortItems as $key => $label)
      {
        $class = 'submenu-link';

        // перебор вариантов сортировки
        if ($sort === $key) 
        {
          $class .= ' active';
        }
        
        // вывод ссылки сортировким
        echo '<a class="' . $class . '" href="index.php?page=view&sort=' . $key . '">' . htmlspecialchars($label) . '</a>';
      }

      echo '</div>';
    }

  // возврат и очистка накопленного буфера 
  return ob_get_clean();
  }
?>