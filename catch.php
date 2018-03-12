<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 17-9-13
 * Time: 下午5:06
 */

set_time_limit(0);

require "src/CatchCourse.php";

if (empty($_GET['account']) || empty($_GET['password']) || empty($_GET['key'])) {
    exit("请输入数据");
}
if (preg_match('/^2017/', $_GET['account']) && empty($_GET['year'])) {
    exit("17级童鞋敬请期待新功能^-^");
}

$account = $_GET['account'];
$password = $_GET['password'];
$key = $_GET['key'];
$place = isset($_GET['place']) ? $_GET['place'] : 1;

$placeStr = $place==1?'--北校区':'--南校区';
CatchCourse::pout("开始选课，等待登录"  . $placeStr);
$catch = new CatchCourse($account, $password);
$arr = explode('-', $key);
while (1) {
    $catch->catch_course(['course'=>$arr, $place]);
}