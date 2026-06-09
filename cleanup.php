<?php
require "api/functions.php";
require "api/games/functions.php";
if (!get("token") || get("token") != $_ENV["CLEANUP_TOKEN"]) api_response(null, "Mã xác thực không đúng.", 401);
$games = all_games(0, true);
$collected_files = [];
// Collect all game files
foreach ($games as $game) {
    try {
        // Collect thumbnail file
        array_push($collected_files, $game->image);
        // Collect screenshot files
        $collected_files = array_merge($collected_files, array_map(function($file) {
            return $file;
        }, $game->screenshots ? $game->screenshots : []));
        // Collect game links
        $collected_files = array_merge($collected_files, array_map(function($file) {
            return $file->path;
        }, $game->links ? $game->links : []));
        // Collect beta game links
        $collected_files = array_merge($collected_files, array_map(function($file) {
            return $file->path;
        }, $game->beta_links ? $game->beta_links : []));
    }
    catch (Exception $ex) {}
}
// Collect user avatars
$result = db_query('SELECT `avatar_url` FROM `nbhzvn_users` WHERE `avatar_url` IS NOT NULL AND `avatar_url` != ""');
while ($row = $result->fetch_object()) {
    array_push($collected_files, $row->avatar_url);
}
// Filter unused files
$deleted_files = array_diff(scandir(__DIR__ . "/uploads"), array_merge([".", "..", ".htaccess", ".migrate_1"], $collected_files));
// Cleanup process
mkdir(__DIR__ . "/deleted_tmp");
foreach ($deleted_files as $file) {
    $file_ = __DIR__ . "/uploads/" . $file;
    if (is_dir($file_)) delete_folder($file_);
    else unlink($file_);
}
api_response($deleted_files, "Đã dọn dẹp tổng cộng " . count($deleted_files) . " tệp tin.");
?>