<?php
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php-error.log');

header('Content-Type: application/json; charset=utf-8');

$dbHost = 'localhost';
$dbName = 'c503029_myrecipe_na4u_ru';
$dbUser = 'c503029_myrecipe_na4u_ru';
$dbPass = 'hU(3NPtVMepd+d7';

try {
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8";
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    );

    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

    // фичи
    $stmt1 = $pdo->query('SELECT id, name FROM mod_features ORDER BY id');
    $features = $stmt1->fetchAll();

    // части
    $stmt2 = $pdo->query('SELECT id, name FROM mod_parts ORDER BY id');
    $parts = $stmt2->fetchAll();

    // категории рецептов
    $stmt3 = $pdo->query('SELECT id, name FROM mod_recipe_category ORDER BY id');
    $recipe_category = $stmt3->fetchAll();

    // галерея рецептов
    $stmt4 = $pdo->query('SELECT id, recipe_id, image FROM mod_recipe_gallery ORDER BY id');
    $recipe_gallery = $stmt4->fetchAll();

    // рецепты
    $stmt5 = $pdo->query('SELECT id, title, category, description, url, image, ingredients, content, is_active, is_show_home FROM mod_recipe ORDER BY id');
    $recipe = $stmt5->fetchAll();

    $response = array(
        'features' => $features,
        'parts'    => $parts,
        'recipe_category'    => $recipe_category,
        'recipe_gallery'    => $recipe_gallery,
        'recipe'    => $recipe
    );

    $out = json_encode($response);
    // Если доступна опция — вернуть кириллицу без экранирования
    if (defined('JSON_UNESCAPED_UNICODE')) {
        $out = json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        // развёртываем \uXXXX в UTF-8 для читаемости
        $out = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', 'unescape_unicode_callback', $out);
    }

    echo $out;
    exit;

} catch (Exception $e) {
    error_log("api.php error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(array('error' => 'Server error'));
    exit;
}