-- Sequence for sp_vendor_email_log primary key auto-increment
CREATE SEQUENCE sp_vendor_email_log_seq
START WITH 1
INCREMENT BY 1
NOCACHE
NOCYCLE;

-- Trigger for auto-increment on ve_id
CREATE OR REPLACE TRIGGER sp_vendor_email_log_trg
BEFORE INSERT ON sp_vendor_email_log
FOR EACH ROW
BEGIN
    IF :NEW.ve_id IS NULL THEN
        SELECT sp_vendor_email_log_seq.NEXTVAL INTO :NEW.ve_id FROM DUAL;
    END IF;
END;
/

-- Create indexes for better performance
CREATE INDEX idx_ve_qr_id ON sp_vendor_email_log(ve_qr_id);
CREATE INDEX idx_ve_vendor_id ON sp_vendor_email_log(ve_vendor_id);
CREATE INDEX idx_ve_sent_at ON sp_vendor_email_log(ve_sent_at);

-- Comments for documentation
COMMENT ON TABLE sp_vendor_email_log IS 'Tracks vendor emails sent for quotation requests';
COMMENT ON COLUMN sp_vendor_email_log.ve_id IS 'Primary key - auto increment';
COMMENT ON COLUMN sp_vendor_email_log.ve_qr_id IS 'Quotation Request ID';
COMMENT ON COLUMN sp_vendor_email_log.ve_vendor_id IS 'Vendor ID';
COMMENT ON COLUMN sp_vendor_email_log.ve_sent_by IS 'Employee ID or username who sent';
COMMENT ON COLUMN sp_vendor_email_log.ve_sent_at IS 'Email sent timestamp';
COMMENT ON COLUMN sp_vendor_email_log.ve_remarks IS 'Optional remarks or notes';
COMMENT ON COLUMN sp_vendor_email_log.ve_email_subject IS 'Email subject line';
COMMENT ON COLUMN sp_vendor_email_log.ve_status IS 'Email status: sent/failed/pending';

