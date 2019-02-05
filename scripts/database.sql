SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `characterstatus` (
  `id` int(11) UNSIGNED NOT NULL,
  `enabled` tinyint(1) UNSIGNED DEFAULT NULL,
  `round_over` tinyint(1) UNSIGNED DEFAULT NULL,
  `fightgroup_id` int(11) UNSIGNED DEFAULT NULL,
  `rolcharacter_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fightgroup` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numenemies` int(11) UNSIGNED DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `round` int(11) UNSIGNED DEFAULT NULL,
  `enabled` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fightgroupcharacter` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `attack` int(11) UNSIGNED DEFAULT NULL,
  `defense` int(11) UNSIGNED DEFAULT NULL,
  `dexterity` int(11) UNSIGNED DEFAULT NULL,
  `life` int(11) UNSIGNED DEFAULT NULL,
  `maxlife` int(11) UNSIGNED DEFAULT NULL,
  `round_over` tinyint(1) UNSIGNED DEFAULT NULL,
  `fightgroup_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `infocommand` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory` (
  `id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) UNSIGNED DEFAULT NULL,
  `item` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charactername` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rolcharacter` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `life` int(11) UNSIGNED DEFAULT NULL,
  `level` int(11) UNSIGNED DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attack` int(11) UNSIGNED DEFAULT NULL,
  `defense` int(11) UNSIGNED DEFAULT NULL,
  `dexterity` int(11) UNSIGNED DEFAULT NULL,
  `maxlife` int(11) UNSIGNED DEFAULT NULL,
  `control` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `telegramuser` (
  `id` int(11) UNSIGNED NOT NULL,
  `userid` int(11) UNSIGNED DEFAULT NULL,
  `ismaster` tinyint(1) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `characterstatus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_characterstatus_fightgroup` (`fightgroup_id`),
  ADD KEY `index_foreignkey_characterstatus_rolcharacter` (`rolcharacter_id`);

ALTER TABLE `fightgroup`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `fightgroupcharacter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_fightgroupcharacter_fightgroup` (`fightgroup_id`);

ALTER TABLE `infocommand`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rolcharacter`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `telegramuser`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `characterstatus`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `fightgroup`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `fightgroupcharacter`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `infocommand`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `inventory`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `rolcharacter`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `telegramuser`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `characterstatus`
  ADD CONSTRAINT `c_fk_characterstatus_fightgroup_id` FOREIGN KEY (`fightgroup_id`) REFERENCES `fightgroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `c_fk_characterstatus_rolcharacter_id` FOREIGN KEY (`rolcharacter_id`) REFERENCES `rolcharacter` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

ALTER TABLE `fightgroupcharacter`
  ADD CONSTRAINT `c_fk_fightgroupcharacter_fightgroup_id` FOREIGN KEY (`fightgroup_id`) REFERENCES `fightgroup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
