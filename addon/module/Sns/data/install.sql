SET NAMES 'utf8';

DROP TABLE IF EXISTS sns_info;
CREATE TABLE sns_info (
  info_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id int(11) NOT NULL DEFAULT 0 COMMENT '分类ID',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  tag text DEFAULT NULL COMMENT '标签',
  img_cover varchar(250) NOT NULL DEFAULT '' COMMENT '封面图片',
  imgs varchar(500) NOT NULL DEFAULT '' COMMENT '图片,分隔',
  title varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  content text DEFAULT NULL COMMENT '内容',
  sort int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  uid int(11) NOT NULL DEFAULT 0 COMMENT '站点发布人',
  member_id int(11) NOT NULL COMMENT '前台发布人',
  add_time bigint(12) NOT NULL DEFAULT 0 COMMENT '发布时间',
  last_time bigint(12) NOT NULL DEFAULT 0 COMMENT '最后修改时间',
  state int(1) NOT NULL DEFAULT 0 COMMENT '信息状态 参考本语言数组info_state 0提交 1通过 2拒绝 -1违规下架',
  price varchar(20) NOT NULL DEFAULT '0.00' COMMENT '价格',
  linkman varchar(30) NOT NULL DEFAULT '' COMMENT '联系人',
  contact varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  reflash bigint(12) NOT NULL DEFAULT 0 COMMENT '刷新时间',
  visit int(11) NOT NULL DEFAULT 1 COMMENT '访问统计',
  top_price decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '置顶价',
  top_start bigint(12) NOT NULL DEFAULT 0 COMMENT '置顶起始时间',
  top_end bigint(12) NOT NULL DEFAULT 0 COMMENT '置顶结束时间',
  sum_cost decimal(18, 2) NOT NULL DEFAULT 0.00 COMMENT '信息总花费',
  top_price_spare decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '备用 置顶价格',
  circle int(3) NOT NULL DEFAULT 0 COMMENT '圈子',
  comment int(11) NOT NULL DEFAULT 0 COMMENT '评论数',
  collection int(11) NOT NULL DEFAULT 0 COMMENT '收藏数',
  qrcode varchar(255) NOT NULL DEFAULT '' COMMENT '商品二维码',
  is_delete int(11) NOT NULL DEFAULT 0 COMMENT '删除标识',
  is_top int(11) NOT NULL DEFAULT 0 COMMENT '是否置顶',
  PRIMARY KEY (info_id),
  INDEX IDX_sns_info_content_classify_id (category_id),
  INDEX IDX_sns_info_content_sort (sort),
  INDEX IDX_sns_info_content_state (state),
  INDEX username (member_id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '分类信息内容';

DROP TABLE IF EXISTS sns_info_attribute_value;
CREATE TABLE sns_info_attribute_value (
  attribute_id int(11) NOT NULL DEFAULT 0 COMMENT '属性ID',
  info_id int(11) NOT NULL DEFAULT 0 COMMENT '内容ID',
  content text DEFAULT NULL COMMENT '值',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  INDEX IDX_sns_info_attribute_value_attribute_id (attribute_id),
  INDEX IDX_sns_info_attribute_value_info_id (info_id)
)
ENGINE = MYISAM
AVG_ROW_LENGTH = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '分类信息属性值';

DROP TABLE IF EXISTS sns_info_category;
CREATE TABLE sns_info_category (
  category_id int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL COMMENT '名称',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  parent int(3) NOT NULL DEFAULT 0 COMMENT '父级ID',
  sort int(3) NOT NULL DEFAULT 0 COMMENT '排序 ',
  visible int(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
  price varchar(100) NOT NULL DEFAULT '' COMMENT '价位 ',
  title_label varchar(10) NOT NULL DEFAULT '' COMMENT '标题输入框前名称',
  title_placeholder varchar(30) NOT NULL DEFAULT '' COMMENT '标题输入框提示内容',
  content_label varchar(10) NOT NULL DEFAULT '' COMMENT '内容输入框前名称',
  content_placeholder varchar(30) NOT NULL DEFAULT '' COMMENT '内容输入框提示内容',
  `column` int(1) NOT NULL DEFAULT 1 COMMENT '分类显示在哪一列',
  price_unit varchar(10) NOT NULL DEFAULT '' COMMENT '价格对应单位',
  icon_label varchar(10) NOT NULL DEFAULT '' COMMENT '封面图片别名',
  icon varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  report_tag varchar(1000) NOT NULL DEFAULT '' COMMENT '可举报的标签 ,分隔',
  PRIMARY KEY (category_id),
  INDEX IDX_sns_info_classify_parent (parent),
  INDEX IDX_sns_info_classify_price (price),
  INDEX IDX_sns_info_classify_sort (sort),
  INDEX IDX_sns_info_classify_visible (visible)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '分类信息 分类';

DROP TABLE IF EXISTS sns_info_category_attribute;
CREATE TABLE sns_info_category_attribute (
  attribute_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id int(11) NOT NULL DEFAULT 0 COMMENT '分类ID',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  name varchar(30) NOT NULL COMMENT '属性名称',
  sort int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  type varchar(50) NOT NULL DEFAULT '' COMMENT '属性类别',
  input_type varchar(100) NOT NULL COMMENT '属性输入框类别',
  placeholder varchar(100) DEFAULT NULL COMMENT '属性输入框提示文字',
  reg varchar(100) DEFAULT NULL COMMENT '属性输入框校验正则',
  `unique` int(1) NOT NULL DEFAULT 0 COMMENT '值是否可重复',
  search_able int(1) NOT NULL DEFAULT 1 COMMENT '值是否可搜索',
  required int(1) NOT NULL DEFAULT 0 COMMENT '值是否必填',
  input_args text DEFAULT NULL COMMENT '输入框CSS参数',
  screening_show int(1) NOT NULL DEFAULT 1 COMMENT '筛查选项中是否显示',
  default_value text DEFAULT NULL COMMENT '属性默认值',
  length int(11) NOT NULL DEFAULT 255 COMMENT '内容最大长度',
  postfix varchar(10) DEFAULT NULL COMMENT '属性后缀',
  PRIMARY KEY (attribute_id),
  INDEX IDX_sns_info_classify_attribute_classify_id (category_id),
  INDEX IDX_sns_info_classify_attribute_sort (sort)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '分类信息 分类属性';

DROP TABLE IF EXISTS sns_info_collection_browse;
CREATE TABLE sns_info_collection_browse (
  record_id int(11) NOT NULL AUTO_INCREMENT,
  info_id int(11) NOT NULL DEFAULT 0 COMMENT '信息id',
  member_id int(11) NOT NULL DEFAULT 0 COMMENT '会员id',
  is_collection int(11) NOT NULL DEFAULT 0 COMMENT '是否收藏 0否 1是',
  collection_time int(11) NOT NULL DEFAULT 0 COMMENT '收藏时间',
  browse_time int(11) NOT NULL DEFAULT 0 COMMENT '浏览时间（最新）',
  browse_sum int(11) NOT NULL DEFAULT 0 COMMENT '浏览数量',
  info_json varchar(500) NOT NULL DEFAULT '' COMMENT '信息的基本信息json(主图+名称+简介)',
  member_json varchar(500) NOT NULL DEFAULT '' COMMENT '浏览人（头像+账号+电话）',
  is_invalid int(11) NOT NULL DEFAULT 0 COMMENT '是否失效（定时批量更新）',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  PRIMARY KEY (record_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '信息的收藏/浏览 记录';

DROP TABLE IF EXISTS sns_info_comment;
CREATE TABLE sns_info_comment (
  comment_id int(11) NOT NULL AUTO_INCREMENT,
  member_id int(11) NOT NULL DEFAULT 0 COMMENT '评论人',
  member_json varchar(500) NOT NULL DEFAULT '' COMMENT '会员基本信息 （头像+账号+电话）',
  score decimal(10, 1) NOT NULL DEFAULT 0.0 COMMENT '评分 最多5星',
  content varchar(500) NOT NULL DEFAULT '' COMMENT '评论内容',
  imgs varchar(255) NOT NULL DEFAULT '' COMMENT '评论图',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '评论时间',
  info_id int(11) NOT NULL DEFAULT 0 COMMENT '信息id',
  is_show int(11) NOT NULL DEFAULT 1 COMMENT '是否显示',
  parent int(11) NOT NULL DEFAULT 0 COMMENT '上级id',
  fabulous int(11) NOT NULL DEFAULT 0 COMMENT '点赞次数',
  is_delete int(11) NOT NULL DEFAULT 0 COMMENT '删除标识 1是',
  uid int(11) NOT NULL DEFAULT 0 COMMENT '管理员id',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  PRIMARY KEY (comment_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '评论';

DROP TABLE IF EXISTS sns_info_report;
CREATE TABLE sns_info_report (
  report_id int(11) NOT NULL AUTO_INCREMENT,
  info_id int(11) NOT NULL DEFAULT 0 COMMENT '举报的信息',
  info_json varchar(255) NOT NULL DEFAULT '' COMMENT '信息的基础 json 存入id、图片、名称即可',
  tag varchar(255) NOT NULL DEFAULT '' COMMENT '举报的标签 可多个,分隔',
  report_explain varchar(255) NOT NULL DEFAULT '' COMMENT '举报的说明',
  contact varchar(11) NOT NULL DEFAULT '' COMMENT '联系方式',
  state int(11) NOT NULL DEFAULT 0 COMMENT '状态 0待审核  1已审核 -1已拒绝 2重新提交',
  refuse_reason varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝理由',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '提交时间',
  state_time int(11) NOT NULL DEFAULT 0 COMMENT '状态时间 ',
  member_id int(11) NOT NULL DEFAULT 0 COMMENT '提交人',
  uid int(11) NOT NULL DEFAULT 0 COMMENT '审核人',
  is_delete int(11) NOT NULL DEFAULT 0 COMMENT '删除标识 1为是',
  site_id int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  PRIMARY KEY (report_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '信息举报';