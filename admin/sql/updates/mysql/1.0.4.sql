ALTER TABLE `#__ehealth_portal_immunisation` ADD `administration_part` INT(11) NOT NULL DEFAULT 0 AFTER `asset_id`;

ALTER TABLE `#__ehealth_portal_immunisation` ADD `immunisation_vaccine_type` INT(11) NOT NULL DEFAULT 0 AFTER `immunisation_up_to_date`;
