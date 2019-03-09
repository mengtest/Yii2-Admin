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
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property string $admin_id 用户ID
 * @property string $admin_name 用户名
 * @property string $admin_pass 密码
 * @property string $admin_email 邮箱
 * @property string $role_id 角色ID
 * @property int $login_time 登录时间
 * @property string $login_ip 最近一次登录IP
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    public $newPass;
    public $rePass;
    public $time;
    public $token;

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
            [['admin_pass', 'admin_email'], 'string', 'max' => 64],
            [['login_ip'], 'string', 'max' => 20],

            ['admin_name', 'required', 'message' => '用户名不能为空', 'on' => ['login', 'create', 'edit', 'forgetPass', 'resetPass']],
            ['admin_pass', 'required', 'message' => '密码不能为空', 'on' => ['login', 'changePass', 'create']],
            ['admin_email', 'required', 'message' => '邮箱不能为空', 'on' => ['create', 'edit', 'changeInfo', 'forgetPass']],
            ['newPass', 'required', 'message' => '新密码不能为空', 'on' => ['changePass', 'resetPass']],
            ['rePass', 'required', 'message' => '确认密码不能为空', 'on' => ['changePass', 'create', 'resetPass']],
            ['rePass', 'compare', 'compareAttribute' => 'newPass', 'message' => '两次密码输入不一致', 'on' => ['changePass', 'resetPass']],
            ['rePass', 'compare', 'compareAttribute' => 'admin_pass', 'message' => '两次密码输入不一致', 'on' => ['create']],
            ['admin_id', 'required', 'message' => 'ID不能为空', 'on' => ['edit', 'del']],
            ['role_id', 'required', 'message' => '角色不能为空', 'on' => ['create', 'edit']],
            ['time', 'required', 'message' => '时间戳不能为空', 'on' => ['resetPass']],
            ['token', 'required', 'message' => 'Token不能为空', 'on' => ['resetPass']],
        ];
    }

    public function getRole()
    {
        return $this->hasOne(Role::className(), ['role_id' => 'role_id']);
    }

    /**
     * 忘记密码
     *
     * @param $post
     * @return array|bool
     */
    public function forgetPass($post)
    {
        $this->scenario = 'forgetPass';

        if ($this->load($post, '') && $this->validate()) {
            // 检查用户名和邮箱是否匹配
            $model = self::findOne(['admin_name' => $this->admin_name, 'admin_email' => $this->admin_email]);
            if (!$model) {
                return [MsgUtil::FAIL_CODE, MsgUtil::NAME_OR_EMAIL_ERROR];
            }

            // 时间戳
            $time = time();
            // token
            $token = Yii::$app->token->generateToken($this->admin_name, $time);

            $mailer = Yii::$app->mailer->compose('forget-pass', ['admin_name' => $this->admin_name, 'time' => $time, 'token' => $token]);
            $mailer->setTo($this->admin_email);
            $mailer->setSubject("Yii2-Admin 找回密码");
            if ($mailer->send()) {
                return [MsgUtil::SUCCESS_CODE, MsgUtil::MAIL_SEND_SUCCESS];
            }
            return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_MSG];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 重置密码
     *
     * @param $post
     * @return array
     * @throws \yii\base\Exception
     */
    public function resetPass($post)
    {
        $this->scenario = 'resetPass';

        if ($this->load($post, '') && $this->validate()) {
            // Token验证
            if (Yii::$app->token->generateToken($this->admin_name, $this->time) != $this->token) {
                return [MsgUtil::FAIL_CODE, MsgUtil::TOKEN_ERROR];
            }

            // 重置密码链接10分钟后过期
            if (time() - $this->time > 60 * 10) {
                return [MsgUtil::FAIL_CODE, MsgUtil::LINK_EXPIRE];
            }

            $model = self::findOne(['admin_name' => $this->admin_name]);
            $model->admin_pass = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
            if ($model->save()) {
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }

            return [MsgUtil::FAIL_CODE, MsgUtil::SAVE_FAIL];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
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
     * 修改信息
     *
     * @param $post
     * @return array
     */
    public function changeInfo($post)
    {
        $this->scenario = 'changeInfo';

        if ($this->load($post, '') && $this->validate()) {
            $where = ['admin_id' => Yii::$app->user->identity->getId()];
            $model = self::findOne($where);
            $model->admin_email = $this->admin_email;
            if ($model->save()) {
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }
            return [MsgUtil::FAIL_CODE, MsgUtil::SAVE_FAIL];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 列表
     *
     * @return array
     */
    public function index()
    {
        $model = self::find();
        $totalCount = $model->count();
        $pageSize = Yii::$app->params['pageSize']['admin'];
        $pager = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
        // 查询的字段
        $select = ['admin_id', 'admin_name', 'admin_email', 'role_id', 'login_time', 'login_ip'];

        $list = $model->offset($pager->offset)->limit($pager->limit)->select($select)->all();

        return ['list' => $list, 'pager' => $pager];
    }

    /**
     * 添加
     *
     * @param $post
     * @return array
     * @throws \yii\base\Exception
     */
    public function create($post)
    {
        $this->scenario = 'create';

        if ($this->load($post, '') && $this->validate()) {
            // 检查角色是否存在
            if (!Role::findOne(['role_id' => $this->role_id])) {
                return [MsgUtil::FAIL_CODE, MsgUtil::PARAM_ERROR];
            }

            // 检查用户名是否存在
            if (self::findOne(['admin_name' => $this->admin_name])) {
                return [MsgUtil::FAIL_CODE, MsgUtil::ADMIN_NAME_EXIST];
            }

            $model = new self();
            $model->admin_name = $this->admin_name;
            $model->admin_pass = Yii::$app->getSecurity()->generatePasswordHash($this->admin_pass);
            $model->admin_email = $this->admin_email;
            $model->role_id = $this->role_id;
            if ($model->save()) {
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }
            return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_MSG];
        }

        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 编辑
     *
     * @param $post
     * @return array
     * @throws \yii\base\Exception
     */
    public function edit($post)
    {
        $this->scenario = 'edit';

        if ($this->load($post, '') && $this->validate()) {
            // 检查角色是否存在
            if (!Role::findOne(['role_id' => $this->role_id])) {
                return [MsgUtil::FAIL_CODE, MsgUtil::PARAM_ERROR];
            }

            // 检查用户名是否存在
            $nameExist = self::find()
                ->where(['admin_name' => $this->admin_name])
                ->andWhere(['<>', 'admin_id', $this->admin_id])
                ->one();
            if ($nameExist) {
                return [MsgUtil::FAIL_CODE, MsgUtil::ADMIN_NAME_EXIST];
            }

            $model = self::findOne(['admin_id' => $this->admin_id]);
            $model->admin_name = $this->admin_name;
            $model->admin_email = $this->admin_email;
            $model->role_id = $this->role_id;
            if ($model->save()) {
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }
            return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_MSG];
        }

        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 删除
     *
     * @param $post
     * @return array
     * @throws \Throwable
     */
    public function del($post)
    {
        $this->scenario = 'del';

        if ($this->load($post, '') && $this->validate()) {
            // 不允许删除当前登录用户
            if (Yii::$app->user->getId() == $this->admin_id) {
                return [MsgUtil::FAIL_CODE, MsgUtil::SELF_DEL];
            }

            $model = self::findOne(['admin_id' => $this->admin_id]);

            // 不允许删除admin用户
            if ($model->admin_name == 'admin') {
                return [MsgUtil::FAIL_CODE, MsgUtil::ADMIN_DEL];
            }

            if ($model->delete()) {
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }

            return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_MSG];
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
