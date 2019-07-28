ALTER TABLE consent_versions DROP FOREIGN KEY consent_versions_consent_id_major_version_id_fkey;
ALTER TABLE consents DROP FOREIGN KEY consents_latest_version_id_fkey;
ALTER TABLE consents DROP FOREIGN KEY consents_latest_major_version_id_fkey;
DROP TABLE user_consent_states;
DROP TABLE user_consents;
DROP TABLE consent_versions;
DROP TABLE consents;
DELETE FROM _schemas WHERE schema_id LIKE 'maniple-user-consents/%';
