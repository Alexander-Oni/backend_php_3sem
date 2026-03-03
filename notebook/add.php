<?php
  // защита от прямого доступа в браузере, выдаем ошибку запрета доступа и прерываем выполнение файла
  if (!defined('APP')) 
  {
    http_response_code(403);
    exit;
  }

  $pdo = get_pdo();

  // переменная для вывода сообщения пользователю
  $message = '';

  // массив полей формы
  $values = 
  [
    'last_name' => '',
    'first_name' => '',
    'middle_name' => '',
    'gender' => '',
    'birthdate' => '',
    'phone' => '',
    'address' => '',
    'email' => '',
    'comment' => '',
  ];

  if ($_SERVER['REQUEST_METHOD'] === 'POST')
  {
    foreach ($values as $key => $v)
    {
      $values[$key] = isset($_POST[$key]) ? trim($_POST[$key]) : '';
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

      // добавление записи
      try
      {
        $stmt = $pdo->prepare("
          INSERT INTO contacts
            (last_name, first_name, middle_name, gender, birthdate, phone, address, email, comment)
          VALUES
            (:last_name, :first_name, :middle_name, :gender, :birthdate, :phone, :address, :email, :comment)
        ");

        $stmt->execute(
          [
            ':last_name' => $values['last_name'],
            ':first_name' => $values['first_name'],
            ':middle_name' => $values['middle_name'],
            ':gender' => $gender,
            ':birthdate' => $values['birthdate'] ?: null,
            ':phone' => $values['phone'],
            ':address' => $values['address'],
            ':email' => $values['email'],
            ':comment' => $values['comment'],
          ]
        );

        $message = '<p class="success">Контакт добавлен</p>';

        // очитска формы
        foreach ($values as $key => $v) 
        {
          $values[$key] = '';
        }
      }
      catch (PDOException $e)
      {
        $message = '<p class="error">Ошибка: не удалось добавить контакт.</p>';
      }
    }
  }
?>

<h2>Добавление контакта</h2>

<?php
  echo $message;
?>

<form method="post">
  <div class="field">
    <label for="last_name">Фамилия*</label>
    <input
      type="text"
      name="last_name"
      id="last_name"
      value="<?php echo htmlspecialchars($values['last_name']); ?>"
    >
  </div>

  <div class="field">
    <label for="first_name">Имя*</label>
    <input
      type="text"
      name="first_name"
      id="first_name"
      value="<?php echo htmlspecialchars($values['first_name']); ?>"
    >
  </div>

  <div class="field">
    <label for="middle_name">Отчество</label>
    <input
      type="text"
      name="middle_name"
      id="middle_name"
      value="<?php echo htmlspecialchars($values['middle_name']); ?>"
    >
  </div>

  <div class="field">
    <label for="gender">Пол</label>
    <select name="gender" id="gender">
      <option value="">-- не выбран --</option>
      <option value="M" <?php if ($values['gender'] === 'M') echo 'selected'; ?>>Мужской</option>
      <option value="F" <?php if ($values['gender'] === 'F') echo 'selected'; ?>>Женский</option>
    </select>
  </div>

  <div class="field">
    <label for="birthdate">Дата рождения</label>
    <input
      type="date"
      name="birthdate"
      id="birthdate"
      value="<?php echo htmlspecialchars($values['birthdate']); ?>"
    >
  </div>

  <div class="field">
    <label for="phone">Телефон</label>
    <input
      type="text"
      name="phone"
      id="phone"
      value="<?php echo htmlspecialchars($values['phone']); ?>"
    >
  </div>

  <div class="field">
    <label for="address">Адрес</label>
    <input
      type="text"
      name="address"
      id="address"
      value="<?php echo htmlspecialchars($values['address']); ?>"
    >
  </div>

  <div class="field">
    <label for="email">E-mail</label>
    <input
      type="text"
      name="email"
      id="email"
      value="<?php echo htmlspecialchars($values['email']); ?>"
    >
  </div>

  <div class="field">
    <label for="comment">Комментарий</label>
    <textarea name="comment" id="comment"><?php
      echo htmlspecialchars($values['comment']); ?></textarea>
  </div>

  <div class="field">
    <button type="submit">Сохранить</button>
  </div>
</form>