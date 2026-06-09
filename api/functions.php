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
        <div id="pagination' . $distant_id . '" class="d-flex justify-content-center align-items-center mt-4 mb-2">
            <button class="btn btn-outline-primary btn-sm me-2" onclick="previousPage(\'' . $distant_id . '\')"><i class="bi bi-chevron-left"></i> Trang trước</button>
            <div class="input-group input-group-sm" style="width: auto;">
                <input id="currentPage' . $distant_id . '" class="form-control bg-dark text-white border-primary text-center shadow-none" style="max-width: 60px;" type="number" value="' . $page . '" onblur="jumpToPage(\'' . $distant_id . '\')" max="' . $pages . '" min="1">
                <span class="input-group-text bg-dark text-white border-primary">/ ' . $pages . '</span>
            </div>
            <button class="btn btn-outline-primary btn-sm ms-2" onclick="nextPage(\'' . $distant_id . '\')">Trang sau <i class="bi bi-chevron-right"></i></button>
        </div>
    ';
}

function get_total() {
    $result = db_query('SELECT SUM(views) as total_views, SUM(downloads) as total_downloads, COUNT(id) as total_games FROM `nbhzvn_games` WHERE 1');
    while ($row = $result->fetch_object()) return $row;
    return null;
}

function user_agent() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (empty($user_agent)) {
        return 'Unknown';
    }
    $os_array = [
        '/windows|win32|win98|win95|win16/i' => 'Windows',
        '/iphone|ipad|ipod/i'                => 'iOS',
        '/macintosh|mac os x|mac_powerpc/i'  => 'macOS',
        '/android/i'                         => 'Android',
        '/ubuntu/i'                          => 'Linux',
        '/linux/i'                           => 'Linux',
        '/blackberry/i'                      => 'BlackBerry',
        '/webos/i'                           => 'WebOS'
    ];
    foreach ($os_array as $regex => $os) {
        if (preg_match($regex, $user_agent)) {
            return $os;
        }
    }
    return 'Unknown';
}

// Update views_today
db_query('UPDATE `nbhzvn_games` SET `views_today` = 0, `downloads_today` = 0, `updated_date` = ? WHERE `updated_date` != ?', date('Y-m-d'), date('Y-m-d'));
?>