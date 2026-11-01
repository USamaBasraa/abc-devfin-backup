-- ============================================================================
-- Add VERSION_NUMBER to Vendor Quotations History Table
-- Purpose: Track version number for each quotation revision
-- Date: November 4, 2025
-- ============================================================================

-- Add VERSION_NUMBER column to track each revision
ALTER TABLE SP_VENDOR_QUOTATIONS_HISTORY 
ADD VERSION_NUMBER NUMBER(5);

-- Add comment for documentation
COMMENT ON COLUMN SP_VENDOR_QUOTATIONS_HISTORY.VERSION_NUMBER IS 'Version number for this vendor quotation (1, 2, 3 for revisions)';

-- Create index for better performance
CREATE INDEX idx_vqh_version ON SP_VENDOR_QUOTATIONS_HISTORY(VERSION_NUMBER);

-- Commit changes
COMMIT;

