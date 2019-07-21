-- ManipleUserConsents schema for MySQL

CREATE TABLE consents (

    consent_id              INTEGER PRIMARY KEY AUTO_INCREMENT,

    created_at              INTEGER NOT NULL,

    updated_at              INTEGER NOT NULL,

    -- in order for maintaining uniqueness with soft-deletes deleted_at cannot
    -- be NULL, as MySQL does not support partial indexes
    deleted_at              INTEGER NOT NULL DEFAULT 0,

    consent_key             VARCHAR(128),

    is_active               TINYINT(1) NOT NULL,

    is_required             TINYINT(1) NOT NULL,

    display_priority        INTEGER NOT NULL DEFAULT 0,

    latest_version_id       INTEGER,

    -- for tracking whether users have responded to active consents
    latest_major_version_id INTEGER,

    UNIQUE INDEX consents_consent_key_deleted_at_idx (consent_key, deleted_at)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


-- consent text versions
CREATE TABLE consent_versions (

    consent_version_id      INTEGER PRIMARY KEY AUTO_INCREMENT,

    consent_id              INTEGER NOT NULL,

    -- points to major version this version was created from
    -- it also marks if this version is a major version (if major_version_id === consent_version_id)
    major_version_id        INTEGER,

    created_at              INTEGER NOT NULL,

    updated_at              INTEGER NOT NULL,

    title                   VARCHAR(191) NOT NULL,

    body                    TEXT NOT NULL,

    CONSTRAINT consent_versions_consent_id_fkey
        FOREIGN KEY (consent_id) REFERENCES consents (consent_id),

    CONSTRAINT consent_versions_parent_version_id_fkey
        FOREIGN KEY (consent_id, major_version_id) REFERENCES consent_versions (consent_id, consent_version_id)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


ALTER TABLE consents ADD CONSTRAINT consent_current_version_id_fkey
    FOREIGN KEY (consent_id, latest_version_id)
        REFERENCES consent_versions (consent_id, consent_version_id);

ALTER TABLE consents ADD CONSTRAINT consent_current_version_id_fkey
    FOREIGN KEY (consent_id, major_version_id)
        REFERENCES consent_versions (consent_id, consent_version_id);

CREATE TABLE user_consents (

    user_consent_id     INTEGER PRIMARY KEY AUTO_INCREMENT,

    user_id             INTEGER NOT NULL,

    consent_id          INTEGER NOT NULL,

    -- this is for displaying consent text
    consent_version_id  INTEGER NOT NULL,

    -- this is for tracking if consent is valid (in terms of being superseded by another major version)
    -- actially this is redundant and can be deducted from consent_version_id
    -- ARE WE GUARANTEED THAT consent_version(major_version_id) matches major_version_id?
    major_version_id    INTEGER NOT NULL,

    saved_at            INTEGER NOT NULL,

    revoked_at          INTEGER,

    -- was this consent required when user gave consent
    is_required         TINYINT(1) NOT NULL,

    decision            TINYINT(1) NOT NULL,

    display_priority    INTEGER NOT NULL DEFAULT 0,

    FOREIGN KEY (user_id) REFERENCES users (user_id),

    FOREIGN KEY (consent_id, consent_version_id) REFERENCES consent_versions (consent_id, consent_version_id),

    FOREIGN KEY (consent_id, major_version_id) REFERENCES consent_versions (consent_id, consent_version_id),

    UNIQUE (user_id, major_version_id)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
