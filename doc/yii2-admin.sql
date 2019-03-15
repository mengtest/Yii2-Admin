/*
Navicat MySQL Data Transfer

Source Server         : phpstudy
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : yii2-admin

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-03-15 12:55:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `admin_name` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `admin_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `admin_email` varchar(64) NOT NULL DEFAULT '' COMMENT '邮箱',
  `role_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `login_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '最近一次登录IP',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '$2y$13$c7B970djkM5VdKvupOeqjezuxVnKSBR/xmUBfzxvYdqSugGjnOely', '1822581649@qq.com', '1', '1552625282', '127.0.0.1');
INSERT INTO `admin` VALUES ('4', 'youke', '$2y$13$W3l.1dVz1cfDo2lBpdbOPeX7aCBor3WUmz7yxhHCA2L7nRq0ruS9m', '3157392850@qq.com', '9', '1552625332', '127.0.0.1');

-- ----------------------------
-- Table structure for auth
-- ----------------------------
DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth` (
  `auth_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限ID',
  `auth_name` varchar(32) NOT NULL DEFAULT '' COMMENT '权限名称',
  `auth_pid` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `auth_controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `auth_action` varchar(32) NOT NULL DEFAULT '' COMMENT '方法',
  `auth_sort` int(11) unsigned NOT NULL DEFAULT '10' COMMENT '排序',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='权限表';

-- ----------------------------
-- Records of auth
-- ----------------------------
INSERT INTO `auth` VALUES ('1', '权限管理', '0', '', '', '10');
INSERT INTO `auth` VALUES ('2', '图标字体', '0', '', '', '10');
INSERT INTO `auth` VALUES ('3', '管理员管理', '1', 'admin', 'index', '10');
INSERT INTO `auth` VALUES ('5', '权限管理', '1', 'auth', 'index', '8');
INSERT INTO `auth` VALUES ('7', '图标对应字体', '2', 'index', 'icon', '10');
INSERT INTO `auth` VALUES ('9', '编辑', '5', 'auth', 'update', '9');
INSERT INTO `auth` VALUES ('11', '角色管理', '1', 'role', 'index', '9');
INSERT INTO `auth` VALUES ('12', '删除', '11', 'role', 'del', '10');
INSERT INTO `auth` VALUES ('13', '编辑', '11', 'role', 'update', '10');
INSERT INTO `auth` VALUES ('16', '删除', '5', 'auth', 'del', '10');
INSERT INTO `auth` VALUES ('17', '添加', '5', 'auth', 'create', '10');
INSERT INTO `auth` VALUES ('19', '添加', '11', 'role', 'create', '10');
INSERT INTO `auth` VALUES ('20', '添加', '3', 'admin', 'create', '10');
INSERT INTO `auth` VALUES ('21', '编辑', '3', 'admin', 'update', '10');
INSERT INTO `auth` VALUES ('22', '删除', '3', 'admin', 'del', '10');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `role_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '角色描述',
  `role_sort` int(11) unsigned NOT NULL DEFAULT '10' COMMENT '角色排序, 默认10, 升序排列',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='角色表';

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', '超级管理员', '拥有所有权限', '10');
INSERT INTO `role` VALUES ('9', '游客', '游客拥有浏览权限, 没有删除/编辑/添加权限', '9');

-- ----------------------------
-- Table structure for role_auth_item
-- ----------------------------
DROP TABLE IF EXISTS `role_auth_item`;
CREATE TABLE `role_auth_item` (
  `item_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `role_id` int(11) unsigned NOT NULL COMMENT '角色ID',
  `auth_id` int(11) unsigned NOT NULL COMMENT '权限ID',
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8 COMMENT='角色权限表';

-- ----------------------------
-- Records of role_auth_item
-- ----------------------------
INSERT INTO `role_auth_item` VALUES ('39', '8', '1');
INSERT INTO `role_auth_item` VALUES ('40', '8', '2');
INSERT INTO `role_auth_item` VALUES ('139', '9', '1');
INSERT INTO `role_auth_item` VALUES ('140', '9', '5');
INSERT INTO `role_auth_item` VALUES ('141', '9', '11');
INSERT INTO `role_auth_item` VALUES ('142', '9', '13');
INSERT INTO `role_auth_item` VALUES ('143', '9', '3');
INSERT INTO `role_auth_item` VALUES ('144', '9', '2');
INSERT INTO `role_auth_item` VALUES ('145', '9', '7');
