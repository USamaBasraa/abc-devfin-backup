-- ============================================================================
-- Add Version Tracking to Email Log
-- Purpose: Track which request version email was sent for
-- Date: November 4, 2025
-- ============================================================================

-- Add new column to track request version
ALTER TABLE SP_VENDOR_EMAIL_LOG 
ADD VE_QR_VERSION NUMBER(5);

-- Add comment for documentation
COMMENT ON COLUMN SP_VENDOR_EMAIL_LOG.VE_QR_VERSION IS 'Quotation Request Version Number when email was sent';

-- Create index for better query performance
CREATE INDEX idx_ve_qr_version ON SP_VENDOR_EMAIL_LOG(VE_QR_VERSION);

-- Commit changes
COMMIT;

