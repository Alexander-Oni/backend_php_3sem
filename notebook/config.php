<?php
  // защита от прямого доступа в браузере, выдаем ошибку запрета доступа и прерываем выполнение файла
  if (!defined('APP'))
  {
    http_response_code(403); 
    exit;
  }
  // PHP Data Object
  function get_pdo()
  {
    $host = '127.0.0.1';    // хост базы данных
    $db = 'notebook_ver2';           // имя  БД
    $user = 'root';         // логин MySQL
    $pass = '';             // пароль MySQL
    $charset = 'utf8mb4';   // кодировка таблиц БД

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset"; // Data Sourse Name(для доступа к бд)

    $options = 
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // прирывание скрипта и генерация исключения
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // ассоциативный массив
      PDO::ATTR_EMULATE_PREPARES => false, // использование настоящих подготовленных запросов
    ];

    try
    {
      $pdo = new PDO($dsn, $user, $pass, $options);

      return $pdo;
    }
    catch (PDOException $e)
    { 
      // обработка ошибки подключения к базе данных
      if (defined('DEBUG_MODE')) 
      {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
      } 
      else {
        die("Извините, возникла проблема с подключением к базе данных. Пожалуйста, попробуйте позже.");
      }
    }
  }
?>
