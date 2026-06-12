UPDATE `groups` SET `is_public` = CASE 
    WHEN `is_public` = 'Y' THEN 1 
    WHEN `is_public` = 'N' THEN 0 
    ELSE 1 
END;

ALTER TABLE `groups` 
MODIFY COLUMN `is_public` TINYINT(1) NOT NULL DEFAULT 1;
