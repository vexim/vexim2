CREATE TABLE `dkim` (
    `dkim_id`       INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `domain_id`     INT(10) UNSIGNED NOT NULL,
    `selector`      VARCHAR(63) NOT NULL DEFAULT 'default',
    `private_key`   TEXT NOT NULL,
    `public_key`    TEXT NOT NULL,
    `canonical`     ENUM('simple', 'relaxed') NOT NULL DEFAULT 'relaxed',
    `sign_headers`  VARCHAR(255) DEFAULT NULL,  -- Optional: custom headers to sign
    `enabled`       TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`dkim_id`),
    UNIQUE KEY `domain_selector` (`domain_id`, `selector`),
    FOREIGN KEY (`domain_id`) REFERENCES `domains` (`domain_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
