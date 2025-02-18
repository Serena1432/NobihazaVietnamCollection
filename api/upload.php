<?php
$is_api = true;
require __DIR__ . "/functions.php";
require __DIR__ . "/users/functions.php";
require __DIR__ . "/users/cookies.php";

try {
    $folder = "../uploads";
    if (!file_exists($folder)) {
        if (!mkdir($folder, 0775, true)) api_response(null, "Có lỗi xảy ra, vui lòng thử lại.", 500);
    }
    switch ($_SERVER["REQUEST_METHOD"]) {
        case "POST": {
            if (!$user || $user->type < 2) api_response(null, "Bạn không có quyền truy cập vào API này.", 403);
            if (post("type") == "chunk") {
                $extension = strtolower(pathinfo("./uploads/" . basename(post("file_name")), PATHINFO_EXTENSION));
                $types = ["zip", "rar", "7z", "jpg", "png", "webp", "jpeg", ".gz"];
                if (!in_array($extension, $types)) api_response(null, "Định dạng tệp tin không được hỗ trợ.", 400);
                if (!isset($_FILES["chunk"])) api_response(null, "Không có dữ liệu của tệp tin.", 400);
                $arguments = ["cursor", "chunks", "file_name"];
                foreach ($arguments as $argument) {
                    if (post($argument) == null) api_response(null, "Không tìm thấy giá trị của " . $argument . ".", 400);
                }
                $generated_name = post("generated_name");
                if (!$generated_name) {
                    $generated_name = random_string(64);
                    $path = $folder . "/" . $generated_name;
                    mkdir($path, 0775, true);
                    if (!file_exists($path)) api_response(null, "Không thể tải tệp tin lên vào thời gian này, vui lòng thử lại.", 500);
                }
                $path = $folder . "/" . $generated_name;
                $cursor = intval(post("cursor")); $chunks = intval(post("chunks"));
                if ($cursor < $chunks - 1) {
                    $path .= "/" . strval($cursor);
                    if (!move_uploaded_file($_FILES["chunk"]["tmp_name"], $path) || !chmod($path, 0775) || !file_exists($path)) api_response(null, "Không thể tải tệp tin lên vào thời gian này, vui lòng thử lại.", 500);
                    api_response($generated_name, "Đã hoàn thành tải lên " . ($cursor + 1) . "/" . $chunks . ".");
                }
                else {
                    $chunk_path = $path . "/" . strval($cursor);
                    if (!move_uploaded_file($_FILES["chunk"]["tmp_name"], $chunk_path) || !chmod($chunk_path, 0775) || !file_exists($chunk_path)) api_response(null, "Không thể tải tệp tin lên vào thời gian này, vui lòng thử lại.", 500);
                    $file_name = $generated_name . "." . strtolower(pathinfo(post("file_name"), PATHINFO_EXTENSION));
                    $file_path = $folder . "/" . $file_name;
                    $file = fopen($file_path, "wb");
                    if (!$file) api_response(null, "Không thể tải tệp tin lên vào thời gian này, vui lòng thử lại. 4", 500);
                    for ($i = 0; $i < $chunks; $i++) {
                        $chunk_path_tmp = $path . "/" . strval($i);
                        $chunk_data_tmp = file_get_contents($chunk_path_tmp);
                        fwrite($file, $chunk_data_tmp);
                        unlink($chunk_path_tmp);
                    }
                    fclose($file);
                    rmdir($path);
                    api_response($file_name, "Tải lên thành công.");
                }
            }
            else {
                if (!isset($_FILES["file"])) api_response(null, "Vui lòng chọn một tệp tin để tải lên.", 400);
                $extension = strtolower(pathinfo("./uploads/" . basename($_FILES["file"]["name"]), PATHINFO_EXTENSION));
                $types = ["zip", "rar", "7z", "jpg", "png", "webp", "jpeg"];
                if (!in_array($extension, $types)) api_response(null, "Định dạng tệp tin không được hỗ trợ.", 400);
                $name = random_string(64) . "." . $extension;
                $path = $folder . "/" . $name;
                if (!move_uploaded_file($_FILES["file"]["tmp_name"], $path) || !file_exists($path)) api_response(null, "Không thể tải tệp tin lên vào thời gian này, vui lòng thử lại.", 500);
                api_response($name, "Tải lên thành công.");
            }
            break;
        }
        default: {
            api_response(null, "Yêu cầu HTTP không hợp lệ.", 405);
            break;
        }
    }
}
catch (Exception $ex) {
    try {if ($path) rmdir($path);} catch (Exception $ex) {}
    api_response($ex->getMessage(), "Có lỗi xảy ra, vui lòng thử lại.", 500);
}
?>