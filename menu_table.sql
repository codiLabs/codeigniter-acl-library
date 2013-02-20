CREATE TABLE IF NOT EXISTS `menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_segment` varchar(96) NOT NULL,
  `menu_name` varchar(64) NOT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `module_id` (`module_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `menu_segment`, `menu_name`) VALUES
(1, 'employee', 'Daftar Pegawai'),
(2, 'customer', 'Daftar Customer'),
(3, 'employee/add', 'Tambah Pegawai');