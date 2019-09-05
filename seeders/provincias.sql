-- --------------------------------------------------------
-- Host:                         hxv-sntdb.hexacta.com
-- Versión del servidor:         10.0.33-MariaDB-1~xenial - mariadb.org binary distribution
-- SO del servidor:              debian-linux-gnu
-- HeidiSQL Versión:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando datos para la tabla snt_develop.provincia: ~24 rows (aproximadamente)
DELETE FROM `provincia`;
/*!40000 ALTER TABLE `provincia` DISABLE KEYS */;
INSERT INTO `provincia` (`id`, `nombre`, `fecha_creado`, `fecha_modificado`) VALUES
	(1, 'Buenos Aires', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(2, 'Capital Federal', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(3, 'Catamarca', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(4, 'Chaco', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(5, 'Chubut', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(6, 'Córdoba', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(7, 'Corrientes', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(8, 'Entre Ríos', '2017-10-04 11:39:53', '2017-10-04 11:39:53'),
	(9, 'Formosa', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(10, 'Jujuy', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(11, 'La Pampa', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(12, 'La Rioja', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(13, 'Mendoza', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(14, 'Misiones', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(15, 'Neuquén', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(16, 'Río Negro', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(17, 'Salta', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(18, 'San Juan', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(19, 'San Luis', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(20, 'Santa Cruz', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(21, 'Santa Fe', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(22, 'Santiago del Estero', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(23, 'Tierra del Fuego', '2017-10-04 11:39:54', '2017-10-04 11:39:54'),
	(24, 'Tucumán', '2017-10-04 11:39:54', '2017-10-04 11:39:54');
/*!40000 ALTER TABLE `provincia` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
