-- =============================================================================
-- Production patch: COST_CENTER_MAPPING for /cost-center-mapping
-- Fixes notice:
--   "Loaded from API. Database sync failed — mappings will not be saved until the table exists."
-- Run as the CMS schema user (same as DB_USERNAME in .env).
-- =============================================================================

-- 1) Create table if missing
DECLARE
    v_cnt NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_cnt
    FROM USER_TABLES
    WHERE TABLE_NAME = 'COST_CENTER_MAPPING';

    IF v_cnt = 0 THEN
        EXECUTE IMMEDIATE '
            CREATE TABLE COST_CENTER_MAPPING (
                ID VARCHAR2(20) NOT NULL,
                TITLE VARCHAR2(255),
                SCHOOL_ID VARCHAR2(50),
                SCHOOL_TITLE VARCHAR2(255),
                CMS_SCHOOL_ID NUMBER,
                CMS_DEPT_ID NUMBER,
                CMS_DEPT_IDS VARCHAR2(500),
                CREATED_AT TIMESTAMP(6) DEFAULT SYSTIMESTAMP,
                UPDATED_AT TIMESTAMP(6) DEFAULT SYSTIMESTAMP,
                CONSTRAINT COST_CENTER_MAPPING_PK PRIMARY KEY (ID)
            )
        ';
    END IF;
END;
/

-- 2) Add CMS_DEPT_IDS if table exists but column is missing
DECLARE
    v_cnt NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_cnt
    FROM USER_TAB_COLUMNS
    WHERE TABLE_NAME = 'COST_CENTER_MAPPING'
      AND COLUMN_NAME = 'CMS_DEPT_IDS';

    IF v_cnt = 0 THEN
        EXECUTE IMMEDIATE 'ALTER TABLE COST_CENTER_MAPPING ADD (CMS_DEPT_IDS VARCHAR2(500))';
    END IF;
END;
/

-- 3) Optional helpful indexes
DECLARE
    v_cnt NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_cnt
    FROM USER_INDEXES
    WHERE INDEX_NAME = 'IDX_CCM_CMS_SCHOOL_ID';

    IF v_cnt = 0 THEN
        EXECUTE IMMEDIATE 'CREATE INDEX IDX_CCM_CMS_SCHOOL_ID ON COST_CENTER_MAPPING (CMS_SCHOOL_ID)';
    END IF;
END;
/

DECLARE
    v_cnt NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_cnt
    FROM USER_INDEXES
    WHERE INDEX_NAME = 'IDX_CCM_CMS_DEPT_ID';

    IF v_cnt = 0 THEN
        EXECUTE IMMEDIATE 'CREATE INDEX IDX_CCM_CMS_DEPT_ID ON COST_CENTER_MAPPING (CMS_DEPT_ID)';
    END IF;
END;
/

COMMIT;
