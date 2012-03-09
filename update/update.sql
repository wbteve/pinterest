2.0;
DROP TABLE IF EXISTS `%DB_PREFIX%adv_layout`;
CREATE TABLE `%DB_PREFIX%adv_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) DEFAULT NULL,
  `layout_id` varchar(255) DEFAULT NULL,
  `tmpl` varchar(255) DEFAULT NULL,
  `rec_id` int(11) DEFAULT NULL,
  `item_limit` int(11) DEFAULT NULL,
  `target_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`page`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%album`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` smallint(6) NOT NULL DEFAULT '0',
  `share_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(80) NOT NULL DEFAULT '',
  `show_type` tinyint(1) NOT NULL DEFAULT '0',
  `tags` text,
  `content` text,
  `photo_count` int(11) NOT NULL DEFAULT '0',
  `goods_count` int(11) NOT NULL DEFAULT '0',
  `img_count` int(11) NOT NULL DEFAULT '0',
  `best_count` int(11) NOT NULL DEFAULT '0',
  `collect_count` int(11) NOT NULL DEFAULT '0',
  `share_count` int(11) NOT NULL DEFAULT '0',
  `create_day` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `cache_data` text,
  `is_flash` tinyint(1) NOT NULL DEFAULT '0',
  `is_best` tinyint(1) NOT NULL DEFAULT '0',
  `flash_img` varchar(255) NOT NULL DEFAULT '',
  `best_img` varchar(255) NOT NULL DEFAULT '',
  `sort` smallint(5) NOT NULL DEFAULT '100',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`),
  KEY `status` (`status`),
  KEY `share_id` (`share_id`),
  KEY `is_best` (`is_best`),
  KEY `is_falsh` (`is_flash`),
  KEY `img_count` (`img_count`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `%DB_PREFIX%album_best`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album_best` (
  `album_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `best_day` int(11) NOT NULL DEFAULT '0',
  `best_time` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `aid_uid` (`album_id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%album_category`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album_category` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `img` varchar(255) NOT NULL DEFAULT '',
  `img_hover` varchar(255) NOT NULL DEFAULT '',
  `seo_title` varchar(255) NOT NULL DEFAULT '',
  `seo_keywords` text,
  `seo_desc` text,
  `sort` smallint(5) NOT NULL DEFAULT '10',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `%DB_PREFIX%album_category` (`id`, `name`, `img`, `img_hover`, `seo_title`, `seo_keywords`, `seo_desc`, `sort`, `status`) VALUES
(1, '时尚', './public/upload/images/201111/27/4ed1e6e200df4.png', './public/upload/images/201111/27/4ed1e7c940571.png', '', '', '', 100, 1),
(2, '美容', './public/upload/images/201111/27/4ed1e6f2a68d5.png', './public/upload/images/201111/27/4ed1e7da120e5.png', '', '', '', 100, 1),
(3, '购物', './public/upload/images/201111/27/4ed1e700ade32.png', './public/upload/images/201111/27/4ed1e7e7800fe.png', '', '', '', 100, 1),
(4, '生活', './public/upload/images/201111/27/4ed1e709c63b5.png', './public/upload/images/201111/27/4ed1e7ee17598.png', '', '', '', 100, 1),
(5, '其他', './public/upload/images/201111/27/4ed1e71912037.png', './public/upload/images/201111/27/4ed1e7f5e1d65.png', '', '', '', 100, 1);

DROP TABLE IF EXISTS `%DB_PREFIX%album_rec`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album_rec` (
  `album_id` int(11) NOT NULL,
  `ashare_id` int(11) NOT NULL,
  `share_id` int(11) NOT NULL,
  `rec_id` int(11) NOT NULL,
  `type` enum('photo','goods') NOT NULL,
  KEY `share_id` (`share_id`),
  KEY `album_id` (`album_id`),
  KEY `ashare_id` (`ashare_id`),
  KEY `album_id_2` (`album_id`,`share_id`,`rec_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%album_share`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album_share` (
  `album_id` int(11) NOT NULL,
  `share_id` int(11) NOT NULL,
  `cid` smallint(6) NOT NULL DEFAULT '0',
  `create_day` int(11) NOT NULL DEFAULT '0',
  KEY `album_id` (`album_id`),
  KEY `cid` (`cid`),
  KEY `create_day` (`create_day`),
  KEY `share_id` (`share_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%album_tags`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album_tags` (
  `tag_name` varchar(60) NOT NULL,
  `album_count` int(11) NOT NULL DEFAULT '0',
  `tag_img` varchar(255) NOT NULL DEFAULT '',
  `is_new` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `tag_name` (`tag_name`),
  KEY `is_new` (`is_new`),
  KEY `album_count` (`album_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%album_tags_related`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%album_tags_related` (
  `tag_name` varchar(60) NOT NULL,
  `album_id` int(11) NOT NULL,
  UNIQUE KEY `tag_aid` (`tag_name`,`album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%atme`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%atme` (
  `uid` int(11) DEFAULT '0',
  `user_name` varchar(100) DEFAULT NULL,
  `share_id` int(11) DEFAULT '0',
  KEY `uid` USING BTREE(`uid`),
  KEY `share_id` USING BTREE(`share_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%event`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `share_id` int(11) NOT NULL,
  `is_event` tinyint(1) NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `last_share` int(11) NOT NULL DEFAULT '0',
  `last_time` int(11) NOT NULL DEFAULT '0',
  `thread_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` USING BTREE(`uid`),
  KEY `title` USING BTREE(`title`),
  KEY `share_id` (`share_id`),
  KEY `thread_count` (`thread_count`),
  KEY `last_share` (`last_share`),
  KEY `is_hot` (`is_hot`),
  KEY `is_event` (`is_event`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `%DB_PREFIX%event_share`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%event_share` (
  `event_id` int(11) NOT NULL,
  `share_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  KEY `event_id` (`event_id`),
  KEY `share_id` (`share_id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%exchange_goods`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%exchange_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `goods_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:虚拟卡 1:实体商品',
  `img` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `integral` int(11) NOT NULL DEFAULT '0',
  `stock` int(11) NOT NULL DEFAULT '0',
  `buy_count` int(11) NOT NULL DEFAULT '0',
  `user_num` smallint(5) NOT NULL DEFAULT '1',
  `is_best` tinyint(1) NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0',
  `begin_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `begin_end_time` (`begin_time`,`end_time`),
  KEY `status` (`status`),
  KEY `sort` (`sort`),
  KEY `is_best` (`is_best`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `%DB_PREFIX%login_module`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%login_module` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(120) NOT NULL DEFAULT '',
  `short_name` varchar(60) NOT NULL DEFAULT '',
  `app_key` varchar(255) NOT NULL DEFAULT '',
  `app_secret` varchar(255) NOT NULL DEFAULT '',
  `is_syn` tinyint(1) NOT NULL DEFAULT '0',
  `sort` smallint(5) NOT NULL DEFAULT '10',
  `desc` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `%DB_PREFIX%login_module` (`id`, `code`, `name`, `short_name`, `app_key`, `app_secret`, `is_syn`, `sort`, `desc`, `status`) VALUES
(1, 'tqq', '腾讯微博', '腾讯', '', '', 1, 10, '申请地址：http://open.t.qq.com/websites/applykey', 1),
(2, 'sina', '新浪微博', '新浪', '', '', 1, 10, '申请地址：http://open.weibo.com/webmaster/add', 1),
(3, 'qq', 'QQ登录', 'QQ', '', '', 0, 10, '申请地址：http://connect.opensns.qq.com/', 1),
(4, 'taobao', '淘宝登录', '淘宝', '', '', 0, 10, '申请地址：http://open.taobao.com', 1);

DROP TABLE IF EXISTS `%DB_PREFIX%order`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(255) NOT NULL,
  `data_name` varchar(255) NOT NULL DEFAULT '',
  `goods_status` tinyint(1) NOT NULL COMMENT '0:未发货;1:部分发货;2:全部发货;3:部分退货;4:全部退货'',',
  `data_num` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL COMMENT '订单状态\r\n0: 未确认\r\n1: 完成\r\n2: 作废',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `region_lv1` int(11) NOT NULL,
  `region_lv2` int(11) NOT NULL,
  `region_lv3` int(11) NOT NULL,
  `region_lv4` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_phone` varchar(255) NOT NULL,
  `fax_phone` varchar(255) NOT NULL,
  `fix_phone` varchar(255) NOT NULL,
  `alim` varchar(255) NOT NULL,
  `msn` varchar(255) NOT NULL,
  `qq` varchar(255) NOT NULL,
  `consignee` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL,
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `memo` varchar(255) NOT NULL,
  `adm_memo` varchar(255) NOT NULL,
  `order_score` int(11) NOT NULL,
  `rec_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `uid` (`uid`),
  KEY `rec_id` (`rec_id`),
  KEY `goods_status` (`goods_status`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `%DB_PREFIX%second`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%second` (
  `sid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT '',
  `sort` smallint(5) DEFAULT '100',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

INSERT INTO `%DB_PREFIX%second` (`sid`, `name`, `sort`, `status`) VALUES
(1, '上衣', 10, 1),
(2, '服装', 10, 1),
(3, '鞋帽', 10, 1),
(4, '配饰', 10, 1),
(5, '美容', 10, 1),
(6, '数码', 10, 1),
(7, '书籍音像', 10, 1),
(8, '其它', 10, 1);

DROP TABLE IF EXISTS `%DB_PREFIX%second_goods`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%second_goods` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `sid` smallint(6) NOT NULL,
  `share_id` int(11) DEFAULT NULL,
  `alipay_gid` varchar(60) NOT NULL DEFAULT '',
  `name` varchar(100) DEFAULT '',
  `content` text,
  `city_id` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `transport_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valid_time` int(11) NOT NULL DEFAULT '0',
  `page` varchar(255) NOT NULL DEFAULT '',
  `num` smallint(6) NOT NULL DEFAULT '0',
  `sign` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`gid`),
  UNIQUE KEY `share_id` (`share_id`),
  KEY `sid` (`sid`),
  KEY `city_id` (`city_id`),
  KEY `uid` (`uid`),
  KEY `sid_city` (`sid`,`city_id`),
  KEY `alipay_gid` (`alipay_gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `%DB_PREFIX%share_rec`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%share_rec` (
  `share_id` int(11) NOT NULL,
  `rec_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`share_id`),
  KEY `rec_count` (`rec_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%shop_category`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%shop_category` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(6) DEFAULT NULL,
  `name` varchar(100) DEFAULT '',
  `sort` smallint(5) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

INSERT INTO `%DB_PREFIX%shop_category` (`id`, `parent_id`, `name`, `sort`) VALUES
(1, 0, '风格', 100),
(2, 0, '看点', 100),
(3, 1, '日韩杂志款', 100),
(4, 1, '小清新混搭', 100),
(5, 1, '欧美高街', 100),
(6, 1, '休闲混搭', 100),
(7, 2, '外贸原单', 100),
(8, 2, '潮流女鞋', 100),
(9, 2, '流行饰品', 100),
(10, 2, '包包手袋', 100),
(11, 2, '手套配件', 100);

DROP TABLE IF EXISTS `%DB_PREFIX%user_bind`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%user_bind` (
  `uid` int(11) NOT NULL,
  `type` varchar(60) NOT NULL,
  `keyid` varchar(100) NOT NULL,
  `info` text,
  `sync` text,
  `refresh_time` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `uid_type` (`uid`,`type`),
  KEY `uid` (`uid`),
  KEY `refresh_time` (`refresh_time`),
  KEY `type_keyid` (`type`,`keyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%user_consignee`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%user_consignee` (
  `uid` int(11) NOT NULL,
  `region_lv1` int(11) NOT NULL DEFAULT '0',
  `region_lv2` int(11) NOT NULL DEFAULT '0',
  `region_lv3` int(11) NOT NULL DEFAULT '0',
  `region_lv4` int(11) NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `mobile_phone` varchar(255) NOT NULL DEFAULT '',
  `fix_phone` varchar(255) NOT NULL DEFAULT '',
  `consignee` varchar(255) NOT NULL DEFAULT '',
  `zip` varchar(255) NOT NULL DEFAULT '',
  `qq` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `fax_phone` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%DB_PREFIX%user_score_log`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%user_score_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL,
  `create_day` int(11) NOT NULL DEFAULT '0',
  `content` text,
  `rec_id` int(11) NOT NULL,
  `rec_module` varchar(255) NOT NULL,
  `rec_action` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `create_day` (`create_day`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `%DB_PREFIX%sharegoods_module`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%sharegoods_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT '',
  `content` text,
  `api_data` text,
  `sort` smallint(5) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `%DB_PREFIX%sharegoods_module` (`id`, `class`, `domain`, `status`, `name`, `url`, `icon`, `logo`, `content`, `api_data`, `sort`) VALUES
(1, 'taobao', 'http://item.taobao.com,http://item.tmall.com', 1, '淘宝', 'http://www.taobao.com', './public/upload/business/taobao.gif', '', '淘宝应用用于获取淘宝商品、店铺信息，可到 http://open.taobao.com/ 点击 申请成为合作伙伴 ', 'a:3:{s:7:"app_key";s:0:"";s:10:"app_secret";s:0:"";s:6:"tk_pid";s:0:"";}', 100),
(2, 'paipai', 'http://auction1.paipai.com', 1, '拍拍', 'http://www.paipai.com', './public/upload/business/paipai.gif', '', '拍拍应用用于获取拍拍商品、店铺信息，可到 http://pop.paipai.com/ 点击 申请成为合作伙伴 ', 'a:4:{s:3:"uin";s:0:"";s:4:"spid";s:0:"";s:5:"token";s:0:"";s:6:"seckey";s:0:"";}', 100);

DROP TABLE IF EXISTS `%DB_PREFIX%referrals`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `is_pay` tinyint(1) NOT NULL,
  `score` float(10,0) NOT NULL,
  `create_time` int(10) DEFAULT '0',
  `create_day` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `rid` (`rid`),
  KEY `is_pay` (`is_pay`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `%DB_PREFIX%share_image_sizes`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%share_image_sizes` (
  `id` varchar(60) NOT NULL DEFAULT '',
  `width` smallint(5) DEFAULT '0',
  `height` smallint(5) DEFAULT '0',
  `is_cut` tinyint(1) DEFAULT '0',
  `is_water` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `%DB_PREFIX%share_image_sizes` (`id`, `width`, `height`, `is_cut`, `is_water`, `status`) VALUES
('image_1', 100, 100, 1, 0, 1),
('image_2', 200, 999, 0, 0, 1),
('image_3', 468, 468, 0, 1, 1),
('image_4', 160, 160, 0, 0, 1);

DROP TABLE IF EXISTS `%DB_PREFIX%role_nav`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%role_nav` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11;

INSERT INTO `%DB_PREFIX%role_nav` (`id`, `name`, `status`, `sort`) VALUES
(2, '分享', 1, 2),
(3, '主题', 1, 3),
(4, '会员', 1, 4),
(5, '权限', 1, 5),
(6, '数据库', 1, 6),
(7, '系统', 1, 7),
(8, '前台', 1, 6),
(9, '二手', 1, 3),
(10, '杂志社', 1, 3);

DROP TABLE IF EXISTS `%DB_PREFIX%role_node`;
CREATE TABLE IF NOT EXISTS `%DB_PREFIX%role_node` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `action` varchar(60) NOT NULL DEFAULT '',
  `action_name` varchar(60) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `module` varchar(60) NOT NULL DEFAULT '',
  `module_name` varchar(60) NOT NULL DEFAULT '',
  `nav_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '从属于哪个模块组, 为0时表示不属于菜单节点',
  `sort` smallint(5) NOT NULL DEFAULT '0',
  `auth_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '授权模式：1:模块授权(module) 2:操作授权(action) 0:节点授权(node)',
  `is_show` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `%DB_PREFIX%role_node` (`action`, `action_name`, `status`, `module`, `module_name`, `nav_id`, `sort`, `auth_type`, `is_show`) VALUES
('', '', 1, 'SysConf', '系统管理', 7, 10, 1, 0),
('index', '系统设置', 1, 'SysConf', '系统管理', 7, 10, 0, 1),
('update', '更新设置', 1, 'SysConf', '系统管理', 7, 10, 0, 0),
('', '', 1, 'SharegoodsModule', '商品接口管理', 7, 9, 1, 0),
('index', '接口列表', 1, 'SharegoodsModule', '商品接口管理', 7, 10, 0, 1),
('update', '更新接口', 1, 'SharegoodsModule', '商品接口管理', 7, 10, 0, 0),
('', '', 1, 'LoginModule', '同步登陆管理', 7, 10, 1, 0),
('index', '模块列表', 1, 'LoginModule', '同步登陆管理', 7, 10, 0, 1),
('update', '更新模块', 1, 'LoginModule', '同步登陆管理', 7, 10, 0, 0),
('', '', 1, 'Cache', '缓存管理', 7, 10, 1, 0),
('system', '清除系统缓存', 1, 'Cache', '缓存管理', 7, 10, 0, 1),
('custom', '清除程序缓存', 1, 'Cache', '缓存管理', 7, 10, 0, 1),
('', '', 1, 'TempFile', '临时文件管理', 7, 10, 1, 0),
('index', '临时文件列表', 1, 'TempFile', '临时文件管理', 7, 10, 0, 1),
('clear', '清除临时文件', 1, 'TempFile', '临时文件管理', 7, 10, 0, 0),
('', '', 1, 'AdminLog', '操作日志管理', 7, 10, 1, 0),
('index', '操作日志列表', 1, 'AdminLog', '操作日志管理', 7, 10, 0, 1),
('remove', '删除操作日志', 1, 'AdminLog', '操作日志管理', 7, 10, 0, 0),
('', '', 1, 'Region', '城市管理', 7, 10, 1, 0),
('index', '城市列表', 1, 'Region', '城市管理', 7, 10, 0, 1),
('add', '添加城市', 1, 'Region', '城市管理', 7, 10, 0, 1),
('update', '更新城市', 1, 'Region', '城市管理', 7, 10, 0, 0),
('remove', '删除城市', 1, 'Region', '城市管理', 7, 10, 0, 0),
('', '', 1, 'DataBase', '数据库操作', 6, 10, 1, 0),
('index', '数据库备份', 1, 'DataBase', '数据库操作', 6, 10, 0, 1),
('dump', '备份操作', 1, 'DataBase', '数据库操作', 6, 10, 0, 0),
('delete', '删除操作', 1, 'DataBase', '数据库操作', 6, 10, 0, 0),
('restore', '恢复操作', 1, 'DataBase', '数据库操作', 6, 10, 0, 0),
('', '', 1, 'Sql', 'SQL操作', 6, 10, 1, 0),
('index', 'SQL操作', 1, 'Sql', 'SQL操作', 6, 10, 0, 1),
('execute', '执行SQL', 1, 'Sql', 'SQL操作', 6, 10, 0, 0),
('', '', 1, 'Admin', '管理员管理', 5, 10, 1, 0),
('index', '管理员列表', 1, 'Admin', '管理员管理', 5, 10, 0, 1),
('add', '添加管理员', 1, 'Admin', '管理员管理', 5, 10, 0, 1),
('update', '更新管理员', 1, 'Admin', '管理员管理', 5, 10, 0, 0),
('remove', '删除管理员', 1, 'Admin', '管理员管理', 5, 10, 0, 0),
('', '', 1, 'Role', '权限组管理', 5, 10, 1, 0),
('index', '角色列表', 1, 'Role', '权限组管理', 5, 10, 0, 1),
('add', '添加角色', 1, 'Role', '权限组管理', 5, 10, 0, 1),
('update', '更新角色', 1, 'Role', '权限组管理', 5, 10, 0, 0),
('remove', '删除角色', 1, 'Role', '权限组管理', 5, 10, 0, 0),
('', '', 1, 'RoleNode', '权限节点管理', 5, 10, 1, 0),
('index', '节点列表', 1, 'RoleNode', '权限节点管理', 5, 10, 0, 1),
('add', '添加节点', 1, 'RoleNode', '权限节点管理', 5, 10, 0, 1),
('update', '更新节点', 1, 'RoleNode', '权限节点管理', 5, 10, 0, 0),
('remove', '删除节点', 1, 'RoleNode', '权限节点管理', 5, 10, 0, 0),
('', '', 1, 'RoleNav', '后台导航菜单管理', 5, 10, 1, 0),
('index', '菜单列表', 1, 'RoleNav', '后台导航菜单管理', 5, 10, 0, 1),
('add', '添加菜单', 1, 'RoleNav', '后台导航菜单管理', 5, 10, 0, 1),
('update', '更新菜单', 1, 'RoleNav', '后台导航菜单管理', 5, 10, 0, 0),
('remove', '删除菜单', 1, 'RoleNav', '后台导航菜单管理', 5, 10, 0, 0),
('', '', 1, 'UserSetting', '会员配置管理', 4, 10, 1, 0),
('index', '设置配置', 1, 'UserSetting', '会员配置管理', 4, 10, 0, 1),
('update', '更新配置', 1, 'UserSetting', '会员配置管理', 4, 10, 0, 0),
('', '', 1, 'User', '会员管理', 4, 10, 1, 0),
('index', '会员列表', 1, 'User', '会员管理', 4, 10, 0, 1),
('add', '添加会员', 1, 'User', '会员管理', 4, 10, 0, 1),
('update', '更新会员', 1, 'User', '会员管理', 4, 10, 0, 0),
('remove', '删除会员', 1, 'User', '会员管理', 4, 10, 0, 0),
('', '', 1, 'UserDaren', '达人管理', 4, 10, 1, 0),
('index', '达人列表', 1, 'UserDaren', '达人管理', 4, 10, 0, 1),
('add', '添加达人', 1, 'UserDaren', '达人管理', 4, 10, 0, 1),
('update', '更新达人', 1, 'UserDaren', '达人管理', 4, 10, 0, 0),
('remove', '删除达人', 1, 'UserDaren', '达人管理', 4, 10, 0, 0),
('', '', 1, 'UserGroup', '会员组管理', 4, 10, 1, 0),
('index', '会员组列表', 1, 'UserGroup', '会员组管理', 4, 10, 0, 1),
('add', '添加会员组', 1, 'UserGroup', '会员组管理', 4, 10, 0, 1),
('update', '更新会员组', 1, 'UserGroup', '会员组管理', 4, 10, 0, 0),
('remove', '删除会员组', 1, 'UserGroup', '会员组管理', 4, 10, 0, 0),
('', '', 1, 'Forum', '论坛分类管理', 3, 10, 1, 0),
('index', '分类列表', 1, 'Forum', '论坛分类管理', 3, 10, 0, 1),
('add', '添加分类', 1, 'Forum', '论坛分类管理', 3, 10, 0, 1),
('update', '更新分类', 1, 'Forum', '论坛分类管理', 3, 10, 0, 0),
('remove', '删除分类', 1, 'Forum', '论坛分类管理', 3, 10, 0, 0),
('', '', 1, 'ForumThread', '论坛主题管理', 3, 10, 1, 0),
('index', '主题列表', 1, 'ForumThread', '论坛主题管理', 3, 10, 0, 1),
('update', '更新主题', 1, 'ForumThread', '论坛主题管理', 3, 10, 0, 0),
('remove', '删除主题', 1, 'ForumThread', '论坛主题管理', 3, 10, 0, 0),
('', '', 1, 'Ask', '问答分类管理', 3, 10, 1, 0),
('index', '分类列表', 1, 'Ask', '问答分类管理', 3, 10, 0, 1),
('add', '添加分类', 1, 'Ask', '问答分类管理', 3, 10, 0, 1),
('update', '更新分类', 1, 'Ask', '问答分类管理', 3, 10, 0, 0),
('remove', '删除分类', 1, 'Ask', '问答分类管理', 3, 10, 0, 0),
('', '', 1, 'AskThread', '问答主题管理', 3, 10, 1, 0),
('index', '主题列表', 1, 'AskThread', '问答主题管理', 3, 10, 0, 1),
('update', '更新主题', 1, 'AskThread', '问答主题管理', 3, 10, 0, 0),
('remove', '删除主题', 1, 'AskThread', '问答主题管理', 3, 10, 0, 0),
('', '', 1, 'Event', '话题管理', 3, 10, 1, 0),
('index', '话题列表', 1, 'Event', '话题管理', 3, 10, 0, 1),
('update', '更新话题', 1, 'Event', '话题管理', 3, 10, 0, 0),
('remove', '删除话题', 1, 'Event', '话题管理', 3, 10, 0, 0),
('', '', 1, 'EventShare', '话题回复管理', 3, 10, 1, 0),
('index', '话题回复列表', 1, 'EventShare', '话题回复管理', 3, 10, 0, 0),
('remove', '删除话题回复', 1, 'EventShare', '话题回复管理', 3, 10, 0, 0),
('', '', 1, 'Share', '分享管理', 2, 10, 1, 0),
('index', '分享列表', 1, 'Share', '分享管理', 2, 10, 0, 1),
('dapei', '搭配列表', 1, 'Share', '分享管理', 2, 10, 0, 1),
('look', '晒货列表', 1, 'Share', '分享管理', 2, 10, 0, 1),
('update', '更新分享', 1, 'Share', '分享管理', 2, 10, 0, 0),
('remove', '删除分享', 1, 'Share', '分享管理', 2, 10, 0, 0),
('', '', 1, 'GoodsCategory', '分享分类管理', 2, 10, 1, 0),
('index', '分类列表', 1, 'GoodsCategory', '分享分类管理', 2, 10, 0, 1),
('add', '添加分类', 1, 'GoodsCategory', '分享分类管理', 2, 10, 0, 1),
('update', '更新分类', 1, 'GoodsCategory', '分享分类管理', 2, 10, 0, 0),
('remove', '删除分类', 1, 'GoodsCategory', '分享分类管理', 2, 10, 0, 0),
('', '', 1, 'GoodsCategoryTags', '分享分类关联标签管理', 2, 10, 1, 0),
('index', '标签列表', 1, 'GoodsCategoryTags', '分享分类关联标签管理', 2, 10, 0, 0),
('setting', '设置标签', 1, 'GoodsCategoryTags', '分享分类关联标签管理', 2, 10, 0, 0),
('update', '更新分类', 1, 'GoodsCategoryTags', '分享分类关联标签管理', 2, 10, 0, 0),
('remove', '删除分类', 1, 'GoodsCategoryTags', '分享分类关联标签管理', 2, 10, 0, 0),
('', '', 1, 'GoodsTags', '分享分类标签管理', 2, 10, 1, 0),
('index', '标签列表', 1, 'GoodsTags', '分享分类标签管理', 2, 10, 0, 1),
('add', '添加标签', 1, 'GoodsTags', '分享分类标签管理', 2, 10, 0, 1),
('update', '更新标签', 1, 'GoodsTags', '分享分类标签管理', 2, 10, 0, 0),
('remove', '删除标签', 1, 'GoodsTags', '分享分类标签管理', 2, 10, 0, 0),
('', '', 1, 'StyleCategory', '搭配分类管理', 2, 10, 1, 0),
('index', '分类列表', 1, 'StyleCategory', '搭配分类管理', 2, 10, 0, 1),
('add', '添加分类', 1, 'StyleCategory', '搭配分类管理', 2, 10, 0, 1),
('', '', 1, 'ShopCategory', '店铺分类管理', 2, 10, 1, 0),
('index', '分类列表', 1, 'ShopCategory', '店铺分类管理', 2, 10, 0, 1),
('add', '添加分类', 1, 'ShopCategory', '店铺分类管理', 2, 10, 0, 1),
('update', '更新分类', 1, 'ShopCategory', '店铺分类管理', 2, 10, 0, 0),
('remove', '删除分类', 1, 'ShopCategory', '店铺分类管理', 2, 10, 0, 0),
('', '', 1, 'Shop', '店铺管理', 2, 10, 1, 0),
('index', '店铺列表', 1, 'Shop', '店铺管理', 2, 10, 0, 1),
('update', '更新店铺', 1, 'Shop', '店铺管理', 2, 10, 0, 0),
('remove', '删除店铺', 1, 'Shop', '店铺管理', 2, 10, 0, 0),
('', '', 1, 'NavCategory', '前台菜单分类管理', 8, 10, 1, 0),
('index', '分类列表', 1, 'NavCategory', '前台菜单分类管理', 8, 10, 0, 1),
('add', '添加分类', 1, 'NavCategory', '前台菜单分类管理', 8, 10, 0, 1),
('update', '更新分类', 1, 'NavCategory', '前台菜单分类管理', 8, 10, 0, 0),
('remove', '删除分类', 1, 'NavCategory', '前台菜单分类管理', 8, 10, 0, 0),
('', '', 1, 'Nav', '前台菜单管理', 8, 10, 1, 0),
('index', '菜单列表', 1, 'Nav', '前台菜单管理', 8, 10, 0, 1),
('add', '添加菜单', 1, 'Nav', '前台菜单管理', 8, 10, 0, 1),
('update', '更新菜单', 1, 'Nav', '前台菜单管理', 8, 10, 0, 0),
('remove', '删除菜单', 1, 'Nav', '前台菜单管理', 8, 10, 0, 0),
('', '', 1, 'FriendLink', '友情链接管理', 8, 10, 1, 0),
('index', '链接列表', 1, 'FriendLink', '友情链接管理', 8, 10, 0, 1),
('add', '添加链接', 1, 'FriendLink', '友情链接管理', 8, 10, 0, 1),
('update', '更新链接', 1, 'FriendLink', '友情链接管理', 8, 10, 0, 0),
('remove', '删除链接', 1, 'FriendLink', '友情链接管理', 8, 10, 0, 0),
('', '', 1, 'UserMsg', '会员信件管理', 4, 10, 1, 0),
('index', '会员信件列表', 1, 'UserMsg', '会员信件管理', 4, 10, 0, 1),
('groupSend', '发送系统信件', 1, 'UserMsg', '会员信件管理', 4, 10, 0, 1),
('groupList', '系统信件列表', 1, 'UserMsg', '会员信件管理', 4, 10, 0, 1),
('', '', 1, 'Integrate', '会员整合', 4, 10, 1, 0),
('index', '会员整合', 1, 'Integrate', '会员整合', 4, 10, 0, 1),
('', '', 1, 'Medal', '会员勋章', 4, 10, 1, 0),
('index', '勋章列表', 1, 'Medal', '会员勋章', 4, 10, 0, 1),
('add', '添加勋章', 1, 'Medal', '会员勋章', 4, 10, 0, 1),
('user', '勋章会员', 1, 'Medal', '会员勋章', 4, 10, 0, 1),
('check', '勋章审核', 1, 'Medal', '会员勋章', 4, 10, 0, 1),
('', '', 1, 'WordType', '敏感词分类管理', 7, 10, 1, 0),
('index', '分类列表', 1, 'WordType', '敏感词分类管理', 7, 10, 0, 1),
('add', '添加分类', 1, 'WordType', '敏感词分类管理', 7, 10, 0, 1),
('update', '更新分类', 1, 'WordType', '敏感词分类管理', 7, 10, 0, 0),
('remove', '删除分类', 1, 'WordType', '敏感词分类管理', 7, 10, 0, 0),
('', '', 1, 'Word', '敏感词管理', 7, 10, 1, 0),
('index', '敏感词列表', 1, 'Word', '敏感词管理', 7, 10, 0, 1),
('add', '添加敏感词', 1, 'Word', '敏感词管理', 7, 10, 0, 1),
('update', '更新敏感词', 1, 'Word', '敏感词管理', 7, 10, 0, 0),
('remove', '删除敏感词', 1, 'Word', '敏感词管理', 7, 10, 0, 0),
('', '', 1, 'AdvPosition', '广告位管理', 8, 10, 1, 0),
('index', '广告位列表', 1, 'AdvPosition', '广告位管理', 8, 10, 0, 1),
('add', '添加广告位', 1, 'AdvPosition', '广告位管理', 8, 10, 0, 1),
('update', '更新广告位', 1, 'AdvPosition', '广告位管理', 8, 10, 0, 0),
('remove', '删除广告位', 1, 'AdvPosition', '广告位管理', 8, 10, 0, 0),
('', '', 1, 'Adv', '广告管理', 8, 10, 1, 0),
('index', '广告列表', 1, 'Adv', '广告管理', 8, 10, 0, 1),
('add', '添加广告', 1, 'Adv', '广告管理', 8, 10, 0, 1),
('update', '添加广告', 1, 'Adv', '广告管理', 8, 10, 0, 0),
('remove', '添加广告', 1, 'Adv', '广告管理', 8, 10, 0, 0),
('', '', 1, 'AdvLayout', '广告布局', 8, 10, 1, 0),
('index', '布局列表', 1, 'AdvLayout', '广告布局', 8, 10, 0, 1),
('add', '添加布局', 1, 'AdvLayout', '广告布局', 8, 10, 0, 1),
('', '', 1, 'SecondSetting', '二手设置管理', 9, 10, 1, 0),
('index', '二手设置', 1, 'SecondSetting', '二手设置管理', 9, 10, 0, 1),
('update', '更新设置', 1, 'SecondSetting', '二手设置管理', 9, 10, 0, 0),
('', '', 1, 'Second', '二手分类管理', 9, 10, 1, 0),
('index', '分类列表', 1, 'Second', '二手分类管理', 9, 10, 0, 1),
('add', '添加分类', 1, 'Second', '二手分类管理', 9, 10, 0, 1),
('update', '更新分类', 1, 'Second', '二手分类管理', 9, 10, 0, 0),
('remove', '删除分类', 1, 'Second', '二手分类管理', 9, 10, 0, 0),
('', '', 1, 'SecondGoods', '二手商品管理', 9, 10, 1, 0),
('index', '商品列表', 1, 'SecondGoods', '二手商品管理', 9, 10, 0, 1),
('update', '更新商品', 1, 'SecondGoods', '二手商品管理', 9, 10, 0, 0),
('remove', '删除商品', 1, 'SecondGoods', '二手商品管理', 9, 10, 0, 0),
('', '', 1, 'UserScoreLog', '会员积分日志', 4, 10, 1, 0),
('index', '日志列表', 1, 'UserScoreLog', '会员积分日志', 4, 10, 0, 1),
('remove', '删除日志', 1, 'UserScoreLog', '会员积分日志', 4, 10, 0, 0),
('', '', 1, 'Referrals', '会员邀请日志', 4, 10, 1, 0),
('index', '日志列表', 1, 'Referrals', '会员邀请日志', 4, 10, 0, 1),
('update', '更新日志', 1, 'Referrals', '会员邀请日志', 4, 10, 0, 0),
('remove', '删除日志', 1, 'Referrals', '会员邀请日志', 4, 10, 0, 0),
('', '', 1, 'ExchangeGoods', '积分商品管理', 4, 10, 1, 0),
('index', '商品列表', 1, 'ExchangeGoods', '积分商品管理', 4, 10, 0, 1),
('add', '添加商品', 1, 'ExchangeGoods', '积分商品管理', 4, 10, 0, 1),
('update', '更新商品', 1, 'ExchangeGoods', '积分商品管理', 4, 10, 0, 0),
('remove', '删除商品', 1, 'ExchangeGoods', '积分商品管理', 4, 10, 0, 0),
('', '', 1, 'Order', '积分订单管理', 4, 10, 1, 0),
('index', '订单列表', 1, 'Order', '积分订单管理', 4, 10, 0, 1),
('update', '更新订单', 1, 'Order', '积分订单管理', 4, 10, 0, 0),
('remove', '删除订单', 1, 'Order', '积分订单管理', 4, 10, 0, 0),
('', '', 1, 'AlbumSetting', '杂志社配置管理', 10, 10, 1, 0),
('index', '设置配置', 1, 'AlbumSetting', '杂志社配置管理', 10, 10, 0, 1),
('update', '更新配置', 1, 'AlbumSetting', '杂志社配置管理', 10, 10, 0, 0),
('', '', 1, 'AlbumCategory', '杂志社分类管理', 10, 10, 1, 0),
('index', '分类列表', 1, 'AlbumCategory', '杂志社分类管理', 10, 10, 0, 1),
('add', '添加分类', 1, 'AlbumCategory', '杂志社分类管理', 10, 10, 0, 1),
('update', '更新分类', 1, 'AlbumCategory', '杂志社分类管理', 10, 10, 0, 0),
('remove', '删除分类', 1, 'AlbumCategory', '杂志社分类管理', 10, 10, 0, 0),
('', '', 1, 'Album', '杂志社管理', 10, 10, 1, 0),
('index', '杂志社列表', 1, 'Album', '杂志社管理', 10, 10, 0, 1),
('update', '更新杂志社', 1, 'Album', '杂志社管理', 10, 10, 0, 0),
('remove', '删除杂志社', 1, 'Album', '杂志社管理', 10, 10, 0, 0);

DELETE FROM `%DB_PREFIX%sys_conf` WHERE `name` = 'TAO_KE_KEY';
DELETE FROM `%DB_PREFIX%sys_conf` WHERE `name` = 'TAO_APP_KEY';
DELETE FROM `%DB_PREFIX%sys_conf` WHERE `name` = 'TAO_APP_SECRET';
UPDATE `%DB_PREFIX%sys_conf` SET `is_show` = '0' WHERE `name` = 'USER_AGREEMENT';
UPDATE `%DB_PREFIX%sys_conf` SET `val` = '2.0' WHERE `name` = 'SYS_VERSION';

INSERT INTO `%DB_PREFIX%sys_conf` (`name`, `val`, `status`, `sort`, `list_type`, `val_arr`, `group_id`, `is_show`, `is_js`) VALUES 
('SECOND_TAOBAO_FORUMID', '', 1, 0, 0, '', 7, 0, 0),
('SECOND_TAOBAO_SIGN', '', 1, 0, 0, '', 7, 0, 0),
('SECOND_ARTICLES', '    当您点击“我接受以上条款，确定”，即表示您已经充分了解并同意接受以下条款（而无论您是否已经注册为会员）：\r\n    1. 二手闲置交易信息发布公告板（即本页，下称本服务）是为网友提供的一项公益性质信息服务，我们不参与任何交易过程，也不对任何交易信息的真实性负责；\r\n    2. 本服务仅供网友发布二手闲置交易信息，不允许发布任何批量贩卖、代购等信息。不欢迎专业卖家和电商在此发布信息。\r\n    3. 发布交易信息请遵守国家及当地的法律、法规，不发布任何有可能影响网站平台安全的信息（包括但不限于武器、毒品、淫秽等信息）。我们保留无理由随时删除任何交易信息的权利。\r\n    4. 交易双方在交易前请充分沟通，交易过程请尽可能选择“支付宝担保交易”服务或同城见面交易。网站不对任何交易后果（包括担不限于财物损失）负责，也不承担任何连带责任，所有交易后果由交易双方独自承担。', 1, 0, 0, '', 7, 0, 0),
('SECOND_STATUS', '0', 1, 0, 0, '', 7, 0, 0),
('ALBUM_DEFAULT_TAGS', 'a:10:{i:0;s:6:"时尚";i:1;s:6:"购物";i:2;s:6:"品牌";i:3;s:6:"美容";i:4;s:6:"生活";i:5;s:6:"街拍";i:6;s:6:"秀场";i:7;s:6:"明星";i:8;s:9:"搭配秀";i:9;s:6:"晒货";}', 0, 0, 0, '', 8, 0, 0),
('ALBUM_TAG_COUNT', '4', 0, 0, 0, '', 8, 0, 0),
('TODAY_MAX_SCORE', '100', 1, 0, 0, '', 6, 0, 0),
('USER_REGISTER_SCORE', '10', 1, 0, 0, '', 6, 0, 0),
('USER_AVATAR_SCORE', '10', 1, 10, 0, '', 6, 0, 0),
('USER_LOGIN_SCORE', '1', 1, 0, 0, '', 6, 0, 0),
('USER_REFERRAL_SCORE', '10', 1, 0, 0, '', 6, 0, 0),
('CLEAR_REFERRAL_SCORE', '-20', 1, 0, 0, '', 6, 0, 0),
('SHARE_DEFAULT_SCORE', '1', 1, 0, 0, '', 6, 0, 0),
('SHARE_IMAGE_SCORE', '5', 1, 0, 0, '', 6, 0, 0),
('DELETE_SHARE_IMAGE_SCORE', '-20', 1, 0, 0, '', 6, 0, 0),
('DELETE_SHARE_DEFAULT_SCORE', '-10', 1, 0, 0, '', 6, 0, 0),
('USER_SORE_RULE', '<h1>\r\n	会员加减分规则</h1>\r\n<p>\r\n	1、会员注册＋10分；</p>\r\n<p>\r\n	2、每日登陆＋1分；</p>\r\n<p>\r\n	3、上传头像＋10分；</p>\r\n<p>\r\n	4、会员成功邀请＋10分；</p>\r\n<p>\r\n	5、删除取消会员邀请－20分；</p>\r\n<p>\r\n	6、发布普通(无图)分享＋1分；</p>\r\n<p>\r\n	7、发布有图分享＋5分；</p>\r\n<p>\r\n	8、管理员删除普通分享－10分；</p>\r\n<p>\r\n	9、管理员删除有图分享－20分；</p>\r\n', 1, 16, 5, '', 6, 0, 0),
('REGRESULT_TO_BIND', '0', 1, 8, 2, '0,1', 1, 1, 0),
('BIND_PUSH_WEIBO', '0', 1, 8, 2, '0,1', 1, 1, 0),
('BOOK_PHOTO_GOODS', '0', 1, 8, 2, '0,1,2', 1, 1, 0);

INSERT INTO `%DB_PREFIX%medal` (`name`, `image`, `give_type`, `expiration`, `conditions`, `keywords`, `confine`, `allow_group`, `desc`, `sort`, `status`, `is_fix`) VALUES
('呼朋唤友', '18.png', 0, 0, 'referrals', NULL, 3, '', '成功邀请3个新蘑菇注册，就可拿到该勋章', 100, 1, 0);

ALTER TABLE `%DB_PREFIX%share` ADD COLUMN `rec_uid` int(11) NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%share` DROP COLUMN `rec_share_id`;
ALTER TABLE `%DB_PREFIX%share` MODIFY COLUMN `type`  enum('default','ask','ershou','fav','comments','ask_post','bar','bar_post','album','album_item','album_best','album_rec','event','event_post') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default' COMMENT 'default:默认(default时，parent_id指向本表， 即转发),bar:主题,ershou:二手,ask:问答,ask_post:问答回复,comments:评论,fav:喜欢,bar:论坛,bar_post:论坛回复';
ALTER TABLE `%DB_PREFIX%share` ADD INDEX ( `collect_count` );

ALTER TABLE `%DB_PREFIX%share_goods` ADD COLUMN `base_id`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%share_goods` ADD COLUMN `base_share`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%share_goods` ADD INDEX ( `base_id` );

ALTER TABLE `%DB_PREFIX%share_photo` ADD COLUMN `base_id`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%share_photo` ADD COLUMN `base_share`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%share_photo` ADD INDEX ( `base_id` );
ALTER TABLE `%DB_PREFIX%share_photo` ADD INDEX ( `sort` );

ALTER TABLE `%DB_PREFIX%shop` ADD COLUMN `data`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `%DB_PREFIX%shop` ADD COLUMN `sort`  smallint(5) NOT NULL DEFAULT 100;
ALTER TABLE `%DB_PREFIX%shop` ADD INDEX ( `recommend_count` );
ALTER TABLE `%DB_PREFIX%shop` ADD INDEX ( `sort` );

ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `invite_id`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `is_buyer`  tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `buyer_level`  smallint(2) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `seller_level`  smallint(2) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%user` ADD INDEX ( `invite_id` );
ALTER TABLE `%DB_PREFIX%user` ADD INDEX ( `credits` );
ALTER TABLE `%DB_PREFIX%user` ADD INDEX ( `status` );


ALTER TABLE `%DB_PREFIX%album` ADD COLUMN `is_index` tinyint(1) DEFAULT 0;
ALTER TABLE `%DB_PREFIX%share` ADD COLUMN `is_index` tinyint(1) DEFAULT 0;
ALTER TABLE `%DB_PREFIX%share` ADD COLUMN `index_img` varchar(255) DEFAULT '';
ALTER TABLE `%DB_PREFIX%share` ADD COLUMN `sort` smallint(4) DEFAULT '100';

ALTER TABLE `%DB_PREFIX%album` ADD INDEX ( `is_index` );

ALTER TABLE `%DB_PREFIX%user_count` ADD COLUMN `seconds`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%user_count` ADD COLUMN `albums`  int(11) NOT NULL DEFAULT 0;
ALTER TABLE `%DB_PREFIX%user_count` ADD COLUMN `referrals`  int(11) NOT NULL DEFAULT 0;