-- ManipleUserConsents schema for MySQL

CREATE TABLE /* PREFIX */consents (

    consent_id          INTEGER PRIMARY KEY AUTO_INCREMENT,

    created_at          INTEGER NOT NULL,

    updated_at          INTEGER NOT NULL,

    deleted_at          INTEGER,

    is_active           TINYINT(1) NOT NULL,

    display_priority    INTEGER NOT NULL DEFAULT 0,

    current_version_id  INTEGER

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


CREATE TABLE /* PREFIX */consent_versions (

    consent_version_id  INTEGER PRIMARY KEY AUTO_INCREMENT,

    consent_id          INTEGER NOT NULL,

    created_at          INTEGER NOT NULL,

    is_required         TINYINT(1) NOT NULL,

    title               VARCHAR(191) NOT NULL,

    body                TEXT NOT NULL,

    body_type           VARCHAR(32) NOT NULL,

    content_hash        VARCHAR(64) NOT NULL UNIQUE,

    FOREIGN KEY (consent_id) REFERENCES /* PREFIX */consents (consent_id)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


ALTER TABLE /* PREFIX */consents ADD
    FOREIGN KEY (current_version_id) REFERENCES /* PREFIX */consent_versions (consent_version_id);


CREATE TABLE /* PREFIX */user_consents (

    user_consent_id     INTEGER PRIMARY KEY AUTO_INCREMENT,

    user_id             INTEGER NOT NULL,

    consent_id          INTEGER NOT NULL,

    consent_version_id  INTEGER NOT NULL,

    saved_at            INTEGER NOT NULL,

    revoked_at          INTEGER,

    decision            TINYINT(1) NOT NULL,

    display_priority    INTEGER NOT NULL DEFAULT 0,

    FOREIGN KEY (user_id) REFERENCES /* PREFIX */users (user_id),

    FOREIGN KEY (consent_id, consent_version_id) REFERENCES /* PREFIX */consent_versions (consent_id, consent_version_id),

    UNIQUE (user_id, consent_version_id)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
