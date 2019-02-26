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

use Yii;
use yii\db\ActiveRecord;
use common\models\MsgUtil;

/**
 * This is the model class for table "auth".
 *
 * @property string $auth_id 权限ID
 * @property string $auth_name 权限名称
 * @property int $auth_pid 父级ID
 * @property string $auth_controller 控制器
 * @property string $auth_action 方法
 * @property string $auth_sort 排序
 */
class Auth extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_pid', 'auth_sort'], 'integer'],
            [['auth_name', 'auth_controller', 'auth_action'], 'string', 'max' => 32],
            [['auth_name'], 'required', 'message' => '权限名称不能为空', 'on' => ['create']],
            [['auth_pid'], 'required', 'message' => '父级权限不能为空', 'on' => ['create']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'auth_id' => 'Auth ID',
            'auth_name' => 'Auth Name',
            'auth_pid' => 'Auth Pid',
            'auth_controller' => 'Auth Controller',
            'auth_action' => 'Auth Action',
            'auth_sort' => 'Auth Sort',
        ];
    }

    public function create($post)
    {
        $this->scenario = 'create';

        if ($this->load($post, '') && $this->validate()) {
            if ($this->save()) {
                // 保存成功
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }

            // 用户名或密码错误
            return [MsgUtil::FAIL_CODE, MsgUtil::SAVE_FAIL];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 父级权限
     *
     * @return string
     */
    public static function pidList()
    {
        // 查询字段
        $field = ['auth_id', 'auth_name', 'auth_pid'];

        // 排序规则
        $orderBy = ['auth_pid' => SORT_ASC, 'auth_sort' => SORT_ASC];

        // 一级权限
        $pidListOne = self::find()
            ->select($field)
            ->where(['auth_pid' => 0])
            ->orderBy($orderBy)
            ->asArray()
            ->all();

        // 二级权限
        $pidListTwo = self::find()
            ->select($field)
            ->where(['in', 'auth_pid', array_column($pidListOne, 'auth_id')])
            ->orderBy($orderBy)
            ->asArray()
            ->all();

        // 字符串拼装
        $pidList = '';
        foreach ($pidListOne as $k => $v) {
            $pidList .= "<option value='". $v['auth_id'] ."'>|----" . $v['auth_name'] . "</option>";
            foreach ($pidListTwo as $kk => $vv) {
                if ($vv['auth_pid'] == $v['auth_id']) {
                    $pidList .= "<option value='". $vv['auth_id'] ."'>|----|----" . $vv['auth_name'] . "</option>";
                }
            }
        }

        return $pidList;
    }
}
