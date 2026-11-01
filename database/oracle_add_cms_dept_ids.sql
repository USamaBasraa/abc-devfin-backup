-- Multi-department support for Cost Center Mapping
-- Run as BNUCMS (or table owner) on the live Oracle DB.
-- App user (e.g. facultyconn) cannot ALTER tables.

-- 1) Add column (skip if it already exists)
ALTER TABLE BNUCMS.COST_CENTER_MAPPING ADD (
    CMS_DEPT_IDS VARCHAR2(1000)
);

COMMENT ON COLUMN BNUCMS.COST_CENTER_MAPPING.CMS_DEPT_IDS IS
    'Comma-separated CMS department IDs for multi-department mapping';

-- 2) Ensure app connect user can read/write it (adjust username if different)
-- GRANT SELECT, INSERT, UPDATE ON BNUCMS.COST_CENTER_MAPPING TO FACULTYCONN;

-- 3) Public synonym (if not already present)
-- CREATE OR REPLACE PUBLIC SYNONYM COST_CENTER_MAPPING FOR BNUCMS.COST_CENTER_MAPPING;

-- Verify:
-- SELECT COLUMN_NAME, DATA_TYPE, DATA_LENGTH
-- FROM ALL_TAB_COLUMNS
-- WHERE TABLE_NAME = 'COST_CENTER_MAPPING'
--   AND COLUMN_NAME = 'CMS_DEPT_IDS';
