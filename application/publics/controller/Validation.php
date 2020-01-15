<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24 0024
 * Time: 16:34
 */
namespace app\publics\controller;

use think\Controller;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Session;
use app\common\model\MenuModel;

class Validation extends Controller
{
    public $uid;
    private $login_url = '/publics/login';

    // 继承
    public function __construct()
    {
        parent::__construct();

        // 判断是否存在数据
        $userinfo = session(ADMIN_USERINFO);
        if (empty($userinfo)) {
            $this->redirect($this->login_url);
        }

        // 获取存储时间
        $stime = session(ADMIN_USERINFO.'time');
        if ($stime - time() < 0) {
            session(ADMIN_USERINFO, null);
            $this->error('登录账号超时，请重新登录', $this->login_url);
        }

        // 获取用户信息并重新设定时间
        $this->uid = $userinfo['id'];
        session(ADMIN_USERINFO.'time', time() + 3600);

        // 操作权限
        $this->auth();
    }

    // 获取操作权限
    public function auth()
    {
        $m_model = new MenuModel();
        $is_auth = $m_model->is_auth($this->uid, Request::path());

        if (!$is_auth) {
            if (Request::isAjax()) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode([ 'code' => 101, 'msg' => '您当前没有权限操作!' ]));
            } else {
                echo "您当前没有权限操作!<br />";
                exit();
            }
        }
    }
}
