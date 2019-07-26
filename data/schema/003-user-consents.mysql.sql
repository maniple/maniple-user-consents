CREATE TABLE user_consents (

    user_consent_id         INTEGER PRIMARY KEY AUTO_INCREMENT,

    user_id                 INTEGER NOT NULL,

    -- this is for displaying consent text
    consent_version_id      INTEGER NOT NULL,

    -- was this consent required when the user made decision for the first time
    is_required             TINYINT(1) NOT NULL,

    display_priority        INTEGER NOT NULL DEFAULT 0,

    saved_at                INTEGER NOT NULL,

    state                   VARCHAR(64) NOT NULL,

    CONSTRAINT user_consents_user_id_fkey
        FOREIGN KEY (user_id)
        REFERENCES users (user_id),

    CONSTRAINT user_consents_consent_version_id_fkey
        FOREIGN KEY (consent_version_id)
        REFERENCES consent_versions (consent_version_id),

    INDEX user_consents_user_id_state_idx (user_id, state)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
