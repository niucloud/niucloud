SET NAMES 'utf8';
DROP TABLE IF EXISTS nc_article;
CREATE TABLE nc_article (
  article_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章编号',
  site_id int(10) NOT NULL DEFAULT 0 COMMENT '站点id',
  title varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
  category_id int(10) NOT NULL DEFAULT 0 COMMENT '文章分类编号',
  short_title varchar(50) NOT NULL DEFAULT '' COMMENT '文章短标题',
  source varchar(50) NOT NULL DEFAULT '' COMMENT '文章来源',
  url varchar(255) NOT NULL DEFAULT '' COMMENT '文章来源链接',
  author varchar(50) NOT NULL DEFAULT '' COMMENT '文章作者',
  summary varchar(1000) NOT NULL DEFAULT '' COMMENT '文章摘要',
  content text NOT NULL COMMENT '文章正文',
  image varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题图片',
  keyword varchar(255) NOT NULL DEFAULT '' COMMENT '文章关键字',
  article_id_array varchar(255) NOT NULL DEFAULT '' COMMENT '相关文章',
  click int(10) NOT NULL DEFAULT 0 COMMENT '文章点击量',
  sort tinyint(1) NOT NULL DEFAULT 0 COMMENT '文章排序0-255',
  commend_flag tinyint(1) NOT NULL DEFAULT 0 COMMENT '文章推荐标志0-未推荐，1-已推荐',
  comment_flag tinyint(1) NOT NULL DEFAULT 1 COMMENT '文章是否允许评论1-允许，0-不允许',
  status tinyint(1) NOT NULL DEFAULT 1 COMMENT '0-草稿、1-待审核、2-已发布、-1-回收站',
  attachment_path text NOT NULL COMMENT '文章附件路径',
  tag varchar(255) NOT NULL DEFAULT '' COMMENT '文章标签',
  comment_count int(10) NOT NULL DEFAULT 0 COMMENT '文章评论数',
  share_count int(10) NOT NULL DEFAULT 0 COMMENT '文章分享数',
  publisher_name varchar(50) NOT NULL DEFAULT '' COMMENT '发布者用户名 ',
  uid int(10) NOT NULL DEFAULT 0 COMMENT '发布者编号',
  last_comment_time int(11) NOT NULL DEFAULT 0 COMMENT '最新评论时间',
  public_time int(11) NOT NULL DEFAULT 0 COMMENT '发布时间',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '文章发布时间',
  modify_time int(11) NOT NULL DEFAULT 0 COMMENT '文章修改时间',
  PRIMARY KEY (article_id),
  INDEX IDX_nc_article_keyword (keyword),
  INDEX IDX_nc_article_title (title)
)
ENGINE = INNODB
AUTO_INCREMENT = 274
AVG_ROW_LENGTH = 540
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '文章表';
DROP TABLE IF EXISTS nc_article_category;
CREATE TABLE nc_article_category (
  category_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  category_name varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  p_id int(11) NOT NULL DEFAULT 0 COMMENT '上级分类',
  sort int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  PRIMARY KEY (category_id),
  INDEX IDX_nc_article_category_category_id (category_id),
  INDEX IDX_nc_article_category_p_id (p_id),
  INDEX IDX_nc_article_category_site_id (site_id),
  INDEX IDX_nc_article_category_sort (sort)
)
ENGINE = INNODB
AUTO_INCREMENT = 98
AVG_ROW_LENGTH = 168
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '文章分类';
DROP TABLE IF EXISTS nc_article_comment;
CREATE TABLE nc_article_comment (
  id int(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  member_id int(11) NOT NULL DEFAULT 0 COMMENT '发表评论的用户id',
  to_member_id int(11) NOT NULL DEFAULT 0 COMMENT '被评论的用户id',
  to_comment_id int(11) NOT NULL DEFAULT 0 COMMENT '被回复的评论id',
  article_id int(11) NOT NULL DEFAULT 0 COMMENT '文章 id',
  like_count int(11) NOT NULL DEFAULT 0 COMMENT '点赞数',
  dislike_count int(11) NOT NULL DEFAULT 0 COMMENT '不喜欢数',
  floor int(11) NOT NULL DEFAULT 0 COMMENT '楼层数',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '评论时间',
  audit_time int(11) NOT NULL DEFAULT 0 COMMENT '审核时间',
  delete_time int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  status int(11) NOT NULL DEFAULT 1 COMMENT '状态,1:已审核,0:未审核, -1:已删除',
  comment_type int(11) NOT NULL DEFAULT 1 COMMENT '评论类型；1实名评论 2匿名评论',
  nick_name varchar(50) NOT NULL DEFAULT '' COMMENT '评论者昵称',
  content text NOT NULL COMMENT '评论内容',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  to_nick_name varchar(255) NOT NULL DEFAULT '' COMMENT '被评论的用户昵称',
  PRIMARY KEY (id),
  INDEX IDX_nc_article_comment (article_id, audit_time, comment_type),
  INDEX IDX_nc_article_comment_site_id (site_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 163
AVG_ROW_LENGTH = 317
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '文章评论表';
DROP TABLE IF EXISTS nc_article_reward_list;
CREATE TABLE nc_article_reward_list (
  reward_id int(11) NOT NULL AUTO_INCREMENT,
  out_trade_no varchar(255) NOT NULL DEFAULT '' COMMENT '打赏外部交易号',
  article_id int(11) NOT NULL DEFAULT 0 COMMENT '文章id',
  member_id int(11) NOT NULL DEFAULT 0 COMMENT '打赏会员',
  money decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '打赏金额',
  create_time int(11) NOT NULL DEFAULT 0,
  pay_time int(11) NOT NULL DEFAULT 0,
  status int(11) NOT NULL DEFAULT 0 COMMENT '状态  0待打赏 1 已打赏',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  PRIMARY KEY (reward_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 50
AVG_ROW_LENGTH = 341
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '文章打赏表';
