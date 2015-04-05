
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vote_mobi`
--

-- --------------------------------------------------------

--
-- 表的结构 `candidate`
--

CREATE TABLE IF NOT EXISTS `candidate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vid` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `summary` text NOT NULL,
  `description` text NOT NULL,
  `photo` varchar(30) NOT NULL,
  `poll` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `vid` (`vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='候选人';

-- --------------------------------------------------------

--
-- 表的结构 `parameter`
--

CREATE TABLE IF NOT EXISTS `parameter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL COMMENT '投票标题',
  `total` int(11) NOT NULL COMMENT '投票总数',
  `begintime` datetime NOT NULL COMMENT '投票开始时间',
  `endtime` datetime NOT NULL COMMENT '投票结束时间',
  `state` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL COMMENT '用户名',
  `realname` varchar(15) DEFAULT NULL COMMENT '真实姓名',
  `password` varchar(40) NOT NULL COMMENT '密码',
  `lastlogin` datetime DEFAULT NULL COMMENT '最后登录时间',
  `ip` varchar(20) DEFAULT NULL COMMENT '最后登录IP',
  `state` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 替换视图以便查看 `view_user`
--
CREATE TABLE IF NOT EXISTS `view_user` (
`vid` int(11)
,`id` int(11)
,`name` varchar(15)
,`realname` varchar(15)
,`password` varchar(40)
);
-- --------------------------------------------------------

--
-- 替换视图以便查看 `view_vote`
--
CREATE TABLE IF NOT EXISTS `view_vote` (
`vid` int(11)
,`uid` int(11)
,`realname` varchar(15)
,`cid` int(11)
,`name` varchar(15)
,`time` datetime
,`ip` varchar(20)
);
-- --------------------------------------------------------

--
-- 表的结构 `vote`
--

CREATE TABLE IF NOT EXISTS `vote` (
  `uid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  `vid` int(11) NOT NULL,
  `state` tinyint(2) NOT NULL DEFAULT '1',
  KEY `uid` (`uid`,`cid`),
  KEY `cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 触发器 `vote`
--
DROP TRIGGER IF EXISTS `addvote`;
DELIMITER //
CREATE TRIGGER `addvote` AFTER INSERT ON `vote`
 FOR EACH ROW update candidate set poll=poll+1 where id=new.cid
//
DELIMITER ;
DROP TRIGGER IF EXISTS `delvote`;
DELIMITER //
CREATE TRIGGER `delvote` AFTER UPDATE ON `vote`
 FOR EACH ROW begin 
set @poll=(select poll from candidate where id = new.cid);
if @poll<>0 and old.state=1 and new.state=0 then
    update candidate set poll=poll-1 where id=new.cid;
end if;
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `v_u`
--

CREATE TABLE IF NOT EXISTS `v_u` (
  `vid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  KEY `vid` (`vid`,`uid`),
  KEY `uid` (`uid`),
  KEY `vid_2` (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 视图结构 `view_user`
--
DROP TABLE IF EXISTS `view_user`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_user` AS select `v_u`.`vid` AS `vid`,`user`.`id` AS `id`,`user`.`name` AS `name`,`user`.`realname` AS `realname`,`user`.`password` AS `password` from (`v_u` left join `user` on((`v_u`.`uid` = `user`.`id`))) where (`user`.`state` = 1);

-- --------------------------------------------------------

--
-- 视图结构 `view_vote`
--
DROP TABLE IF EXISTS `view_vote`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_vote` AS select `vote`.`vid` AS `vid`,`vote`.`uid` AS `uid`,`user`.`realname` AS `realname`,`vote`.`cid` AS `cid`,`candidate`.`name` AS `name`,`vote`.`time` AS `time`,`vote`.`ip` AS `ip` from ((`vote` left join `user` on((`vote`.`uid` = `user`.`id`))) left join `candidate` on((`vote`.`cid` = `candidate`.`id`))) where ((`vote`.`state` = 1) and (`candidate`.`state` = 1) and (`user`.`state` = 1));

--
-- 限制导出的表
--

--
-- 限制表 `candidate`
--
ALTER TABLE `candidate`
  ADD CONSTRAINT `candidate_ibfk_1` FOREIGN KEY (`vid`) REFERENCES `parameter` (`id`) ON UPDATE CASCADE;

--
-- 限制表 `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `vote_ibfk_2` FOREIGN KEY (`cid`) REFERENCES `candidate` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- 限制表 `v_u`
--
ALTER TABLE `v_u`
  ADD CONSTRAINT `v_u_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `v_u_ibfk_1` FOREIGN KEY (`vid`) REFERENCES `parameter` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
