/*
Created: 14.08.2010
Modified: 18.02.2016
Model: MySQL 5.1
Database: MySQL 5.1
*/


-- Create tables section -------------------------------------------------

-- Table crop_cell

CREATE TABLE crop_cell
(
  id_cell Int UNSIGNED NOT NULL AUTO_INCREMENT
 COMMENT 'Код ячейки',
  id_user Int UNSIGNED
 COMMENT 'Пользователь',
  n_cell Int UNSIGNED NOT NULL
 COMMENT 'Номер ячейки',
  dt_event Int UNSIGNED NOT NULL
 COMMENT 'Дата создания ячейки',
  v_text Text
 COMMENT 'Текст сообщения'
)
 DEFAULT CHARACTER SET utf8
 COLLATE utf8_general_ci
;

CREATE INDEX IX_Relationship1 ON crop_cell (id_user)
;

ALTER TABLE crop_cell ADD  PRIMARY KEY (id_cell)
;

ALTER TABLE crop_cell ADD UNIQUE n_cell (n_cell)
;

-- Create relationships section ------------------------------------------------- 

ALTER TABLE crop_cell ADD CONSTRAINT Relationship1 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE RESTRICT ON UPDATE RESTRICT
;

