<?php
include __DIR__ . "/connection.php";
require __DIR__ . "/db_setup.php";
require __DIR__ . "/csrf.php";
require __DIR__ . "/mail.php";
require __DIR__ . "/classes.php";

$http = (empty($_SERVER["HTTPS"]) ? "http" : "https");
$host = get_root_domain();

function check_email_validity($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) &&
           (str_ends_with($email, "@gmail.com") || str_ends_with($email, "@yahoo.com") || str_ends_with($email, "@outlook.com"));
}

function db_query($query, ...$args) {
    global $conn;
    $tmp = $conn->prepare($query);
    $type = "";
    for ($i = 0; $i < func_num_args() - 1; $i++) $type .= "s";
    if (func_num_args() >= 2) $tmp->bind_param($type, ...$args);
    $tmp->execute();
    return $tmp->get_result();
}

function api_header() {
    global $api_version;
    header("Content-Type: application/json");
    header("Api-Version: " . $api_version);
}

function api_response($data, $message = "", $status_code = 200) {
    api_header();
    $res = new stdClass();
    $res->success = ($status_code == 200);
    $res->status_code = $status_code;
    $res->message = $message;
    $res->data = $data;
    http_response_code($status_code);
    die(json_encode($res));
}

function http_get_request($url, $headers = []) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_UNRESTRICTED_AUTH => true,
        CURLOPT_HTTPHEADER => $headers
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function http_post_request($url, $body = array(), $headers = []) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_VERBOSE => true,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_UNRESTRICTED_AUTH => true,
        CURLOPT_POST => 1,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $body
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function pagination($item_count = 0, $items_per_page = 20, $page = 1, $distant_id = "") {
    $pages = ceil($item_count / $items_per_page);
    if ($item_count == 0 || $pages < 2) return "";
    echo '
        <div id="pagination' . $distant_id . '" class="nbhzvn_pagination">
            <button class="previous" onclick="previousPage(\'' . $distant_id . '\')">&lt; Trang trước</button>
            <input id="currentPage' . $distant_id . '" class="current_page" type="number" value="' . $page . '" onblur="jumpToPage(\'' . $distant_id . '\')" max="' . $pages . '">
            <button class="pages_count"> / ' . $pages . '</button>
            <button class="next" onclick="nextPage(\'' . $distant_id . '\')">Trang sau &gt;</button>
        </div>
    ';
}

function get_total() {
    $result = db_query('SELECT SUM(views) as total_views, SUM(downloads) as total_downloads, COUNT(id) as total_games FROM `nbhzvn_games` WHERE 1');
    while ($row = $result->fetch_object()) return $row;
    return null;
}

// Update views_today
db_query('UPDATE `nbhzvn_games` SET `views_today` = 0, `downloads_today` = 0, `updated_date` = ? WHERE `updated_date` != ?', date('Y-m-d'), date('Y-m-d'));
?>