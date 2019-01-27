/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50525
Source Host           : localhost:3306
Source Database       : db1

Target Server Type    : MYSQL
Target Server Version : 50525
File Encoding         : 65001

Date: 2019-01-27 17:19:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `password` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES (null, 'Україна, м.Львів, вул. Нижанківського, 5а', '	+38 (032) 261 00 12', '	office@bak.lviv.ua');
INSERT INTO `admin` VALUES (null, null, null, null);

-- ----------------------------
-- Table structure for authors
-- ----------------------------
DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(100) DEFAULT NULL COMMENT 'Автор',
  `photo` varchar(30) DEFAULT NULL,
  `describe` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`author_id`),
  UNIQUE KEY `author_id` (`author_id`,`author`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of authors
-- ----------------------------
INSERT INTO `authors` VALUES ('1', 'Джек Лондон', null, 'Jack London, справжнє ім\'я — Джон Ґріффіт Че́йні, англ. John Griffith Chaney; нар. 12 січня 1876, Сан-Франциско — помер 22 листопада 1916, Ґлен-Еллен, Каліфорнія) — американський письменник, громадський діяч, соціаліст. Найбільш відомий як автор пригодницьких оповідань і романів. Його знають, поважають і люблять діти і дорослі в різних країнах світу.');
INSERT INTO `authors` VALUES ('4', 'Стівен Кінг', null, 'американський письменник, автор більш ніж 200 творів, серед яких понад 50 книг-бестселерів у стилях жахи (англ. horror), фентезі, трилер, містика. Також писав під псевдонімом Річард Бахман (англ. Richard Bachman). Було продано більш ніж 350 млн копій його романів та збірок оповідань. На основі його історій знято низку фільмів, а також намальовані комікси. ');
INSERT INTO `authors` VALUES ('5', 'Агата Крісті', null, 'англійська письменниця, майстриня і одна з найвідоміших у світі представниць детективного жанру, творець класичних персонажів детективної літератури — Еркюля Пуаро та міс Марпл.');
INSERT INTO `authors` VALUES ('6', 'Святослав Караванський', null, 'f');
INSERT INTO `authors` VALUES ('7', 'Ева Гата', null, 'g');
INSERT INTO `authors` VALUES ('8', 'Віра Вовк', null, 'h');
INSERT INTO `authors` VALUES ('9', 'Ксенія Жукровська', null, null);
INSERT INTO `authors` VALUES ('10', null, null, null);

-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `book` varchar(100) NOT NULL DEFAULT '' COMMENT 'Книга',
  `author` int(11) DEFAULT NULL,
  `folder` int(11) DEFAULT NULL,
  `price` decimal(4,2) DEFAULT '0.00',
  `picture` varchar(30) DEFAULT NULL,
  `describe` text,
  PRIMARY KEY (`book_id`),
  UNIQUE KEY `book_id` (`book_id`),
  UNIQUE KEY `book_name` (`book`) USING BTREE,
  KEY `author` (`author`),
  CONSTRAINT `author` FOREIGN KEY (`author`) REFERENCES `authors` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of books
-- ----------------------------
INSERT INTO `books` VALUES ('0', 'Книга 1', '1', '0', '0.00', '100', 'Книга розкриває нам таємниці....');
INSERT INTO `books` VALUES ('1', 'Книга 2', '4', '0', '0.00', '120', 'Ми поринаємо в чудовий світ ....');
INSERT INTO `books` VALUES ('6', 'Книга 3', '4', '0', '0.00', '80', null);
INSERT INTO `books` VALUES ('7', 'Книга 4', '5', '0', '0.00', '90', null);
INSERT INTO `books` VALUES ('8', 'Книга 5', '5', '0', '0.00', '50', null);
INSERT INTO `books` VALUES ('9', 'Книга 6', '1', '0', '0.00', '100', null);
INSERT INTO `books` VALUES ('10', 'Книга 7', null, '0', '0.00', '120', null);

-- ----------------------------
-- Table structure for folders
-- ----------------------------
DROP TABLE IF EXISTS `folders`;
CREATE TABLE `folders` (
  `folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `folder` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`folder_id`),
  UNIQUE KEY `folder_id` (`folder_id`,`folder`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of folders
-- ----------------------------
INSERT INTO `folders` VALUES ('1', 'Дитяча література');
INSERT INTO `folders` VALUES ('2', 'Художня література');
INSERT INTO `folders` VALUES ('3', 'Освітня література');
INSERT INTO `folders` VALUES ('4', 'Технічна література');
INSERT INTO `folders` VALUES ('5', null);

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `header` varchar(255) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`news_id`),
  UNIQUE KEY `news_id` (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', '2019-01-16', 'Новина 1', 'новина 1 - івсів впів вап вап ва');
INSERT INTO `news` VALUES ('2', '2019-01-15', 'Новина 2', 'новина 2 - ваовл лвавал лвалвалд лівалмів лвілм лві');
INSERT INTO `news` VALUES ('3', '2019-01-01', 'Новина 3', 'новина 3 - вм укав а вапукпс укпсукп чкп укпсук пс чукп');

-- ----------------------------
-- Table structure for regions
-- ----------------------------
DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `region_id` int(11) NOT NULL AUTO_INCREMENT,
  `region` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`region_id`),
  UNIQUE KEY `region_id` (`region_id`),
  KEY `region_id_2` (`region_id`,`region`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of regions
-- ----------------------------
INSERT INTO `regions` VALUES ('1', 'Київ');
INSERT INTO `regions` VALUES ('2', 'Чернігів');
INSERT INTO `regions` VALUES ('3', 'Суми');
INSERT INTO `regions` VALUES ('4', 'Харків');
INSERT INTO `regions` VALUES ('5', 'Луганська обл.');
INSERT INTO `regions` VALUES ('6', 'Донецька обл.');
INSERT INTO `regions` VALUES ('7', 'Дніпропетровськ');
INSERT INTO `regions` VALUES ('8', 'Черкаси');
INSERT INTO `regions` VALUES ('9', 'Кіровоград');
INSERT INTO `regions` VALUES ('10', 'Запоріжжя');
INSERT INTO `regions` VALUES ('11', 'Одеса');
INSERT INTO `regions` VALUES ('12', 'Хесрон');
INSERT INTO `regions` VALUES ('13', 'Миколаїв');
INSERT INTO `regions` VALUES ('14', 'Вінниця');
INSERT INTO `regions` VALUES ('15', 'Житомир');
INSERT INTO `regions` VALUES ('16', 'Рівне');
INSERT INTO `regions` VALUES ('17', 'Луцьк');
INSERT INTO `regions` VALUES ('18', 'Львів');
INSERT INTO `regions` VALUES ('19', 'Івано-Франківськ');
INSERT INTO `regions` VALUES ('20', 'Чернівці');
INSERT INTO `regions` VALUES ('21', 'Тернопіль');
INSERT INTO `regions` VALUES ('22', 'Ужгород');
INSERT INTO `regions` VALUES ('23', 'Польща');
INSERT INTO `regions` VALUES ('24', 'Хмельницький');

-- ----------------------------
-- Table structure for shops
-- ----------------------------
DROP TABLE IF EXISTS `shops`;
CREATE TABLE `shops` (
  `shop_id` int(11) NOT NULL AUTO_INCREMENT,
  `www` tinytext,
  `url` varchar(100) DEFAULT NULL,
  `region` int(11) NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`shop_id`),
  UNIQUE KEY `shop_id` (`shop_id`),
  KEY `region` (`region`),
  CONSTRAINT `region` FOREIGN KEY (`region`) REFERENCES `regions` (`region_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shops
-- ----------------------------
INSERT INTO `shops` VALUES ('1', '1', 'knyga.com.ua', '2', 'інтернет магазин книжок');
INSERT INTO `shops` VALUES ('2', '1', 'book.com.ua', '1', 'інтернет-книгарня');
INSERT INTO `shops` VALUES ('3', '0', null, '18', 'Книгарня \"Джерело\", м.Львів, вул.Городоцька 54  066-2254545');
INSERT INTO `shops` VALUES ('4', '0', null, '1', 'Магазин \"Книжковий світ\" м.Київ, вул Хрещатик, 22   063-7894555');
SET FOREIGN_KEY_CHECKS=1;
