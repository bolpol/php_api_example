DROP TABLE IF EXISTS `users_table`;
CREATE TABLE `users_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) UNIQUE,
  `mode` varchar(10) NOT NULL,
  `nonce` integer(11) NOT NULL,
  PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci' ENGINE=InnoDB;