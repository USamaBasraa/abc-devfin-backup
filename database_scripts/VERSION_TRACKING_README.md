# Version Tracking System - Complete Documentation

## Overview
This system tracks versions for:
1. Quotation Requests (when quantity is revised)
2. Vendor Emails (which request version email was sent for)
3. Vendor Quotations (which request version, and negotiation versions)

---

## Database Changes

### 1. Email Log Enhancement
**Table:** `SP_VENDOR_EMAIL_LOG`

**New Column:**
- `VE_QR_VERSION` - Tracks which request version the email was sent for

**Script:** `add_version_tracking_email_log.sql`

### 2. Vendor Quotations Enhancement
**Table:** `SP_VENDOR_QUOTATIONS`

**New Columns:**
- `QR_VERSION` - Which request version this quotation is for
- `QUANTITY_QUOTED` - Quantity quoted by vendor
- `IS_CURRENT` - 1 = Current, 0 = Old version

### 3. Vendor Quotations History
**Table:** `SP_VENDOR_QUOTATIONS_HISTORY`

**All Columns:**
- `VQH_ID` - Primary key (auto-increment)
- `VERSION_NUMBER` - Version number (1, 2, 3 for revisions)
- `QR_VERSION` - Which request version
- `IS_CURRENT` - 1 = Current, 0 = Old
- `VQ_ID` - Links to main quotation
- `VENDOR_ID` - Vendor who submitted
- `QUOTATION_REQUEST_ID` - Request ID
- `PRICE`, `CURRENCY`, `DELIVERY_TIME`, etc.

**Scripts:**
- `vendor_quotations_history_sequence.sql` (sequence and trigger)
- `add_version_number_to_history.sql` (add VERSION_NUMBER)

---

## How It Works

### Scenario 1: First Time Flow

```
Step 1: Create Request (qr_id=100, version=1)
Step 2: Send Email to Vendors
        → Email Log: ve_qr_version = 1
Step 3: Procurement Submits Vendor Response
        → Main: vq_id=500, qr_version=1, is_current=1
        → History: vqh_id=1, version_number=1, is_current=1
```

### Scenario 2: Request Revised (Quantity Changed)

```
Step 1: User Revises Request Quantity
        → Request: version = 2, is_revised = 1
        → History: Old version IS_CURRENT = 0
        → History: New version IS_CURRENT = 1

Step 2: Send Email Again
        → Email Log: ve_qr_version = 2 (for new version)

Step 3: Submit New Vendor Response
        → Main: vq_id=501, qr_version=2, is_current=1
        → History: version_number=1, is_current=1
```

### Scenario 3: Verbal Negotiation (Same Request)

```
Step 1: Procurement Updates Quotation (price changed)
        → Main: Old quotation is_current = 0
        → Main: New quotation created, is_current = 1
        
        → History: Old entry IS_CURRENT = 0
        → History: New entry version_number++, IS_CURRENT = 1
```

---

## Code Changes

### File: `VendorEmailLog.php`
- Added `ve_qr_version` to fillable

### File: `VendorQuotation.php`
- Added `qr_version`, `quantity_quoted`, `is_current` to fillable

### File: `VendorQuotationController.php`

#### Method: `sendMailToVendors()`
- Gets current request version
- Saves version in email log
- Clear comments explain each step

#### Method: `store()`
- Tracks request version
- Marks old quotations as not current
- Creates history entry
- Handles first time vs update logic
- Simple step-by-step code

---

## Database Queries

### Get Current Version for Request
```sql
SELECT current_version 
FROM sp_quotation_request 
WHERE qr_id = ?
```

### Get All Emails for Request
```sql
SELECT * FROM sp_vendor_email_log 
WHERE ve_qr_id = ? 
ORDER BY ve_qr_version DESC, ve_sent_at DESC
```

### Get Current Quotation
```sql
SELECT * FROM sp_vendor_quotations 
WHERE quotation_request_id = ? 
  AND vendor_id = ? 
  AND is_current = 1
```

### Get Quotation History
```sql
SELECT * FROM SP_VENDOR_QUOTATIONS_HISTORY 
WHERE VQ_ID = ? 
ORDER BY VERSION_NUMBER DESC
```

---

## Testing Checklist

- [ ] Run all SQL scripts in order
- [ ] Test email sending - check ve_qr_version saved
- [ ] Test first time quotation submission
- [ ] Test quotation update (negotiation)
- [ ] Test request revision - email log tracks new version
- [ ] Verify history table has correct versions
- [ ] Check IS_CURRENT flags work correctly

---

## Future Enhancements

1. Add UI to show version history in modal
2. Show email logs with version numbers
3. Compare old vs new quotation versions
4. Track who made changes (audit trail)

---

## Contact
For questions or issues, contact development team.

Last Updated: November 4, 2025

