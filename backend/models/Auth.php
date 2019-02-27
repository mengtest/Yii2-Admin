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
use yii\data\Pagination;
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
            [['auth_name'], 'required', 'message' => '权限名称不能为空', 'on' => ['create', 'edit']],
            [['auth_pid'], 'required', 'message' => '父级权限不能为空', 'on' => ['create', 'edit']],
            ['auth_id', 'integer'],
            ['auth_id', 'required', 'message' => 'ID不能为空', 'on' => ['del']],
        ];
    }

    /**
     * 添加
     *
     * @param $post
     * @return array
     */
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
     * 编辑
     *
     * @param $post
     * @return array
     */
    public function edit($post)
    {
        $this->scenario = 'edit';
        if ($this->load($post, '') && $this->validate()) {
            $model = self::findOne(['auth_id' => $this->auth_id]);
            $model->auth_name = $this->auth_name;
            $model->auth_pid = $this->auth_pid;
            $model->auth_controller = $this->auth_controller;
            $model->auth_action = $this->auth_action;
            $model->auth_sort = $this->auth_sort;
            if ($model->save()) {
                // 更新成功
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }
            return [MsgUtil::FAIL_CODE, MsgUtil::UPDATE_FAIL];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 删除
     *
     * @param $post
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function del($post)
    {
        $this->scenario = 'del';

        if ($this->load($post, '') && $this->validate()) {
            $child = self::findOne(['auth_pid' => $this->auth_id]);
            if ($child) {
                return [MsgUtil::FAIL_CODE, MsgUtil::CHILD_ERROR];
            }
            $model = self::findOne(['auth_id' => $this->auth_id]);
            $res = $model->delete();
            if ($res) {
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            }
            return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_MSG];
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 父级权限
     *
     * @return string
     */
    public static function pidList($auth_pid = null)
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
        if (!$auth_pid) {
            foreach ($pidListOne as $k => $v) {
                $pidList .= "<option value='". $v['auth_id'] ."'>|----" . $v['auth_name'] . "</option>";
                foreach ($pidListTwo as $kk => $vv) {
                    if ($vv['auth_pid'] == $v['auth_id']) {
                        $pidList .= "<option value='". $vv['auth_id'] ."'>|----|----" . $vv['auth_name'] . "</option>";
                    }
                }
            }
        } else {
            foreach ($pidListOne as $k => $v) {
                if ($v['auth_id'] == $auth_pid) {
                    $pidList .= "<option value='". $v['auth_id'] ."' selected>|----" . $v['auth_name'] . "</option>";
                } else {
                    $pidList .= "<option value='". $v['auth_id'] ."'>|----" . $v['auth_name'] . "</option>";
                }

                foreach ($pidListTwo as $kk => $vv) {
                    if ($vv['auth_pid'] == $v['auth_id']) {
                        if ($vv['auth_id'] == $auth_pid) {
                            $pidList .= "<option value='" . $vv['auth_id'] . "' selected>|----|----" . $vv['auth_name'] . "</option>";
                        } else {
                            $pidList .= "<option value='" . $vv['auth_id'] . "'>|----|----" . $vv['auth_name'] . "</option>";
                        }
                    }
                }
            }
        }


        return $pidList;
    }

    /**
     * 列表
     *
     * @return array|mixed
     */
    public function index()
    {
        // 一级权限
        $model = self::find()->where(['auth_pid' => 0]);
        $pidOneList = $model->orderBy(['auth_sort' => SORT_ASC])->asArray()->all();
        $list = $this->getChild($pidOneList);
        $list = $this->setPrefix($list);
        return $list;
    }

    /**
     * 递归查询子级权限列表
     *
     * @param $list
     * @return array
     */
    public function getChild($list)
    {
        $tree = [];
        foreach ($list as $auth) {
            $tree[] = $auth;
            $childList = self::find()->where(['auth_pid' => $auth['auth_id']])->orderBy(['auth_sort' => SORT_ASC])->asArray()->all();
            $tree = array_merge($tree, $this->getChild($childList));
        }
        return $tree;
    }

    /**
     * 加前缀
     *
     * @param $tree
     * @return mixed
     */
    public function setPrefix($tree)
    {
        // 前缀
        $p = '|--------';

        // auth_pid => num
        $prefix = [0 => 0];
        $num = 0;
        foreach ($tree as $k => $v) {
            // 从第二个元素开始, 如果本元素和上一个元素的auth_pid不相同, $num++
            if ($k > 0) {
                if ($v['auth_pid'] != $tree[$k - 1]['auth_pid']) {
                    $num++;
                }
            }

            // 如果本元素的auth_pid已经在数组里面, 则$num从数组中取值
            if (array_key_exists($v['auth_pid'], $prefix)) {
                $num = $prefix[$v['auth_pid']];
            }
            // 如果本元素的auth_pid不在数组里面, 那么auth_pid和$num要赋值在$prefix数组里面
            else {
                $prefix[$v['auth_pid']] = $num;
            }

            $tree[$k]['auth_name'] = str_repeat($p, $num) . $v['auth_name'];
        }

        return $tree;
    }
}
