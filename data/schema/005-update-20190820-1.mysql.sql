-- Store timestamps as BIGINT
ALTER TABLE consents MODIFY COLUMN created_at BIGINT NOT NULL;
ALTER TABLE consents MODIFY COLUMN updated_at BIGINT NOT NULL;
ALTER TABLE consents MODIFY COLUMN deleted_at BIGINT;

ALTER TABLE consent_versions MODIFY COLUMN created_at BIGINT NOT NULL;
ALTER TABLE consent_versions MODIFY COLUMN updated_at BIGINT NOT NULL;

ALTER TABLE user_consents MODIFY COLUMN saved_at   BIGINT NOT NULL;
ALTER TABLE user_consents MODIFY COLUMN expires_at BIGINT;

ALTER TABLE user_consent_states MODIFY COLUMN saved_at BIGINT NOT NULL;
