-- SQL migration for projectbridge plugin tables (GLPI 11+)

CREATE TABLE IF NOT EXISTS `glpi_plugin_projectbridge_entities` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_id` INT(11) NOT NULL,
  `contract_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_projectbridge_contracts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contract_id` INT(11) NOT NULL,
  `project_id` INT(11) NOT NULL,
  `nb_hours` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_projectbridge_tickets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` INT(11) NOT NULL,
  `projecttasks_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_projectbridge_configs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `value` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_projectbridge_states` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(250) NOT NULL,
  `projectstates_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `glpi_plugin_projectbridge_contractquotaalerts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contract_id` INT(11) NOT NULL,
  `quotaAlert` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
