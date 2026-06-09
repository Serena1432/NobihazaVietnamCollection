<?php
const SUCCESS = 1;
const FAILED = 0;

const MISSING_INFORMATION = "-101";
const USERNAME_ALREADY_EXISTS = "-102";
const EMAIL_ALREADY_EXISTS = "-103";

const INCORRECT_CREDENTIALS = "-201";

const SEND_MAIL_FAILED = "-301";

const DISALLOWED_TYPE = "-401";

const ALREADY_RATED = "-501";

const NORMAL_USER_TYPE = 1;
const UPLOADER_TYPE = 2;
const ADMINISTRATOR_TYPE = 3;

const ENGINE_RPG2K = 1;
const ENGINE_RGSS = 2;
const ENGINE_RPGMV = 3;
const ENGINE_OTHER = 4;

const LANGUAGE_VIETNAMESE = 1;
const LANGUAGE_ENGLISH = 2;
const LANGUAGE_JAPANESE = 3;
const LANGUAGE_CHINESE = 4;
const LANGUAGE_OTHER = 5;
const LANGUAGE_MULTIPLE = 6;

const STATUS_DEVELOPING = 1;
const STATUS_FINISHED = 2;
const STATUS_ABANDONED = 3;

const OS_WINDOWS = "windows";
const OS_MACOS = "mac";
const OS_LINUX = "linux";
const OS_ANDROID = "android";
const OS_IOS = "ios";

const ACTION_FOLLOW = 2;
const ACTION_UNFOLLOW = 1;

const COMMENT_DEFAULT = 1;
const COMMENT_REPLY = 2;
const COMMENT_MENTION = 3;

const META_DESCRIPTION = "Nobihaza Vietnam Community Collection là một trang web được tạo bởi Serena1432 để tổng hợp (hầu hết) các game Nobihaza vào một website riêng, với mục tiêu là giúp các người chơi Nobihaza có thể tìm kiếm game hay theo dõi game mình thích dễ hơn.";

$engine_vocab = array(
    ENGINE_RPG2K => "RPG Maker 2000/2003",
    ENGINE_RGSS => "RPG Maker XP/VX/VX Ace",
    ENGINE_RPGMV => "RPG Maker MV",
    ENGINE_OTHER => "Phần Mềm Làm Game Khác"
);

$short_engine_vocab = array(
    ENGINE_RPG2K => "RPG2000/2003",
    ENGINE_RGSS => "RPGXP/VX/VXAce",
    ENGINE_RPGMV => "RPGMV",
    ENGINE_OTHER => "Engine Khác"
);

$language_vocab = array(
    LANGUAGE_VIETNAMESE => "Tiếng Việt",
    LANGUAGE_ENGLISH => "Tiếng Anh",
    LANGUAGE_JAPANESE => "Tiếng Nhật",
    LANGUAGE_CHINESE => "Tiếng Trung",
    LANGUAGE_OTHER => "Ngôn Ngữ Khác",
    LANGUAGE_MULTIPLE => "Đa Ngôn Ngữ"
);

$status_vocab = array(
    STATUS_DEVELOPING => "Đang phát triển",
    STATUS_FINISHED => "Đã hoàn thành",
    STATUS_ABANDONED => "Đã tạm ngưng"
);

$os_vocab = array(
    OS_WINDOWS => "Windows",
    OS_MACOS => "macOS",
    OS_LINUX => "Linux",
    OS_ANDROID => "Android",
    OS_IOS => "iOS"
);

$type_vocab = array(
    NORMAL_USER_TYPE => "Tài khoản thường",
    UPLOADER_TYPE => "Uploader",
    ADMINISTRATOR_TYPE => "Quản trị viên"
);
?>