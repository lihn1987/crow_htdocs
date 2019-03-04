/*
 Navicat Premium Data Transfer

 Source Server         : ddd
 Source Server Type    : MySQL
 Source Server Version : 100136
 Source Host           : localhost:3306
 Source Schema         : crow_cloud

 Target Server Type    : MySQL
 Target Server Version : 100136
 File Encoding         : 65001

 Date: 04/03/2019 20:01:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for order_list
-- ----------------------------
DROP TABLE IF EXISTS `order_list`;
CREATE TABLE `order_list` (
  `index` int(255) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_count` varchar(255) DEFAULT NULL,
  `buy_time` char(255) DEFAULT NULL,
  `is_history` varchar(255) DEFAULT NULL,
  `is_paid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`index`),
  KEY `g` (`user_id`),
  KEY `d` (`product_id`),
  CONSTRAINT `d` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`index`),
  CONSTRAINT `g` FOREIGN KEY (`user_id`) REFERENCES `user_list` (`index`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of order_list
-- ----------------------------
BEGIN;
INSERT INTO `order_list` VALUES (2, 1, 1, '2', '20190302-134239', '0', '1');
INSERT INTO `order_list` VALUES (3, 1, 1, '3', '20190303-134715', '0', '1');
INSERT INTO `order_list` VALUES (4, 1, 1, '2', '20190302-134239', '1', '1');
COMMIT;

-- ----------------------------
-- Table structure for product_list
-- ----------------------------
DROP TABLE IF EXISTS `product_list`;
CREATE TABLE `product_list` (
  `index` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `income` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `is_history` varchar(255) DEFAULT NULL,
  `count` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product_list
-- ----------------------------
BEGIN;
INSERT INTO `product_list` VALUES (1, '3月期云养牛.太平洋承保', '8800', '10.88%', '90', '0', '994');
INSERT INTO `product_list` VALUES (2, '6月期云养牛.太平洋承保', '8700', '10.88%', '180', '0', '1000');
INSERT INTO `product_list` VALUES (3, '9月期云养牛.太平洋承保', '8600', '10.88%', '270', '0', '1000');
INSERT INTO `product_list` VALUES (4, '12月期云养牛.太平洋承保', '8500', '10.88%', '360', '0', '1000');
COMMIT;

-- ----------------------------
-- Table structure for user_list
-- ----------------------------
DROP TABLE IF EXISTS `user_list`;
CREATE TABLE `user_list` (
  `index` int(255) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `last_login_time` varchar(255) DEFAULT NULL,
  `wallet` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`index`,`user_name`) USING BTREE,
  KEY `index` (`index`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_list
-- ----------------------------
BEGIN;
INSERT INTO `user_list` VALUES (1, '18108192376', '20190304-194745', '947200');
COMMIT;

-- ----------------------------
-- View structure for user_order_list
-- ----------------------------
DROP VIEW IF EXISTS `user_order_list`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `user_order_list` AS select `product_list`.`title` AS `title`,`product_list`.`price` AS `price`,`product_list`.`income` AS `income`,`product_list`.`time` AS `time`,`order_list`.`buy_time` AS `buy_time`,`user_list`.`user_name` AS `user_name`,`order_list`.`product_count` AS `product_count`,`order_list`.`is_history` AS `is_history`,`order_list`.`is_paid` AS `is_paid`,`order_list`.`product_id` AS `product_id`,`order_list`.`user_id` AS `user_id` from ((`order_list` join `user_list` on((`order_list`.`user_id` = `user_list`.`index`))) join `product_list` on((`order_list`.`product_id` = `product_list`.`index`)));

SET FOREIGN_KEY_CHECKS = 1;
