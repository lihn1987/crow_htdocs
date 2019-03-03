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

 Date: 04/03/2019 01:19:37
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
  `buy_time` char(255) DEFAULT NULL,
  `is_history` varchar(255) DEFAULT NULL,
  `is_paid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`index`),
  KEY `g` (`user_id`),
  KEY `d` (`product_id`),
  CONSTRAINT `d` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`index`),
  CONSTRAINT `g` FOREIGN KEY (`user_id`) REFERENCES `user_list` (`index`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of order_list
-- ----------------------------
BEGIN;
INSERT INTO `order_list` VALUES (1, 1, 1, '20190304-011805', '0', '1');
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
INSERT INTO `product_list` VALUES (1, '3月期云养牛.太平洋承保', '8800', '10.88%', '3', NULL, '999');
INSERT INTO `product_list` VALUES (2, '6月期云养牛.太平洋承保', '8700', '10.88%', '6', NULL, '1000');
INSERT INTO `product_list` VALUES (3, '9月期云养牛.太平洋承保', '8600', '10.88%', '9', NULL, '1000');
INSERT INTO `product_list` VALUES (4, '12月期云养牛.太平洋承保', '8500', '10.88%', '12', NULL, '1000');
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
  PRIMARY KEY (`index`,`user_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_list
-- ----------------------------
BEGIN;
INSERT INTO `user_list` VALUES (1, '18108192376', NULL, '991200');
COMMIT;

-- ----------------------------
-- View structure for order_list_current
-- ----------------------------
DROP VIEW IF EXISTS `order_list_current`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `order_list_current` AS select `product_list`.`title` AS `title`,`product_list`.`price` AS `price`,`product_list`.`income` AS `income`,`product_list`.`time` AS `time`,`product_list`.`is_history` AS `is_history`,`order_list`.`buy_time` AS `buy_time`,`user_list`.`user_name` AS `user_name` from ((`order_list` join `user_list` on((`order_list`.`user_id` = `user_list`.`index`))) join `product_list` on((`order_list`.`product_id` = `product_list`.`index`))) where (`order_list`.`is_history` = '0');

SET FOREIGN_KEY_CHECKS = 1;
