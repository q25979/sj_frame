<?php
/**
 * 菜单管理
 * User: Administrator
 * Date: 2018/8/24 0024
 * Time: 16:34
 */
namespace app\publics\controller;
use app\common\model\MenuModel;
use think\facade\Request;

class Menu extends Validation
{
    // 模型定义
    private $m_model;

    // 初始化
    public function __construct()
    {
        parent::__construct();
        $this->m_model = new MenuModel();
    }

    // 菜单列表
    public function list()
    {
        if (Request::isAjax()) {
            return json($this->m_model->get_list($this->uid, false));
        }

        return $this->fetch();
    }

    // 修改数据
    public function update()
    {
        if (Request::isAjax()) {
            $post = input('post.');

            $map['id'] = $post['id'];
            $info = $this->m_model->where($map)->update($post);

            if ($info > 0) {
                return json([
                    'code'  => 200,
                    'msg'   => '更新成功，请手动刷新页面'
                ]);
            } else {
                return json([
                    'code'  => 0,
                    'msg'   => '数据更新失败'
                ]);
            }
        }
    }

    // 删除数据
    public function delete()
    {
        $id = input('post.key');
        $info = $this->m_model->where(['id' => $id])->delete();

        if ($info > 0) {
            return json([
                'code'  => 200,
                'msg'   => '菜单删除成功，请手动刷新页面'
            ]);
        } else {
            return json([
                'code'  => 0,
                'msg'   => '菜单删除失败或菜单不存在'
            ]);
        }
    }

    // 添加菜单
    public function add()
    {
        if (Request::isAjax()) {
            $post = input('post.');

            $info = $this->m_model->insert($post);
            if ($info > 0) {
                $result = [ 'code' => 200, 'msg' => '菜单添加成功，请手动刷新页面' ];
            } else {
                $result = [ 'code' => 0, 'msg' => '菜单添加失败'];
            }

            return json($result);
        }

        $this->assign([
            'parent' => $this->m_model->get_parent(),
            'info'   => false
        ]);
        return $this->fetch('save');
    }

    // 修改菜单
    public function save()
    {
        $id = input('get.id');
        if (empty($id)) {
            echo "获取菜单数据失败";
            return ;
        }

        $this->assign([
            'parent' => $this->m_model->get_parent(),
            'info'   => $this->m_model->getById($id)
        ]);
        return $this->fetch('save');
    }
}
