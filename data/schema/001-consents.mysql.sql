CREATE TABLE consents (

    consent_id              INTEGER PRIMARY KEY AUTO_INCREMENT,

    created_at              INTEGER NOT NULL,

    updated_at              INTEGER,

    deleted_at              INTEGER,

    is_active               TINYINT(1) NOT NULL DEFAULT 0,

    is_required             TINYINT(1) NOT NULL DEFAULT 0,

    display_priority        INTEGER NOT NULL DEFAULT 0,

    latest_version_id       INTEGER,

    latest_major_version_id INTEGER,

    INDEX consents_is_active_idx (is_active)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
