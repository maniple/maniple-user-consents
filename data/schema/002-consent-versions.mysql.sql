# noinspection SqlResolveForFile

CREATE TABLE consent_versions (

    consent_version_id      INTEGER PRIMARY KEY AUTO_INCREMENT,

    consent_id              INTEGER NOT NULL,

    -- points to major version this version was created from, it also marks
    -- if this version is a major version (if major_version_id === consent_version_id)
    major_version_id        INTEGER,

    created_at              INTEGER NOT NULL,

    updated_at              INTEGER,

    title                   VARCHAR(191) NOT NULL,

    body                    TEXT NOT NULL,

    CONSTRAINT consent_versions_consent_id_fkey
        FOREIGN KEY (consent_id)
        REFERENCES consents (consent_id),

    -- index on (consent_id) is required, otherwise we get this:
    -- ERROR 1215 (HY000): Cannot add foreign key constraint
    INDEX consent_versions_consent_id_idx (consent_id),

    CONSTRAINT consent_versions_consent_id_major_version_id_fkey
        FOREIGN KEY (consent_id, major_version_id)
        REFERENCES consent_versions (consent_id, consent_version_id)

) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


ALTER TABLE consents ADD CONSTRAINT consents_latest_version_id_fkey
    FOREIGN KEY (consent_id, latest_version_id)
    REFERENCES consent_versions (consent_id, consent_version_id);


ALTER TABLE consents ADD CONSTRAINT consents_latest_major_version_id_fkey
    FOREIGN KEY (consent_id, latest_major_version_id)
    REFERENCES consent_versions (consent_id, consent_version_id);
