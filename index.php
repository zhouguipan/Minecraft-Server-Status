<?php
error_reporting (E_ERROR);ini_set ("display_errors","Off");
//$server_ip = "116.10.184.211";
//$server_port = 62256;
$server_ip = $_GET["ip"];
$server_port = $_GET["port"];
$socket = fsockopen($server_ip, $server_port, $errno, $errstr, 1);
if (!$socket) {
    //echo "无法连接到服务器";
    //echo "无法连接到服务器: $errstr ($errno)\n";
    //exit(1);
}
fwrite($socket, "\xfe\x01");
$data = fread($socket, 1024);
if (substr($data, 0, 1) != "\xff") {
    //echo "无法读取服务器状态";
    //exit(1);
}
$data = substr($data, 3);
$data = iconv( 'UTF-16BE', 'UTF-8', $data );
$server_data = explode("\x00", $data);
$version = $server_data[2];
$motd = $server_data[3];//转换motd颜色为html
    $motdcvrhtml = str_replace("§k", "", $motd);
    $motdcvrhtml = str_replace("§l", "", $motdcvrhtml);
    $motdcvrhtml = str_replace("§m", "", $motdcvrhtml);
    $motdcvrhtml = str_replace("§n", "", $motdcvrhtml);
    $motdcvrhtml = str_replace("§o", "", $motdcvrhtml);
    $motdcvrhtml = str_replace("§r", '<font color="#">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§0", '<font color="#000000">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§1", '<font color="#0000AA">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§2", '<font color="#00AA00">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§3", '<font color="#00AAAA">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§4", '<font color="#AA0000">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§5", '<font color="#AA00AA">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§6", '<font color="#FFAA00">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§7", '<font color="#AAAAAA">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§8", '<font color="#555555">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§9", '<font color="#5555FF">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§a", '<font color="#55FF55">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§b", '<font color="#55FFFF">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§c", '<font color="#FF5555">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§d", '<font color="#FF55FF">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§e", '<font color="#FFFF55">', $motdcvrhtml);
    $motdcvrhtml = str_replace("§f", '<font color="#FFFFFF">', $motdcvrhtml);
    $motd = cs($motdcvrhtml);
$players = $server_data[4] . "/" . $server_data[5];//在线人数
if(empty($version) == false){//判断服务器是否在线
    $state = '<font color="#00FF00">在线</font>';
}
if(empty($version) == true){
    $state = '<font color="#FF0000">离线</font>';
    $version = "未知";
    $motd = "未知";
    $players = "未知";
}
$ip_int = ip2long($server_ip);
$ip1 = $server_ip.":".$server_port;
function cs($html) {
    @preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];
    @preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i=0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</'.$openedtags[$i].'>';
        } else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
}
class Timer{
private $startTime = 0;
private $stopTime = 0;
function start(){$this->startTime = microtime(true);}
function stop(){$this->stopTime = microtime(true);}
function spent(){return round(($this->stopTime-$this->startTime),4);}}
$timer= new Timer();
$timer->start();
usleep(1000);
$timer->stop();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>MC服务器状态查询</title>
</head>
<body bgcolor="#606060">
	<h3>MC服务器状态查询</h3>
    <div>
        <a>状态：<?php echo $state; ?></a><br>
        <a>连接地址：<?php echo $ip1; ?></a><br>
        <a>版本：<?php echo $version; ?></a><br>
        <a>描述：<?php echo $motd; ?></a><br>
        <a>在线人数：<?php echo $players; ?></a><br>
        <a>查询耗时：<?php echo $timer->spent().""; ?></a><br>
        <button onclick="location.reload ();">刷新</button>
    </div>
</body>
</html>