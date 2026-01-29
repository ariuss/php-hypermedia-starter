-- Down migration: setup
BEGIN TRANSACTION;

DROP TABLE IF EXISTS users;

COMMIT;