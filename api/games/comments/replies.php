<?php
$is_api = true;
require __DIR__ . "/../../functions.php";
require __DIR__ . "/../../users/functions.php";
require __DIR__ . "/../../users/cookies.php";
require __DIR__ . "/../functions.php";

try {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case "GET": {
            if (!get("id")) api_response(null, "Vui lòng nhập ID của bình luận.", 400);
            $comment = new Nbhzvn_Comment(intval(get("id")));
            if (!$comment->id) api_response(null, "Không tìm thấy bình luận có ID này.", 404);
            $result = $comment->fetch_replies();
            if (get("include_html")) {
                $new_res = new stdClass();
                $new_res->list = $result;
                $html = "";
                foreach ($result as $comment) $html .= $comment->to_html(!!$comment->replied_to, $user);
                $new_res->html = $html;
                $result = $new_res;
            }
            else if (get("html")) {
                $html = "";
                foreach ($result as $comment) $html .= $comment->to_html(!!$comment->replied_to, $user);
                $result = $html;
            }
            api_response($result, "Thực hiện thành công.");
        }
        default: {
            api_response(null, "Yêu cầu HTTP không hợp lệ.", 405);
            break;
        }
    }
}
catch (Exception $ex) {
    switch ($ex->getMessage()) {
        default: {
            $error = "Có lỗi không xác định xảy ra. Vui lòng báo cáo cho nhà phát triển của website.";
            break;
        }
    }
    api_response($ex->getMessage(), $error, 500);
}
?>