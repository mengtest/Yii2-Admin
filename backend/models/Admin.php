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

namespace backend\models;

use common\models\MsgUtil;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property string $admin_id 用户ID
 * @property string $admin_name 用户名
 * @property string $admin_pass 密码
 * @property string $role_id 角色ID
 * @property int $login_time 登录时间
 * @property string $login_ip 最近一次登录IP
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    public $newPass;
    public $rePass;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'login_time'], 'integer'],
            [['admin_name'], 'string', 'max' => 32],
            [['admin_pass'], 'string', 'max' => 64],
            [['login_ip'], 'string', 'max' => 20],

            ['admin_name', 'required', 'message' => '用户名不能为空', 'on' => ['login']],
            ['admin_pass', 'required', 'message' => '密码不能为空', 'on' => ['login', 'changePass']],
            ['newPass', 'required', 'message' => '新密码不能为空', 'on' => ['changePass']],
            ['rePass', 'required', 'message' => '确认密码不能为空', 'on' => ['changePass']],
            ['rePass', 'compare', 'compareAttribute' => 'newPass', 'message' => '两次密码输入不一致', 'on' => ['changePass']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => 'Admin ID',
            'admin_name' => 'Admin Name',
            'admin_pass' => 'Admin Pass',
            'role_id' => 'Role ID',
            'login_time' => 'Login Time',
            'login_ip' => 'Login Ip',
        ];
    }

    /**
     * 登录
     *
     * @param $post
     * @return array
     */
    public function login($post)
    {
        $this->scenario = 'login';

        if ($this->load($post, '') && $this->validate()) {
            $where = ['admin_name' => $this->admin_name];
            $model = self::findOne($where);
            if ($model && Yii::$app->getSecurity()->validatePassword($this->admin_pass, $model->admin_pass)) {
                // 登录成功
                Yii::$app->user->login($model);
                $model->login_ip = Yii::$app->request->userIP;
                $model->login_time = time();
                $model->save();
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }

            // 用户名或密码错误
            return [MsgUtil::FAIL_CODE, MsgUtil::NAME_OR_PASS_ERROR];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 修改密码
     *
     * @param $post
     * @return array
     * @throws \yii\base\Exception
     */
    public function changePass($post)
    {
        $this->scenario = 'changePass';

        if ($this->load($post, '') && $this->validate()) {
            $where = ['admin_id' => Yii::$app->user->identity->getId()];
            $model = self::findOne($where);
            if ($model && Yii::$app->getSecurity()->validatePassword($this->admin_pass, $model->admin_pass)) {
                // 原密码输入正确
                $model->admin_pass = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
                if ($model->save()) {
                    return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
                }
                return [MsgUtil::FAIL_CODE, MsgUtil::SAVE_FAIL];
            }

            // 用户名或密码错误
            return [MsgUtil::FAIL_CODE, MsgUtil::PASS_ERROR];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 根据给到的ID查询身份
     *
     * @param string|integer $id 被查询的ID
     * @return IdentityInterface|null 通过ID匹配到的身份对象
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * 根据 token 查询身份
     *
     * @param string $token 被查询的 token
     * @return IdentityInterface|null 通过 token 得到的身份对象
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * 获取该认证实例表示的用户的ID
     *
     * @return int|string 当前用户ID
     */
    public function getId()
    {
        return $this->admin_id;
    }

    /**
     * 获取基于cookie登录时使用的认证密钥
     *
     * @return string 当前用户的(cookie)认证密钥
     */
    public function getAuthKey()
    {
        return '';
    }

    /**
     * 基于cookie登录密钥的验证的逻辑的实现
     *
     * @param string $authKey 当前用户的(cookie)认证密钥
     * @return boolean
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }
}
