CREATE TABLE user_consent_states (

    user_consent_state_id   INTEGER PRIMARY KEY AUTO_INCREMENT,

    user_consent_id         INTEGER NOT NULL,

    saved_at                INTEGER NOT NULL,

    is_required             TINYINT(1) NOT NULL,

    state                   VARCHAR(32) NOT NULL,

    CONSTRAINT user_consent_states_user_consent_id_fkey
        FOREIGN KEY (user_consent_id)
        REFERENCES user_consents (user_consent_id)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
