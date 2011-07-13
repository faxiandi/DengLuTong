CREATE TABLE `denglutong_user` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `dlt_user_id` varchar(64) NOT NULL COMMENT '第三方网站用户ID',
 `user_id` varchar(16) NOT NULL COMMENT '本站用户ID',
 `vendor` varchar(20) NOT NULL DEFAULT '' COMMENT '第三方网站名称',
 `keys` text NOT NULL,
 `name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '名称',
 `screen_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '昵称',
 `desc` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '简介',
 `url` varchar(100) NOT NULL DEFAULT '' COMMENT '主页',
 `img` varchar(100) NOT NULL DEFAULT '' COMMENT '头像',
 `gender` varchar(1) NOT NULL DEFAULT '' COMMENT '性别',
 `email` varchar(30) NOT NULL DEFAULT '' COMMENT '邮箱',
 `location` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '所在地',
 PRIMARY KEY (`id`),
 KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii

--DEMO用
CREATE TABLE `user` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_name` varchar(10) CHARACTER SET utf8 NOT NULL,
 `pass` varchar(6) NOT NULL,
 `email` varchar(20) NOT NULL DEFAULT '',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii