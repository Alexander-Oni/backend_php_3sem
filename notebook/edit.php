<?php
  // защита от прямого доступа в браузере, выдаем ошибку запрета доступа и прерываем выполнение файла
  if (!defined('APP')) 
  {
    http_response_code(403);
    exit;
  }

  $pdo = get_pdo();
?>

<h2>Редактирование контакта</h2>

<?php

  // переменная для вывода сообщения пользователю
  $message = '';

  // запрос для вывода существующих записей контакотов
  $stmt = $pdo->query("
    SELECT id, last_name, first_name
    FROM contacts
    ORDER BY last_name ASC, first_name ASC
  ");

  // получение всъ записей в виде массива
  $contacts = $stmt->fetchAll();

  // проверка на наличие записей
  if (!$contacts) 
  {
    echo '<p>Записей пока нет.</p>';
    return;
  }
  // определение записи для редактирования, по умолчанию это запись с id = 0
  $currentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

  $ids = [];
  foreach ($contacts as $c) 
  {
    $ids[] = (int)$c['id'];
  }

  if (!in_array($currentId, $ids, true)) 
  {
    $currentId = (int)$contacts[0]['id'];
  }

  // обработка отправки формы
  if ($_SERVER['REQUEST_METHOD'] === 'POST')
  {
    $postId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // проверка наличиЯ текущего id в списке записей
    if (in_array($postId, $ids, true))
    {
      $currentId = $postId;

      // поля для чтения из запроса
      $fields = [
        'last_name',
        'first_name',
        'middle_name',
        'gender',
        'birthdate',
        'phone',
        'address',
        'email',
        'comment',
      ];

      // берем значения полей из $_POST
      $values = [];
      foreach ($fields as $f) 
      {
        $values[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : '';
      }

      // валидация на наличие имени и фамилии
      if ($values['last_name'] === '' || $values['first_name'] === '') 
      {
        $message = '<p class="error">Ошибка: обязательные поля (имя, фамилия) - пусты</p>';
      }
      else
      {
        // валидация поля гендера
        $gender = $values['gender'];
        if ($gender !== 'M' && $gender !== 'F') 
        {
          $gender = null;
        }

        // попытка обновления записи
        try
        {
          $stmt = $pdo->prepare("
            UPDATE contacts
            SET
                last_name   = :last_name,
                first_name  = :first_name,
                middle_name = :middle_name,
                gender      = :gender,
                birthdate   = :birthdate,
                phone       = :phone,
                address     = :address,
                email       = :email,
                comment     = :comment
            WHERE id = :id
          ");

          $stmt->execute([
            ':last_name'   => $values['last_name'],
            ':first_name'  => $values['first_name'],
            ':middle_name' => $values['middle_name'],
            ':gender'      => $gender,
            ':birthdate'   => $values['birthdate'] ?: null,
            ':phone'       => $values['phone'],
            ':address'     => $values['address'],
            ':email'       => $values['email'],
            ':comment'     => $values['comment'],
            ':id'          => $currentId,
          ]);

          $message = '<p class="success">Данные контакта обновлены.</p>';
        }
        catch (PDOException $e)
        {
          $message = '<p class="error">Ошибка: не удалось обновить данные контакта.</p>';
        }
      }
    }
  }

  //получение актуальных данных из бд
  $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
  $stmt->execute([':id' => $currentId]);
  $current = $stmt->fetch();

  if (!$current) 
  {
    echo '<p class="error">Запись не найдена.</p>';
    return;
  }

  // вывод текущих записей в виде ссылок
  echo '<div class="edit-list">';

  foreach ($contacts as $c) 
  {
    $cls = '';
    if ((int)$c['id'] === $currentId) {
        $cls = 'current-item';
    }

    $href = 'index.php?page=edit&id=' . (int)$c['id']; // переход по id

    echo '<a class="' . $cls . '" href="' . $href . '">' . htmlspecialchars($c['last_name'] . ' ' . $c['first_name']) . '</a> ';
  }

  echo '</div>';

  echo $message;
?>

<form method="post">
  <!-- cкрытое поле с id записи, которую редактируем, для понимания какую запись м ы должны обновить -->
  <input type="hidden" name="id" value="<?php echo (int)$currentId; ?>">

  <div class="field">
    <label for="last_name">Фамилия*</label>
    <input
      type="text"
      name="last_name"
      id="last_name"
      value="<?php echo htmlspecialchars($current['last_name']); ?>"
    >
  </div>

  <div class="field">
    <label for="first_name">Имя*</label>
    <input
      type="text"
      name="first_name"
      id="first_name"
      value="<?php echo htmlspecialchars($current['first_name']); ?>"
    >
  </div>

  <div class="field">
    <label for="middle_name">Отчество</label>
    <input
      type="text"
      name="middle_name"
      id="middle_name"
      value="<?php echo htmlspecialchars($current['middle_name']); ?>"
    >
  </div>

  <div class="field">
    <label for="gender">Пол</label>
    <select name="gender" id="gender">
      <option value="">-- не выбран --</option>
      <option value="M" <?php if ($current['gender'] === 'M') echo 'selected'; ?>>Мужской</option>
      <option value="F" <?php if ($current['gender'] === 'F') echo 'selected'; ?>>Женский</option>
    </select>
  </div>

  <div class="field">
    <label for="birthdate">Дата рождения</label>
    <input
      type="date"
      name="birthdate"
      id="birthdate"
      value="<?php echo htmlspecialchars($current['birthdate']); ?>"
    >
  </div>

  <div class="field">
    <label for="phone">Телефон</label>
    <input
      type="text"
      name="phone"
      id="phone"
      value="<?php echo htmlspecialchars($current['phone']); ?>"
    >
  </div>

  <div class="field">
    <label for="address">Адрес</label>
    <input
      type="text"
      name="address"
      id="address"
      value="<?php echo htmlspecialchars($current['address']); ?>"
    >
  </div>

  <div class="field">
    <label for="email">E-mail</label>
    <input
      type="text"
      name="email"
      id="email"
      value="<?php echo htmlspecialchars($current['email']); ?>"
    >
  </div>

  <div class="field">
    <label for="comment">Комментарий</label>
    <textarea name="comment" id="comment"><?php
      echo htmlspecialchars($current['comment']); ?>
    </textarea>
  </div>

  <div class="field">
    <button type="submit">Сохранить</button>
  </div>
</form>
