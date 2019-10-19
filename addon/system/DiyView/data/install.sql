SET NAMES 'utf8';

DROP TABLE IF EXISTS nc_site_diy_view;
CREATE TABLE nc_site_diy_view (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  site_id int(11) NOT NULL COMMENT '站点id',
  name varchar(50) NOT NULL,
  addon_name varchar(50) NOT NULL DEFAULT '' COMMENT '插件名称',
  title varchar(255) NOT NULL COMMENT '模板名称',
  value text NOT NULL COMMENT '配置值',
  type varchar(255) NOT NULL DEFAULT 'OTHER' COMMENT '类型（DEFAULT,OTHER,COMPONENT）',
  create_time int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  update_time int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  show_type varchar(255) NOT NULL DEFAULT 'H5' COMMENT '展示方式(H5,WEAPP,APP)',
  icon varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  PRIMARY KEY (id),
  INDEX IDX_nc_site_diy_view (site_id, name)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
COMMENT = '自定义模板表';