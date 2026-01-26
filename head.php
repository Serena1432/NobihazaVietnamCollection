<?php
$parsedown = new Parsedown();
$parsedown->setSafeMode(true);
$parsedown->setMarkupEscaped(true);
$parsedown->setBreaksEnabled(true);
use Soundasleep\Html2Text;
$meta_title = ($profile_user ? ($profile_user->display_name() . " ") : ($game ? ($game->name . " - ") : ($title ? ($title . " - ") : ""))) . "Nobihaza Vietnam Community Collection";
$meta_description = $game ? explode("\n", Html2Text::convert($parsedown->text($game->description)))[0] : META_DESCRIPTION;
?>
    <meta name="description" content="<?php echo $meta_description ?>" />
    <meta name="keywords" content="nobihaza,nobihaza game,nobihaza community collection,nobihaza vietnam,nobita's resident evil,nobihaza tieng viet,tai game nobihaza" />
    <meta name="author" content="Serena1432" />
    <meta name="copyright" content="(C) 2024 Serena1432" />
    <meta name="application-name" content="Nobihaza Vietnam Community Collection" />
    <meta property="og:title" content="<?php echo $meta_title ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:image" content="<?php echo ($game && str_contains($_SERVER["REQUEST_URI"], "games")) ? ("/uploads/" . $game->image) : "/img/logo.png" ?>" />
    <meta property="og:url" content="<?php echo $http . "://" . $host . $_SERVER["REQUEST_URI"] ?>" />
    <meta property="og:description" content="<?php echo $meta_description ?>" />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="<?php echo $meta_title ?>" />
    <meta name="twitter:description" content="<?php echo $meta_description ?>" />
    
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" type="image/png" sizes="180x180" href="/img/icon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/icon/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/img/icon/android-chrome-512x512.png">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php if (!$title) echo 'Nobihaza Vietnam Collection'; else echo $title; ?></title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Geologica" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="/css/bootstrap-darkly.min.css" type="text/css">
    <link rel="stylesheet" href="/css/style.css?v=<?=$res_version?>" type="text/css">
