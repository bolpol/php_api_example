DROP TABLE IF EXISTS `address_table`;
CREATE TABLE `address_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` varchar(50) UNIQUE,
  `base_address` varchar(100) NOT NULL,
  `base_balance` varchar(100) NULL,
  `priv_key_base_address` varchar(100) NOT NULL,
  `bonus_address` varchar(100) NOT NULL,
  `bonus_balance` varchar(100) NULL,
  `priv_key_bonus_address` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci' ENGINE=InnoDB;