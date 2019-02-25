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

use backend\models\Admin;
use common\models\MsgUtil;
use Yii;
use yii\web\Controller;

/**
 * LoginController 登录控制器
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    wangyaxian <1822581649@qq.com>
 * @link      https://github.com/duiying/Yii2-Admin
 */
class LoginController extends Controller
{
    /**
     * 登录
     *
     * @return string
     */
    public function actionLogin()
    {
        if (Yii::$app->request->isAjax) {
            $model = new Admin();
            $post = Yii::$app->request->post();
            $res = $model->login($post);
            return MsgUtil::dataFormat($res);
        }
        return $this->render('login');
    }

    public function actionLogout()
    {
        Yii::$app->user->logout(false);
        return MsgUtil::dataFormat([MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG]);
    }
}