-- ManipleUserConsents schema for MySQL

CREATE TABLE /* PREFIX */consents (

    consent_id      INTEGER PRIMARY KEY AUTO_INCREMENT,

    created_at      INTEGER NOT NULL,

    updated_at      INTEGER NOT NULL,

    deleted_at      INTEGER,

    is_active       TINYINT(1) NOT NULL,

    is_required     TINYINT(1) NOT NULL,

    display_order   INTEGER NOT NULL,

    title           VARCHAR(191) NOT NULL,

    body            TEXT NOT NULL,

    body_type       VARCHAR(32) NOT NULL

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


CREATE TABLE /* PREFIX */user_consents (

    user_id INTEGER NOT NULL,

    consent_version_id INTEGER NOT NULL,

    created_at INTEGER NOT NULL,

    revoked_at INTEGER,

    FOREIGN KEY (user_id) REFERENCES /* PREFIX */users (user_id),

    FOREIGN KEY (consent_version_id) REFERENCES /* PREFIX */consent_versions (consent_version_id),

    PRIMARY KEY (user_id, consent_version_id)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


-- UserConsents

-- SELECT pv.*, uc.* from user_consents join consent_policy_versions

-- GET ALL POLICIES THIS USER HAS -- GOOD IDEA TO STORE IT IN SESSION, BECAUSE IT HAS TO BE QUICKLY DETERMINED!
-- SELECT policy_id FROM user_consents JOIN consent_policy_versions ON uc.policy_version_id = cpv.policy_version_id
-- WHERE policy_id IN (POLICY_IDS)

-- POLICY CONSENT SCREEN
-- foreach policies -- if present show accepted version (MAYBE with notice that this has been updated?), otherwise show current version,
