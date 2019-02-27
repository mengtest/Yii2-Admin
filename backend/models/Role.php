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

            ['role_name', 'required', 'message' => '角色名称不能为空', 'on' => ['create']],
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

}
