<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 17-9-13
 * Time: 下午4:39
 */

require "config.php";

class CatchCourse
{
    //随机id
    public $rndnum;
    //登陆状态
    public $status = 0;
    //cookie存放目录
    public $cookie;
    //账号
    public $account;
    //密码
    public $name;
    //姓名
    protected $pw;

    public function __construct($account, $password)
    {
        $this->account = $account;
        $this->pw = $password;
        $this->get_random_id();
        $this->login();
        $this->get_name();
    }


    /**
     * @return bool
     * @throws Exception
     * 登陆，保存cookie文件
     */
    public function login()
    {
        $login_url = 'http://' . URL . '/' . $this->rndnum . '/default2.aspx';
        $this->cookie = tempnam('./temp', 'cookie');

        $login_page = $this->curl($login_url);
        $__VIEWSTATE = $this->get_view($login_page);

        $captcha = $this->get_captcha();
        $postField = array(
            '__VIEWSTATE' => $__VIEWSTATE,
            'txtUserName' => $this->account,
            'TextBox2' => $this->pw,
            'txtSecretCode' => $captcha,
            'RadioButtonList1' => '学生',
            'Button1' => '',
            'lbLanguage' => '',
            'hidPdrs' => '',
            'hidsc' => ''
        );

        $contents = $this->curl($login_url, $postField);

        $contents = iconv("GBK", "UTF-8//IGNORE", $contents);
        $contents = preg_replace("/lang=\"gb2312\"/", "", $contents);
        $contents = preg_replace("/gb2312/", "utf-8", $contents);

        if (preg_match('/(alert)/', $contents)) {
            $this->status = -1;
            if (preg_match('//i', $contents))
                die("密码错误");
            elseif (preg_match('/用户名不存在/i', $contents)) {
                die("用户名不存在");
            }
            return false;
        }
        self::pout("登录成功");
        $this->status = 1;
        return true;
    }

    public function get_random_id()
    {
        $login_url = 'http://' . URL . '/default2.aspx';
        $ch = curl_init($login_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        curl_close($ch);

        preg_match("/<a href='\/(.*)\/default2.aspx'>/", $contents, $arr);
        $this->rndnum = $arr[1];
    }

    public function get_view($contents)
    {
        $pattern = '/<input type=\"hidden\" name=\"__VIEWSTATE\" value=\"(.*?)\" \/>/i';
        preg_match($pattern, $contents, $matches);
        return end($matches);
    }


    public function curl($url, $curlPost = NULL)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
        if ($curlPost != NULL)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $contents = curl_exec($ch);
        curl_close($ch);

        return $contents;
    }


    public function get_captcha()
    {
        $imgUrl = 'http://' . URL . '/' . $this->rndnum . '/CheckCode.aspx';
        $result = $this->curl($imgUrl);
        $result = base64_encode($result);
        $captcha = $this->curl(CAPTCHA_URL, ['img' => $result]);
        return $captcha;
    }


    public function get_name()
    {
        //step4:获取查询成绩的链接,以及姓名学号
        $url = 'http://' . URL . '/' . $this->rndnum . '/xs_main.aspx?xh=' . $this->account;
        $contents = $this->curl($url);

        $pattern = "/<a href=\"xscjcx.aspx\?(.*)\" target='zhuti'/U";
        if (!preg_match($pattern, $contents, $matches)) {
            return;
        }
        if (!$matches[1]) {
            return;
        }

        $url1 = $matches[1];
        $arr = explode('=', $url1);
        $this->name = $contents = explode('&', $arr[2])[0];
    }

    public function catch_course($params)
    {
        if ($this->status) {
            $courseUrl = 'http://' . URL . '/' . $this->rndnum . '/xf_xsqxxxk.aspx?xh='. $this->account .'&xm='. $this->name .'&gnmkdm=N121103';
            ReCatch:
            $post = $this->get_catch_post($params);
            for ($i = 0; $i < 10; $i++) {
                $result = $this->curl($courseUrl, $post);
                if ($this->is_logout($result)) {
                    self::pout("重新登录");
                    $this->login();
                    goto ReCatch;
                }

                $result = mb_convert_encoding($result, 'UTF-8', 'GBK,UTF-8,ASCII');

                if (preg_match_all('/alert\(\'(.*?)\'\);/i', $result, $alt)) {
                    foreach ($alt[1] as $massage) {
                        self::pout($massage);
                    }
                } elseif (preg_match_all('/三秒防刷/',$result)) {
                    self::pout("被三秒防刷,正在重试");
                    goto ReCatch;
                }else {
                    self::pout("继续选课");
                }
            }

        } else {
            $this->login();
            $this->get_name();
            return $this->catch_course($params);
        }
    }

    public function get_catch_post($params)
    {
        $courseUrl = 'http://' . URL . '/' . $this->rndnum . '/xf_xsqxxxk.aspx?xh='. $this->account .'&xm='. $this->name .'&gnmkdm=N121103';
        T:
        $coursePage = $this->curl($courseUrl);
        $coursePage = mb_convert_encoding($coursePage, 'UTF-8', 'GBK,UTF-8,ASCII');
        $__VIEWSTATE = $this->get_view($coursePage);
        if ($this->is_logout($coursePage) || !$__VIEWSTATE) {
            $this->login();
            goto T;
        }

        $this->test_current_course($coursePage);
        $amount = $this->get_amount($coursePage);

        $post = [
            '__VIEWSTATE' => $__VIEWSTATE,
            'ddl_xqbs' => 1,
            'dpkcmcGrid:txtChoosePage' => isset($params['page'])? $params['page'] : 1,
            'dpkcmcGrid:txtPageSize' => isset($params['page_size'])? $params['page_size'] : $amount,
            'Button1' => '++%CC%E1%BD%BB++',
            'ddl_ywyl' => '',
        ];

        $goalPage = $result = $this->curl($courseUrl, $post);
        $__VIEWSTATE0 = $this->get_view($goalPage);
        if ($this->is_logout($goalPage) || !$__VIEWSTATE0) {
            $this->login();
            goto T;
        }

        $post['__VIEWSTATE'] = $__VIEWSTATE0;

        foreach ($params['course'] as $course) {
            $post['kcmcGrid:_ctl'. $course .':xk'] = 'on';
        }
        return $post;
    }

    public function is_logout($content)
    {
        if (preg_match('/object mov/i', $content)) {
            return true;
        }
        return false;
    }

    public static function pout($string)
    {
        echo str_repeat(" ",1024);
        echo $string."<br>";
        echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
        ob_flush();
        flush();
    }

    public function test_current_course($content)
    {
        preg_match_all('/已选课程[\w\W]*?<tr class=\"datelisthead\"\>[\w\W]*?<\/tr\>(<tr\>[\w\W]*?<\/tr\>){0,2}/i', $content, $course);
        self::pout("当前共选中" . count($course[1]) . "门课程");
        foreach ($course[1] as $value) {
            self::pout($value);
        }
        if (count($course[1]) >= 2) {
            exit("选课完毕");
        }
    }

    public function get_amount($content)
    {
        preg_match('/总共([0-9]+)条记录/', $content, $amount);
        return isset($amount[1]) ? $amount[1] : 100;
    }

}