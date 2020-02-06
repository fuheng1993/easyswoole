<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/3
 * Time: 10:54
 */
namespace App\Model;

use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Utility\Schema\Table;

/**
 * Class WechatModel
 * Create With Automatic Generator
 * @property $adminId
 * @property $adminName
 * @property $adminAccount
 * @property $adminPassword
 * @property $adminSession
 * @property $adminLastLoginTime
 * @property $adminLastLoginIp
 */
class WechatModel extends AbstractModel
{
    protected $tableName = 'td_wechat';

    protected $primaryKey = 'id';

    /**
     * @getAll
     * @keyword adminName
     * @param  int  page  1
     * @param  string  keyword
     * @param  int  pageSize  10
     * @return array[total,list]
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword))
        {
            $where['username'] = ['%' . $keyword . '%', 'like'];
        }
        $list  = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    public function login():?WechatModel
    {
        $info = $this->get(['username' => $this->username, 'password' => $this->password]);
        return $info;
    }

    /*
     * 以account进行查询
     */
    public function accountExist($field = '*'):?WechatModel
    {
        $info = $this->field($field)->get(['username' => $this->username]);
        return $info;
    }

    public function getOneBySession($field = '*'):?WechatModel
    {
        $info = $this->field($field)->get(['adminSession' => $this->adminSession]);
        return $info;
    }

    public function logout()
    {
        return $this->update(['adminSession' => '']);
    }

}