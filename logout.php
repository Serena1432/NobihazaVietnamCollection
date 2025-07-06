<?php
setcookie("nbhzvn_username", "", time() - 3600, "/", "." . $_SERVER["HTTP_HOST"]);
setcookie("nbhzvn_login_token", "", time() - 3600, "/", "." . $_SERVER["HTTP_HOST"]);
header($_GET["speedrun"] ? "Location: https://speedrun.nbhzvn.one" : "Location: /");
?>