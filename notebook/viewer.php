<?php
  // защита от прямого доступа в браузере, выдаем ошибку запрета доступа и прерываем выполнение файла
  if (!defined('APP')) 
  {
    http_response_code(403);
    exit;
  }

  function get_viewer($sort, $page)
  {
    $pdo = get_pdo();

    $rowPerPage = 10; // количество записей на одной странице

    // определение поля для сортировки
    switch ($sort)
    {
      case 'last_name':
        $orderBy = 'last_name ASC, first_name ASC';
        break;

      case 'birthdate':
        $orderBy = 'birthdate ASC';
        break;

      default:
        $orderBy = 'id ASC';
        $sort = 'created';
        break;
    }

    // получение количесвта записей в бд
    $stmt = $pdo->query('SELECT COUNT(*) FROM contacts');
    $total = (int)$stmt->fetchColumn();

    if ($total === 0) 
    {
      return '<p>Записей пока нет.</p>';
    }

    // подсчет количества страниц
    $totalPages = (int)ceil($total / $rowPerPage);

    // валидация номера страницы
    if ($page < 1) 
    {
      $page = 1;
    } 
    elseif ($page > $totalPages) 
    {
      $page = $totalPages;
    }

    // подсчет смещения отобржения строк, сколько записей нужно пропустить
    $offset = ($page - 1) * $rowPerPage;

    // подготовлление и выполнение запроса для выборки
    $sql = "SELECT * FROM contacts ORDER BY $orderBy LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $rowPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // получаем все строки
    $rows = $stmt->fetchAll();

    // начало буферизации
    ob_start();

    echo '<table border="1" cellpadding="5" cellspacing="0">';
    // заголовки столбцов
    echo '<tr>';

    echo '<th>Фамилия</th>';
    echo '<th>Имя</th>';
    echo '<th>Отчество</th>';
    echo '<th>Пол</th>';
    echo '<th>Дата рождения</th>';
    echo '<th>Телефон</th>';
    echo '<th>Адрес</th>';
    echo '<th>E-mail</th>';
    echo '<th>Комментарий</th>';

    echo '</tr>';

    // вывод строк таблицы
    foreach ($rows as $row)
    {
      echo '<tr>';

      echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
      echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
      echo '<td>' . htmlspecialchars($row['middle_name']) . '</td>';
      echo '<td>' . htmlspecialchars($row['gender']) . '</td>';
      echo '<td>' . htmlspecialchars($row['birthdate']) . '</td>';
      echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
      echo '<td>' . htmlspecialchars($row['address']) . '</td>';
      echo '<td>' . htmlspecialchars($row['email']) . '</td>';
      echo '<td>' . nl2br(htmlspecialchars($row['comment'])) . '</td>'; // nl2br для сохранения переносов

      echo '</tr>';
    }

    echo '</table>';

    // блок пагинации
    if ($totalPages > 1) 
    {
      echo '<div class="pagination">';

      for ($i = 1; $i <= $totalPages; $i++) 
      {
        $class = 'page-link';

        if ($i === $page) 
        {
          $class .= ' active';
        }

        $href = 'index.php?page=view&sort=' . $sort . '&p=' . $i;

        echo '<a class="' . $class . '" href="' . $href . '">' . $i . '</a>';
      }

      echo '</div>';
    }

    return ob_get_clean();
  }