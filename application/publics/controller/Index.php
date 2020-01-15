<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/24 0024
 * Time: 16:34
 */
namespace app\publics\controller;
use app\common\model\MenuModel;

class Index extends Validation
{
    public function index()
    {
        return $this->fetch();
    }

    // 主页
    public function home()
    {
        echo "主页";
    }

    // 菜单管理
    public function menu()
    {
        $m_model = new MenuModel();

        return json($m_model->get_list($this->uid));
    }
}
