<?php
  // защита от прямого доступа в браузере, выдаем ошибку запрета доступа и прерываем выполнение файла
  if (!defined('APP')) {
    http_response_code(403);
    exit;
  }

  $pdo = get_pdo();
?>

<h2>Удаление контакта</h2>

<?php

  // переменная для вывода сообщения пользователю
  $message = '';

  // проверка предачи id контакта в GET-параметрах
  if (isset($_GET['id']))
  {
    $id = (int)$_GET['id']; // приведение id к целому числу

    // получение фамилии
    $stmt = $pdo->prepare("SELECT last_name FROM contacts WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    // проверка нахождения контакта
    if ($row)
    {
      $lastName = $row['last_name']; // сохранение фамилии
      $del = $pdo->prepare("DELETE FROM contacts WHERE id = :id"); //подготвление запроса
      $del->execute([':id' => $id]); // удаление

      $message = '<p class="success">Контакт '. htmlspecialchars($lastName) . ' удален.</p>';
    }
    else
    {
      $message = '<p class="error">Контакт не найден.</p>';
    }
  }

  echo $message;

  // запрос всех контактов для отоброажения
  $stmt = $pdo->query("
    SELECT id, last_name, first_name, middle_name
    FROM contacts
    ORDER BY last_name ASC, first_name ASC
  ");

  $contacts = $stmt->fetchAll();

  if (!$contacts) 
  {
    echo '<p>Контактов пока нет.</p>';
    return;
  }

  // вывод всех контактов в виде списка
  echo '<ul>';

  foreach ($contacts as $c)
  {
    $href = 'index.php?page=delete&id=' . (int)$c['id'];

    $text = trim($c['last_name'] . ' ' . $c['first_name'] . ' ' . $c['middle_name']);

    echo '<li><a href="' . $href . '">' . htmlspecialchars($text) . '</a></li>';
  }

  echo '</ul>';
?>
