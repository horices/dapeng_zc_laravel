/*
Navicat MySQL Data Transfer

Source Server         : 设计学院
Source Server Version : 50619
Source Host           : test.bzr.dapengjiaoyu.com:3306
Source Database       : test_bzr_main

Target Server Type    : MYSQL
Target Server Version : 50619
File Encoding         : 65001

Date: 2017-09-13 09:20:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `course_package`
-- ----------------------------
DROP TABLE IF EXISTS `course_package`;
CREATE TABLE `course_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '套餐标题',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0主套餐 1附加套餐',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '关联用户ID',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '套餐总金额',
  `course_id` varchar(255) NOT NULL DEFAULT '' COMMENT '包含课程id，逗号隔开',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COMMENT='课程套餐表';

-- ----------------------------
-- Records of course_package
-- ----------------------------


-- ----------------------------
-- Table structure for `rebate_activity`
-- ----------------------------
DROP TABLE IF EXISTS `rebate_activity`;
CREATE TABLE `rebate_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '创建者ID,外键关联user_headmaster的UID',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '活动标题',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠价格',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '活动开始时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '活动结束时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='优惠活动表';

-- ----------------------------
-- Records of rebate_activity
-- ----------------------------


-- ----------------------------
-- Table structure for `user_pay`
-- ----------------------------
DROP TABLE IF EXISTS `user_pay`;
CREATE TABLE `user_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '关联用户ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `registration_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联报名记录表ID',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联课程套餐表ID',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号',
  `qq` varchar(15) NOT NULL DEFAULT '' COMMENT '学员qq',
  `rebate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '学员支付备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `adviser_id` int(11) NOT NULL DEFAULT '0' COMMENT '课程顾问ID',
  `adviser_name` varchar(15) NOT NULL DEFAULT '' COMMENT '课程顾问姓名',
  `adviser_qq` varchar(15) NOT NULL DEFAULT '0' COMMENT '课程顾问qq',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '学员姓名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8 COMMENT='用户支付记录表';

-- ----------------------------
-- Records of user_pay
-- ----------------------------

-- ----------------------------
-- Table structure for `user_pay_log`
-- ----------------------------
DROP TABLE IF EXISTS `user_pay_log`;
CREATE TABLE `user_pay_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '关联用户ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `pay_type` varchar(10) NOT NULL DEFAULT '' COMMENT '支付方式',
  `pay_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联支付记录表ID',
  `registration_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联报名记录表ID',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联课程套餐表ID',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号',
  `qq` varchar(15) NOT NULL DEFAULT '' COMMENT '学员qq',
  `pay_time` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '学员支付备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `adviser_id` int(11) NOT NULL DEFAULT '0' COMMENT '课程顾问ID',
  `adviser_name` varchar(15) NOT NULL DEFAULT '' COMMENT '课程顾问姓名',
  `adviser_qq` varchar(15) NOT NULL DEFAULT '' COMMENT '课程顾问qq',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '学员姓名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8 COMMENT='用户支付记录表';

-- ----------------------------
-- Records of user_pay_log
-- ----------------------------


-- ----------------------------
-- Table structure for `user_registration`
-- ----------------------------
DROP TABLE IF EXISTS `user_registration`;
CREATE TABLE `user_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '关联用户ID',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '学员姓名',
  `adviser_id` int(11) NOT NULL DEFAULT '0' COMMENT '课程顾问ID',
  `adviser_name` varchar(15) NOT NULL DEFAULT '' COMMENT '课程顾问姓名',
  `adviser_qq` varchar(15) NOT NULL DEFAULT '' COMMENT '课程顾问qq',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `qq` varchar(15) NOT NULL DEFAULT '' COMMENT '学员qq',
  `is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否时最新开通',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联的课程套餐ID',
  `package_attach_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联附加套餐',
  `give_id` varchar(20) NOT NULL DEFAULT '' COMMENT '关联赠送课程ID,逗号隔开',
  `rebate_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联优惠活动的',
  `package_all_title` varchar(100) NOT NULL DEFAULT '' COMMENT '报名套餐组合成的名字',
  `amount_submitted` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '已提交的金额',
  `package_total_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '套餐总金额',
  `reg_time` int(11) NOT NULL DEFAULT '0' COMMENT '报名时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '报名备注',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `rebate` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `is_active` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用该报名记录',
  `fq_type` varchar(10) NOT NULL DEFAULT '' COMMENT '默认无,CASH:现金,HUABEI:花呗分期,MYFQ:蚂蚁分期',
  `client_submit` varchar(10) NOT NULL DEFAULT '' COMMENT '提交过来的客户端 PC WAP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8 COMMENT='用户报名表';

-- ----------------------------
-- Records of user_registration
-- ----------------------------
