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
        return $this->render('index');
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
}