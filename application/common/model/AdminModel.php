<?php
/**
 * user 模型
 * User: Administrator
 * Date: 2018/8/25 0025
 * Time: 17:06
 */

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class AdminModel extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $table = 'dn_admin';
}