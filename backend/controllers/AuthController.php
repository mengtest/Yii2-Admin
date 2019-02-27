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

use Yii;
use backend\models\Auth;
use common\models\MsgUtil;
use backend\controllers\BaseController;

/**
 * 权限控制器
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    wangyaxian <1822581649@qq.com>
 * @link      https://github.com/duiying/Yii2-Admin
 */
class AuthController extends BaseController
{
    /**
     * 列表
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Auth;
        $list = $model->index();
        return $this->render("index", ['list' => $list]);
    }

    /**
     * 添加
     *
     * @return string
     */
    public function actionCreate()
    {
        $pidList = Auth::pidList();
        if (Yii::$app->request->isAjax) {
            $model = new Auth();
            $post = Yii::$app->request->post();
            $res = $model->create($post);
            return MsgUtil::dataFormat($res);
        }
        return $this->render('create', ['pidList' => $pidList]);
    }

    /**
     * 编辑
     *
     * @return string|\yii\web\Response
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Auth();
            $post = Yii::$app->request->post();
            $res = $model->edit($post);
            return MsgUtil::dataFormat($res);
        }

        $auth_id = Yii::$app->request->get('auth_id');
        if (!$auth_id) {
            return $this->redirect(['base/error']);
        }
        $model = Auth::findOne(['auth_id' => $auth_id]);
        if (!$model) {
            return $this->redirect(['base/error']);
        }
        $pidList = Auth::pidList($model->auth_pid);
        return $this->render('update', ['model' => $model, 'pidList' => $pidList]);
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
            $model = new Auth();
            $post = Yii::$app->request->post();
            $res = $model->del($post);
            return MsgUtil::dataFormat($res);
        }
    }
}