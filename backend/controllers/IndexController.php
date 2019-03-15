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
use Yii;
use backend\controllers\BaseController;

/**
 * IndexController 首页控制器
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    wangyaxian <1822581649@qq.com>
 * @link      https://github.com/duiying/Yii2-Admin
 */
class IndexController extends BaseController
{
    /**
     * 首页
     *
     * @return string
     */
    public function actionIndex()
    {
        // 首页菜单
        $menu = Auth::getMenu();
        return $this->render('index', ['menu' => $menu]);
    }

    /**
     * 欢迎页
     *
     * @return string
     */
    public function actionWelcome()
    {
        return $this->render('welcome', [
            'info' => Yii::$app->system->getInfo()
        ]);
    }

    /**
     * 图标对应字体
     *
     * @return string
     */
    public function actionIcon()
    {
        return $this->render('icon');
    }
}