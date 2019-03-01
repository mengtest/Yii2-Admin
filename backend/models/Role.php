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
use yii\db\Exception;
use common\models\MsgUtil;

/**
 * This is the model class for table "role".
 *
 * @property string $role_id 角色ID
 * @property string $role_name 名称
 * @property string $role_desc 角色描述
 * @property string $role_sort 角色排序, 默认10, 升序排列
 */
class Role extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_sort'], 'integer'],
            [['role_name'], 'string', 'max' => 32],
            [['role_desc'], 'string', 'max' => 255],

            ['role_name', 'required', 'message' => '角色名称不能为空', 'on' => ['create', 'edit']],
            ['role_id', 'required', 'message' => '角色ID不能为空', 'on' => ['edit', 'del']],
        ];
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
        $pageSize = 2;
        $pager = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
        $list = $model->offset($pager->offset)->limit($pager->limit)->all();

        return ['list' => $list, 'pager' => $pager];
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
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if (!$this->save()) {
                    throw new Exception('保存失败');
                }

                $role_id = $this->attributes['role_id'];
                foreach ($post['auth_id'] as $auth_id) {
                    $roleAuthItemModel = new RoleAuthItem();
                    $roleAuthItemModel->role_id = $role_id;
                    $roleAuthItemModel->auth_id = $auth_id;
                    if (!$roleAuthItemModel->save()) {
                        throw new Exception('保存失败');
                    }
                }

                $transaction->commit();
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            } catch (\Exception $exception) {
                $transaction->rollBack();
                return [MsgUtil::FAIL_CODE, MsgUtil::SAVE_FAIL];
            }
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
            $model = self::findOne(['role_id' => $this->role_id]);
            if (!$model) {
                // 数据格式校验失败
                return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->role_name = $this->role_name;
                $model->role_desc = $this->role_desc;
                $model->role_sort = $this->role_sort;
                if (!$model->save()) {
                    throw new Exception('保存失败');
                }

                // 删除角色原来的权限
                $itemList = RoleAuthItem::findAll(['role_id' => $this->role_id]);
                if ($itemList && !RoleAuthItem::deleteAll(['role_id' => $this->role_id])) {
                    throw new Exception('删除失败');
                }

                // 角色新增权限
                $role_id = $this->attributes['role_id'];
                foreach ($post['auth_id'] as $auth_id) {
                    $roleAuthItemModel = new RoleAuthItem();
                    $roleAuthItemModel->role_id = $role_id;
                    $roleAuthItemModel->auth_id = $auth_id;
                    if (!$roleAuthItemModel->save()) {
                        throw new Exception('保存失败');
                    }
                }

                $transaction->commit();
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            } catch (\Exception $exception) {
                $transaction->rollBack();
                return [MsgUtil::FAIL_CODE, MsgUtil::SAVE_FAIL];
            }
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
            $model = self::findOne(['role_id' => $this->role_id]);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 删除角色
                $res = $model->delete();
                if (!$res) {
                    throw new Exception('删除失败');
                }

                // 删除角色的权限
                $itemList = RoleAuthItem::findAll(['role_id' => $this->role_id]);
                if ($itemList && !RoleAuthItem::deleteAll(['role_id' => $this->role_id])) {
                    throw new Exception('删除失败');
                }

                $transaction->commit();
                return [MsgUtil::SUCCESS_CODE, MsgUtil::SUCCESS_MSG];
            } catch (\Exception $exception) {
                $transaction->rollBack();
                return [MsgUtil::FAIL_CODE, MsgUtil::DEL_FAIL];
            }
        }

        // 数据格式校验失败
        return [MsgUtil::FAIL_CODE, MsgUtil::FAIL_VALIDATE];
    }

    /**
     * 角色列表
     *
     * @return array|ActiveRecord[]
     */
    public function roleList()
    {
        $select = ['role_id', 'role_name'];
        return self::find()->select($select)->orderBy(['role_sort' => SORT_ASC])->all();
    }
}
