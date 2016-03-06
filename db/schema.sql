/*
Created: 14.08.2010
Modified: 06.03.2016
Model: MySQL 5.1
Database: MySQL 5.1
*/

-- Create tables section -------------------------------------------------

-- Table crop_cell

CREATE TABLE crop_cell
(
  id_cell Int UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Код ячейки',
  id_user Int UNSIGNED,
  y Int UNSIGNED
  COMMENT 'Номер группы, в которую входит ячейка. Нумерация - снизу вверх, начиная с 1.',
  x Int UNSIGNED
  COMMENT 'Номер ячейки в группе. Нумерация - справа налево, начиная с 1.',
  n Int UNSIGNED
  COMMENT 'Номер ячейки, считая справа снизу',
  n_em Tinyint UNSIGNED NOT NULL
  COMMENT 'Код эмоции',
  dt_event Int UNSIGNED NOT NULL
  COMMENT 'Дата привязки ячейки',
  v_mail Varchar(255) NOT NULL
  COMMENT 'Электронный адрес отправителя',
  v_text Text
  COMMENT 'Сообщение',
  v_temp Varchar(30)
  COMMENT 'Директория, в которой схарятся временные файлы',
  b_ok Bool NOT NULL DEFAULT 0
  COMMENT 'Признак подтверждённости ячейки. Вместе с этим обнуляется v_temp',
  b_ban Bool NOT NULL DEFAULT 0
  COMMENT 'Признак забаненности ячейки',
 PRIMARY KEY (id_cell)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Create relationships section ------------------------------------------------- 

ALTER TABLE crop_cell ADD CONSTRAINT Relationship1 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;


