<?php

namespace App\HttpController\Api\Order;

use App\HttpController\Base;
use App\Model\Order\TdOrderModel;
use EasySwoole\Http\Annotation\Param;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

/**
 * Class TdOrder
 * Create With Automatic Generator
 */
class TdOrder extends Base
{
	/**
	 * @api {get|post} /Api/Order/TdOrder/add
	 * @apiName add
	 * @apiGroup /Api/Order/TdOrder
	 * @apiPermission
	 * @apiDescription add新增数据
	 * @Param(name="id", alias="", required="", lengthMax="11")
	 * @Param(name="contents", alias="内容", required="", lengthMax="")
	 * @Param(name="type_id", alias="类型ID", required="", lengthMax="11")
	 * @Param(name="wechat_id", alias="微信ID", required="", lengthMax="11")
	 * @Param(name="status", alias="1待处理 2已处理", required="", lengthMax="2")
	 * @Param(name="result", alias="处理结果", required="", lengthMax="")
	 * @Param(name="create_time", alias="11", required="", lengthMax="11")
	 * @Param(name="update_time", alias="", required="", lengthMax="11")
	 * @Param(name="user_id", alias="提交人", required="", lengthMax="11")
	 * @apiParam {int} id
	 * @apiParam {string} contents 内容
	 * @apiParam {int} type_id 类型ID
	 * @apiParam {int} wechat_id 微信ID
	 * @apiParam {int} status 1待处理 2已处理
	 * @apiParam {string} result 处理结果
	 * @apiParam {int} create_time 11
	 * @apiParam {int} update_time
	 * @apiParam {int} user_id 提交人
	 * @apiSuccess {Number} code
	 * @apiSuccess {Object[]} data
	 * @apiSuccess {String} msg
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {"code":200,"data":{},"msg":"success"}
	 * @author: AutomaticGeneration < 1067197739@qq.com >
	 */
	public function add()
	{
		$param = $this->request()->getRequestParam();
		$data = [
		    'id'=>$param['id'],
		    'contents'=>$param['contents'],
		    'type_id'=>$param['type_id']??'0',
		    'wechat_id'=>$param['wechat_id']??'0',
		    'status'=>$param['status']??'1',
		    'result'=>$param['result'],
		    'create_time'=>$param['create_time']??'0',
		    'update_time'=>$param['update_time']??'0',
		    'user_id'=>$param['user_id']??'0',
		];
		$model = new TdOrderModel($data);
		$rs = $model->save();
		if ($rs) {
		    $this->writeJson(Status::CODE_OK, $model->toArray(), "success");
		} else {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
		}
	}


