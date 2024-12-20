<?php
require "api/functions.php";
require "api/users/functions.php";
require "api/users/cookies.php";
require "api/games/functions.php";

if (!get("id") || !get("index")) redirect_to_home();

$game = new Nbhzvn_Game(intval(get("id"))); $index = intval(get("index")) - 1; $link = $game->links[$index];
if (!$game->id || !$link) redirect_to_home();
$path = "./uploads/" . $link->path;
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Type: application/' . $extension);

header('Content-Disposition: attachment; filename="'. $link->name . '";');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($path));

if (ob_get_level()) {
    ob_end_clean();
}

$game->add_downloads_count();
readfile($path);

exit;
?>