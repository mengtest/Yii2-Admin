<?php
/**
 * Yii2-Admin
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    wangyaxian <1822581649@qq.com>
 * @link      https://github.com/duiying/Yii2-Admin
 */

namespace backend\controllers;

use backend\models\Role;
use Yii;
use backend\models\Admin;
use common\models\MsgUtil;
use backend\controllers\BaseController;

/**
 * 管理员控制器
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    wangyaxian <1822581649@qq.com>
 * @link      https://github.com/duiying/Yii2-Admin
 */
class AdminController extends BaseController
{
    /**
     * 修改密码
     *
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionChangePass()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Admin();
            $post = Yii::$app->request->post();
            $res = $model->changePass($post);
            return MsgUtil::dataFormat($res);
        }
        return $this->render('change-pass');
    }

    /**
     * 列表
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Admin();
        $res = $model->index();
        return $this->render('index', ['list' => $res['list'], 'pager' => $res['pager']]);
    }

    /**
     * 添加
     *
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Admin();
            $post = Yii::$app->request->post();
            $res = $model->create($post);
            return MsgUtil::dataFormat($res);
        }

        $roleModel = new Role();
        // 角色列表
        $roleList = $roleModel->roleList();

        return $this->render('create', ['roleList' => $roleList]);
    }

    /**
     * 编辑
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Admin();
            $post = Yii::$app->request->post();
            $res = $model->edit($post);
            return MsgUtil::dataFormat($res);
        }

        // 管理员ID
        $admin_id = Yii::$app->request->get('admin_id');
        // 检查参数
        if (!$admin_id) {
            return $this->redirect(['base/error']);
        }

        $roleModel = new Role();
        // 角色列表
        $roleList = $roleModel->roleList();

        $model = Admin::find()->where(['admin_id' => $admin_id])->select(['admin_id', 'admin_name', 'role_id'])->one();

        return $this->render('update', ['model' => $model, 'roleList' => $roleList]);
    }

    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Admin();
            $post = Yii::$app->request->post();
            $res = $model->del($post);
            return MsgUtil::dataFormat($res);
        }
    }
}