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

namespace common\models;

/**
 * 格式化AJAX返回的数据
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    wangyaxian <1822581649@qq.com>
 * @link      https://github.com/duiying/Yii2-Admin
 */
class MsgUtil
{
    /**
     * 成功状态码
     */
    const SUCCESS_CODE = '200';

    /**
     * 失败状态码
     */
    const FAIL_CODE = '201';

    const SUCCESS_MSG = '操作成功';
    const FAIL_MSG = '操作失败';
    const FAIL_VALIDATE = '数据格式校验失败';
    const NAME_OR_PASS_ERROR = '用户名或者密码错误';
    const PASS_ERROR = '原密码输入错误';
    const SAVE_FAIL = '数据保存失败';
    const UPDATE_FAIL = '数据更新失败';
    const CHILD_ERROR = '请先删除子级权限';

    /**
     * 格式化AJAX返回的数据
     *
     * @param array $arr['状态码', '提示信息']
     * @return string
     */
    public static function dataFormat($arr = [])
    {
        return json_encode(['code' => $arr[0], 'msg' => $arr[1]]);
    }
}