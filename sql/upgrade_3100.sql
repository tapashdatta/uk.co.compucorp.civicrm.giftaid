-- /*******************************************************
-- * civicrm_civigiftaid_batchsettings
-- *******************************************************/

CREATE TABLE IF NOT EXISTS `civicrm_civigiftaid_batchsettings` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `batch_id`                INT UNSIGNED COMMENT '',
  `financial_types_enabled` TEXT COMMENT '',
  `globally_enabled`        TINYINT COMMENT '',
  PRIMARY KEY (`id`),
  CONSTRAINT FK_civicrm_civigiftaid_batchsettings_batch_id FOREIGN KEY (`batch_id`) REFERENCES `civicrm_batch` (`id`) ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci;
