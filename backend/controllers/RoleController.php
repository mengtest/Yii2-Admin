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

use backend\models\Auth;
use backend\models\Role;
use backend\models\RoleAuthItem;
use Yii;
use common\models\MsgUtil;
use backend\controllers\BaseController;

/**
 * 角色控制器
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    wangyaxian <1822581649@qq.com>
 * @link      https://github.com/duiying/Yii2-Admin
 */
class RoleController extends BaseController
{
    /**
     * 列表
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Role();
        $res = $model->index();
        return $this->render('index', ['list' => $res['list'], 'pager' => $res['pager']]);
    }

    /**
     * 添加
     *
     * @return string
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Role();
            $post = Yii::$app->request->post();
            $res = $model->create($post);
            return MsgUtil::dataFormat($res);
        }
        $authModel = new Auth();
        $authList = $authModel->authList();
        return $this->render('create', ['authList' => $authList]);
    }

    /**
     * 编辑
     *
     * @return string|\yii\web\Response
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Role();
            $post = Yii::$app->request->post();
            $res = $model->edit($post);
            return MsgUtil::dataFormat($res);
        }
        // 角色ID
        $role_id = Yii::$app->request->get('role_id');
        // 检查参数
        if (!$role_id) {
            return $this->redirect(['base/error']);
        }
        $model = Role::findOne(['role_id' => $role_id]);
        if (!$model) {
            return $this->redirect(['base/error']);
        }
        $authModel = new Auth();
        $authList = $authModel->authList();

        // 角色拥有的权限
        $itemList = RoleAuthItem::find()->where(['role_id' => $role_id])->select(['auth_id'])->asArray()->all();
        $itemList = array_column($itemList, 'auth_id');
        return $this->render('update', ['authList' => $authList, 'model' => $model, 'itemList' => $itemList]);
    }

    /**
     * 删除
     *
     * @return string
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDel()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Role();
            $post = Yii::$app->request->post();
            $res = $model->del($post);
            return MsgUtil::dataFormat($res);
        }
    }
}