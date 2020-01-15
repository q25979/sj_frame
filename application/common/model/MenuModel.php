<?php
/**
 * 菜单管理
 * User: Administrator
 * Date: 2018/8/25 0025
 * Time: 17:06
 */

namespace app\common\model;

use think\Model;
use app\common\model\AdminModel;
use app\common\model\RuleModel;

class MenuModel extends Model
{
    protected $table = 'dn_menu';

    // 获取菜单列表
    public function get_list($uid, $is_menu = true)
    {
        $a_model = new AdminModel();
        $r_model = new RuleModel();

        // 获取用户权限
        $rule_id = $a_model->getFieldById($uid, 'rule_id');
        if ($rule_id == 0) {
            return false;
        }

        if ($is_menu) {
            $map['status'] = 1;
            $map['type']   = 1;
            $m_id = $r_model->getFieldById($rule_id, 'm_id');
        } else {
            $map  = [];
            $m_id = 0;
        }

        // 根据身份获取菜单权限
        if ($m_id == '0') {
            $tlist = $this->where($map)
                ->order('sort', 'asc')
                ->all();
        } else {
            $tlist = $this->where($map)
                ->where('id', 'in', $m_id)
                ->order('sort', 'asc')
                ->all();
        }

        // 分解子菜单
        $pmlist = [];
        $cmlist = [];
        foreach ($tlist as $k => $v) {
            if ($v['parent_id'] == 0) {
                array_push($pmlist, $v);
            } else {
                array_push($cmlist, $v);
            }
        }

        foreach ($pmlist as $k => &$v) {
            $child = array();
            foreach ($cmlist as $ck => $cv) {
                if ($v['id'] == $cv['parent_id']) {
                    array_push($child, $cv);
                }
            }
            $v['children'] = $child;
        }

        return $pmlist;
    }

    // 获取菜单权限
    public function is_auth($uid, $path)
    {
        $a_model = new AdminModel();
        $r_model = new RuleModel();

        // 获取用户权限
        $rule_id = $a_model->getFieldById($uid, 'rule_id');
        if ($rule_id == 0) {
            return false;
        }

        // 根据身份获取菜单权限
        $m_id = $r_model->getFieldById($rule_id, 'm_id');
        if ($m_id == '0') {
            return true;
        } else {
            $tlist = $this
                ->where('id', 'in', $m_id)
                ->all();
        }
        $plist = explode("/", $path);

        foreach ($tlist as $k => $v) {
            if ($plist[0] == $v['app'] && $plist[1] == $v['model']) {
                if (isset($plist[2]) && $v['action'] == $plist[2]) {
                    return true;
                } else if (!isset($plist[2])) {
                    if ($v['action'] == 'index') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    // 获取上级
    public function get_parent()
    {
        $map['parent_id'] = 0;
        return $this->where($map)->order('sort', 'asc')->all();
    }
}
