<?php

$url = ""; if (isset($_GET['url'])) $url = $_GET["url"];
$callback = ""; if (isset($_GET['callback'])) $callback = $_GET["callback"];
$isBilibili = preg_match("/\\bav\\d+(?:\/index_\\d+\\.html|\/)?$/", $url, $matches);

if (!$isBilibili) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-Type: text/html;charset=utf-8");
    echo '<h1 class="error">提供的网址不是合法视频页面</h1>';
    exit;
}

// get download addr
exec("python3 ./biliDownLoad.py http://www.bilibili.com/video/$matches[0]", $cmdRst);
$dlink = $cmdRst[0];
writeLog();

// handle JSONP
if ($callback != "") {
    header("Content-Type: text/javascript;charset=utf-8");
    echo $callback . "('$dlink')";
}
// no JSONP
else {
    header("Content-Type: text/html;charset=utf-8");

    if (strpos($dlink, "http") !== FALSE) {
        header("Location: $dlink");
        echo"<script>alert('已经开始下载！');history.go(-1);</script>";
        exit;
    } elseif ($dlink == "error2") {
        echo "<script>{window.alert('网址不正确！');location.href='/'};</script>";
        exit;
    }
    echo "<script>{window.alert('出现异常错误，可能为请求过频或视频已经被删除，请稍后再试，抱歉！');location.href='/'};</script>";
    exit;
}

function writeLog() {
    $myfile = fopen("debug.log", "a+");

    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    fwrite($myfile, $ipaddress."   /video/$url   $dlink\n");
    fclose($myfile);
}
