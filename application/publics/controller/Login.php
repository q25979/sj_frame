<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24 0024
 * Time: 16:34
 */
namespace app\publics\controller;

use app\common\model\AdminModel;
use think\Controller;
use think\facade\Request;
use think\captcha\Captcha;
use think\facade\Session;
use think\facade\Cache;

class Login extends Controller
{
    public function index()
    {
        // 账号登录
        if (Request::isPost()) {
            $post = Request::post();

            $info = AdminModel::where(['login_user' => $post['username']])->find();
            $lock = Cache::get(ADMIN_LOCK_KEY.$info['id']);

            if (!empty($lock) && $lock >= 10) {
                return json(['code' => -1, 'msg' => '您已经输入操作10次了，请1小时后再试']);
            }

            if (!captcha_check($post['code'])) {
                return json(['code' => -1, 'msg' => '验证码错误', 'data' => Session::get('ThinkPHP.CN')]);
            }

            // 密码错误
            $password =  md5($post['password'].strtotime($info['create_time']));
            if (empty($info) || $info['login_pass'] != $password) {
                if (empty($lock)) {
                    Cache::set(ADMIN_LOCK_KEY.$info['id'], 1, 30);
                } else {
                    Cache::inc(ADMIN_LOCK_KEY.$info['id']);
                }

                return json(['code' => -1, 'msg' => '您的账号或密码错误', 'data' => $info]);
            }

            // 登录成功, 缓存一个小时
            session(ADMIN_USERINFO.'time', time() + 3600);
            session(ADMIN_USERINFO, $info);
            return json(['code' => 200, 'msg' => '登录成功，正在为您跳转...']);
        }

        return $this->fetch();
    }

    // 账号退出
    public function logout()
    {
        session(ADMIN_USERINFO, null);
        return json(['code' => 0, 'msg' => '清除成功']);
    }
}