	/**
	 * @api {get|post} /Api/Order/TdOrder/update
	 * @apiName update
	 * @apiGroup /Api/Order/TdOrder
	 * @apiPermission
	 * @apiDescription update修改数据
	 * @Param(name="id", alias="", optional="", lengthMax="11")
	 * @Param(name="contents", alias="内容", optional="", lengthMax="")
	 * @Param(name="type_id", alias="类型ID", optional="", lengthMax="11")
	 * @Param(name="wechat_id", alias="微信ID", optional="", lengthMax="11")
	 * @Param(name="status", alias="1待处理 2已处理", optional="", lengthMax="2")
	 * @Param(name="result", alias="处理结果", optional="", lengthMax="")
	 * @Param(name="create_time", alias="11", optional="", lengthMax="11")
	 * @Param(name="update_time", alias="", optional="", lengthMax="11")
	 * @Param(name="user_id", alias="提交人", optional="", lengthMax="11")
	 * @apiParam {int} id 主键id
	 * @apiParam {int} [id]
	 * @apiParam {mixed} [contents] 内容
	 * @apiParam {int} [type_id] 类型ID
	 * @apiParam {int} [wechat_id] 微信ID
	 * @apiParam {int} [status] 1待处理 2已处理
	 * @apiParam {mixed} [result] 处理结果
	 * @apiParam {int} [create_time] 11
	 * @apiParam {int} [update_time]
	 * @apiParam {int} [user_id] 提交人
	 * @apiSuccess {Number} code
	 * @apiSuccess {Object[]} data
	 * @apiSuccess {String} msg
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {"code":200,"data":{},"msg":"success"}
	 * @author: AutomaticGeneration < 1067197739@qq.com >
	 */
	public function update()
	{
		$param = $this->request()->getRequestParam();
		$model = new TdOrderModel();
		$info = $model->get(['id' => $param['id']]);
		if (empty($info)) {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], '该数据不存在');
		    return false;
		}
		$updateData = [];

		$updateData['id'] = $param['id']??$info->id;
		$updateData['contents'] = $param['contents']??$info->contents;
		$updateData['type_id'] = $param['type_id']??$info->type_id;
		$updateData['wechat_id'] = $param['wechat_id']??$info->wechat_id;
		$updateData['status'] = $param['status']??$info->status;
		$updateData['result'] = $param['result']??$info->result;
		$updateData['create_time'] = $param['create_time']??$info->create_time;
		$updateData['update_time'] = $param['update_time']??$info->update_time;
		$updateData['user_id'] = $param['user_id']??$info->user_id;
		$rs = $info->update($updateData);
		if ($rs) {
		    $this->writeJson(Status::CODE_OK, $rs, "success");
		} else {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], $model->lastQueryResult()->getLastError());
		}
	}


	/**
	 * @api {get|post} /Api/Order/TdOrder/getOne
	 * @apiName getOne
	 * @apiGroup /Api/Order/TdOrder
	 * @apiPermission
	 * @apiDescription 根据主键获取一条信息
	 * @Param(name="id", alias="", optional="", lengthMax="11")
	 * @apiParam {int} id 主键id
	 * @apiSuccess {Number} code
	 * @apiSuccess {Object[]} data
	 * @apiSuccess {String} msg
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {"code":200,"data":{},"msg":"success"}
	 * @author: AutomaticGeneration < 1067197739@qq.com >
	 */
	public function getOne()
	{
		$param = $this->request()->getRequestParam();
		$model = new TdOrderModel();
		$bean = $model->get(['id' => $param['id']]);
		if ($bean) {
		    $this->writeJson(Status::CODE_OK, $bean, "success");
		} else {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
		}
	}


	/**
	 * @api {get|post} /Api/Order/TdOrder/getAll
	 * @apiName getAll
	 * @apiGroup /Api/Order/TdOrder
	 * @apiPermission
	 * @apiDescription 获取一个列表
	 * @apiParam {String} [page=1]
	 * @apiParam {String} [limit=20]
	 * @apiParam {String} [keyword] 关键字,根据表的不同而不同
	 * @apiSuccess {Number} code
	 * @apiSuccess {Object[]} data
	 * @apiSuccess {String} msg
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {"code":200,"data":{},"msg":"success"}
	 * @author: AutomaticGeneration < 1067197739@qq.com >
	 */
	public function getAll()
	{
		$param = $this->request()->getRequestParam();
		$page = (int)($param['page']??1);
		$limit = (int)($param['limit']??20);
		$model = new TdOrderModel();
		$data = $model->getAll($page, $param['keyword']??null, $limit);
		$this->writeJson(Status::CODE_OK, $data, 'success');
	}


	/**
	 * @api {get|post} /Api/Order/TdOrder/delete
	 * @apiName delete
	 * @apiGroup /Api/Order/TdOrder
	 * @apiPermission
	 * @apiDescription 根据主键删除一条信息
	 * @Param(name="id", alias="", optional="", lengthMax="11")
	 * @apiParam {int} id 主键id
	 * @apiSuccess {Number} code
	 * @apiSuccess {Object[]} data
	 * @apiSuccess {String} msg
	 * @apiSuccessExample {json} Success-Response:
	 * HTTP/1.1 200 OK
	 * {"code":200,"data":{},"msg":"success"}
	 * @author: AutomaticGeneration < 1067197739@qq.com >
	 */
	public function delete()
	{
		$param = $this->request()->getRequestParam();
		$model = new TdOrderModel();

		$rs = $model->destroy(['id' => $param['id']]);
		if ($rs) {
		    $this->writeJson(Status::CODE_OK, [], "success");
		} else {
		    $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
		}
	}
}

