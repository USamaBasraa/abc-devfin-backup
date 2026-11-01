-- ============================================================================
-- Create Sequence for SP_VENDOR_QUOTATIONS_HISTORY Primary Key
-- Purpose: Auto-increment VQH_ID
-- Date: November 4, 2025
-- ============================================================================

-- Create sequence
CREATE SEQUENCE sp_vendor_quotations_history_seq
START WITH 1
INCREMENT BY 1
NOCACHE
NOCYCLE;

-- Create trigger for auto-increment
CREATE OR REPLACE TRIGGER sp_vendor_quotations_history_trg
BEFORE INSERT ON SP_VENDOR_QUOTATIONS_HISTORY
FOR EACH ROW
BEGIN
    IF :NEW.VQH_ID IS NULL THEN
        SELECT sp_vendor_quotations_history_seq.NEXTVAL INTO :NEW.VQH_ID FROM DUAL;
    END IF;
END;
/

-- Create indexes for better performance
CREATE INDEX idx_vqh_vq_id ON SP_VENDOR_QUOTATIONS_HISTORY(VQ_ID);
CREATE INDEX idx_vqh_qr_id ON SP_VENDOR_QUOTATIONS_HISTORY(QUOTATION_REQUEST_ID);
CREATE INDEX idx_vqh_vendor_id ON SP_VENDOR_QUOTATIONS_HISTORY(VENDOR_ID);
CREATE INDEX idx_vqh_is_current ON SP_VENDOR_QUOTATIONS_HISTORY(IS_CURRENT);

-- Add comments for documentation
COMMENT ON TABLE SP_VENDOR_QUOTATIONS_HISTORY IS 'History table tracking all versions of vendor quotations including revisions after verbal negotiations';
COMMENT ON COLUMN SP_VENDOR_QUOTATIONS_HISTORY.VQH_ID IS 'Primary key - auto increment';
COMMENT ON COLUMN SP_VENDOR_QUOTATIONS_HISTORY.QR_VERSION IS 'Which quotation request version this quotation is for';
COMMENT ON COLUMN SP_VENDOR_QUOTATIONS_HISTORY.IS_CURRENT IS '1 = Current version, 0 = Old version';
COMMENT ON COLUMN SP_VENDOR_QUOTATIONS_HISTORY.VQ_ID IS 'Links to main vendor quotation table';

-- Commit changes
COMMIT;

