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

$account = $_GET['account'];
$password = $_GET['password'];
$key = $_GET['key'];

CatchCourse::pout("开始选课，等待登录");
$catch = new CatchCourse($account, $password);
$arr = explode('-', $key);
while (1) {
    $catch->catch_course(['course'=>$arr]);
}