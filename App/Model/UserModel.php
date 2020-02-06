<?php
/**
 * Created by PhpStorm.
 * User: Double-jin
 * Date: 2019/6/19
 * Email: 605932013@qq.com
 */

namespace App\Model;


use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Utility\Schema\Table;

class Admins extends AbstractModel
{
    protected $tableName = 'td_admins';
    //自定义表结构
    public function schemaInfo(bool $isCache = true): Table
    {
        $table = new Table();
        $table->colInt('uid')->setIsPrimaryKey(true);
        $table->colChar('username', 255);
        $table->colInt('status');
        $table->colInt('uid')->setIsPrimaryKey(true);

        return $table;
    }

}