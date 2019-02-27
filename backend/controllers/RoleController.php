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
use Yii;
use backend\models\Admin;
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
}