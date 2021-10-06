use project;

set sql_mode = '';
set character set 'utf8';
set names 'utf8';

create table user
(
  id                int primary key                     not null auto_increment,
  email             varchar(255) default ''             not null,
  is_banned         tinyint default 0                   not null,
  is_test           tinyint default 0                   not null,
  created_at        timestamp default current_timestamp not null,
  updated_at        timestamp default '0000-00-00 00:00:00' not null,
  password          varchar(255)                        not null,
  token             varchar(255)                        null,
  fullname          varchar(255) default 'Anonymous'    not null
);
