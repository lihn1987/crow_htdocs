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

 Date: 01/03/2019 16:43:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for product_list
-- ----------------------------
DROP TABLE IF EXISTS `product_list`;
CREATE TABLE `product_list` (
  `index` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `income` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of product_list
-- ----------------------------
BEGIN;
INSERT INTO `product_list` VALUES (1, 'title', 'price', 'income');
INSERT INTO `product_list` VALUES (2, 'title1', 'price1', 'income1');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
