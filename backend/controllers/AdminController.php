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
}