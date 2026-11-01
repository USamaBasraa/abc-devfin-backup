# 📚 Vendor Quotation Version System - Complete Guide
**Roman Urdu Documentation for Juniors**

---

## 📖 Table of Contents
1. [System Overview](#system-overview)
2. [Database Tables](#database-tables)
3. [Complete Flow](#complete-flow)
4. [Code Explanation](#code-explanation)
5. [Examples](#examples)

---

## 🎯 System Overview

**Ye system kya karta hai?**

Jab hum kisi item ke liye vendors se quotation mangwate hain, to:
1. ✉️ Vendors ko email jaati hai
2. 📝 Vendors quotation submit karte hain
3. 🔄 Agar quantity ya requirements change hoti hai, to REVISE karte hain
4. 📊 Har change ki **HISTORY** maintain hoti hai

**Kyun zaruri hai version tracking?**
- Pata chale ke kis version ke against email gayi
- Pata chale vendor ne kis version ka response diya
- Agar price negotiate ho to purane versions compare kar sakein
- Audit trail maintain ho

---

## 📦 Database Tables

### 1️⃣ **SP_QUOTATION_REQUESTS** (Main Request Table)
```
Purpose: Budget item ke against quotation request banti hai
```

| Column | Description | Example |
|--------|-------------|---------|
| `QR_ID` | Request ki unique ID | 101 |
| `ITEM_ID` | Konsi budget item ke liye | 45 |
| `ITEM_NAME` | Item ka naam | "Dell Laptop" |
| `REQUESTED_QUANTITY` | Kitni quantity chahiye | 10 |
| `CURRENT_VERSION` | Latest version number | 2 |
| `IS_REVISED` | Kya revise hua hai? | 1 (Yes) / 0 (No) |

**Kab insert hota hai?**
- Jab pehli baar quotation request create karte hain
- Budget list se item select karke "Request Quotation" click karte hain

**Kab update hota hai?**
- Jab quantity ya requirements REVISE karte hain
- `CURRENT_VERSION` increment hota hai (1 → 2 → 3...)
- `IS_REVISED` = 1 ho jata hai

---

### 2️⃣ **SP_QUOTATION_REQUEST_HISTORY** (Request History Table)
```
Purpose: Har revision ki history maintain karta hai
```

| Column | Description | Example |
|--------|-------------|---------|
| `QRH_ID` | History record ID | 501 |
| `QR_ID` | Original request ID | 101 |
| `VERSION_NUMBER` | Konsa version | 1, 2, 3... |
| `IS_CURRENT` | Current version hai? | 1 / 0 |
| `REQUESTED_QUANTITY` | Us time kitni quantity thi | 10 |
| `ITEM_NAME` | Item name | "Dell Laptop" |
| `REMARKS` | Revision ki wajah | "Quantity increased" |
| `CREATED_AT` | Kab bani | 2025-11-04 10:00:00 |

**Kab insert hota hai?**
1. **Pehli baar request create hoti hai:**
   - Version 1 ban jata hai
   - `IS_CURRENT = 1`

2. **Jab revise karte hain:**
   - Purane versions ka `IS_CURRENT = 0` ho jata hai
   - Naya version insert hota hai with `IS_CURRENT = 1`

**Example Flow:**
```
Time 1: Pehli baar request
├─ Version 1: Quantity = 10, IS_CURRENT = 1

Time 2: Quantity 15 kar di (Revise)
├─ Version 1: Quantity = 10, IS_CURRENT = 0 (Old)
└─ Version 2: Quantity = 15, IS_CURRENT = 1 (Current)
```

---

### 3️⃣ **SP_VENDOR_EMAIL_LOG** (Email History Table)
```
Purpose: Kis vendor ko kab email gayi, kis version ke liye
```

| Column | Description | Example |
|--------|-------------|---------|
| `VE_ID` | Email log ID | 301 |
| `VE_QR_ID` | Konsi request ke liye | 101 |
| `VE_QR_VERSION` | Konse version ke liye | 2 |
| `VE_VENDOR_ID` | Konse vendor ko | 11 |
| `VE_SENT_AT` | Kab bheji | 2025-11-04 11:00:00 |
| `VE_SENT_BY` | Kisne bheji | "admin" |

**Kab insert hota hai?**
- Jab "Email Vendors" tab se email send karte hain
- Har vendor ke liye alag entry banti hai
- Us time ka `CURRENT_VERSION` save hota hai

**Example:**
```
Request ID: 101, Current Version: 2

Email bhejte waqt:
├─ Vendor 1: VE_QR_VERSION = 2
├─ Vendor 2: VE_QR_VERSION = 2
└─ Vendor 3: VE_QR_VERSION = 2

Agar baad mein Version 3 ban jaye aur dobara email bhejein:
├─ Vendor 1: VE_QR_VERSION = 3 (New entry)
└─ Vendor 2: VE_QR_VERSION = 3 (New entry)
```

---

### 4️⃣ **SP_VENDOR_QUOTATIONS** (Vendor Response Table)
```
Purpose: Vendor ne jo quotation submit ki wo save hoti hai
```

| Column | Description | Example |
|--------|-------------|---------|
| `VQ_ID` | Quotation ID | 201 |
| `VENDOR_ID` | Konse vendor ne submit ki | 11 |
| `QUOTATION_REQUEST_ID` | Kis request ke liye | 101 |
| `QR_VERSION` | Kis version ke against | 2 |
| `QUANTITY_QUOTED` | Kitni quantity quote ki | 10 |
| `PRICE` | Unit price | 85000 |
| `CURRENCY` | Currency | PKR |
| `DELIVERY_TIME` | Delivery time | "10 days" |
| `WARRANTY` | Warranty | "1 year" |
| `IS_CURRENT` | Current quotation hai? | 1 / 0 |

**Kab insert hota hai?**
1. **Pehli baar vendor submit karta hai:**
   - Naya record banta hai
   - `IS_CURRENT = 1`
   - `QR_VERSION` = Current request version

2. **Vendor apna quotation revise karta hai:**
   - Purana record ka `IS_CURRENT = 0` ho jata hai
   - Naya record insert hota hai with `IS_CURRENT = 1`

**Example Flow:**
```
Vendor: Bilal (ID: 11)
Request: 101 (Version 2)

Time 1: Pehli baar submit
├─ VQ_ID: 201
├─ Price: 80,000
├─ IS_CURRENT: 1

Time 2: Price revise ki (Verbal negotiation)
├─ VQ_ID: 201, IS_CURRENT: 0 (Old)
└─ VQ_ID: 202, Price: 75,000, IS_CURRENT: 1 (New)
```

---

### 5️⃣ **SP_VENDOR_QUOTATIONS_HISTORY** (Vendor Quotation History)
```
Purpose: Vendor ke har quotation submission ki complete history
```

| Column | Description | Example |
|--------|-------------|---------|
| `VQH_ID` | History ID | 701 |
| `VQ_ID` | Original quotation ID | 201 |
| `VENDOR_ID` | Vendor ID | 11 |
| `QUOTATION_REQUEST_ID` | Request ID | 101 |
| `QR_VERSION` | Request version | 2 |
| `VERSION_NUMBER` | Vendor's quotation version | 1, 2, 3... |
| `IS_CURRENT` | Current hai? | 1 / 0 |
| `QUANTITY_QUOTED` | Quantity | 10 |
| `PRICE` | Price | 85000 |
| `CURRENCY` | Currency | PKR |
| `DELIVERY_TIME` | Delivery | "10 days" |
| `WARRANTY` | Warranty | "1 year" |
| `SPECIFICATIONS` | Specs | "i7, 16GB RAM" |
| `REMARKS` | Remarks | "Best price" |
| `CREATED_AT` | Kab submit ki | 2025-11-04 12:00:00 |

**Kab insert hota hai?**
- Jab bhi vendor quotation submit karta hai
- Har submission ki entry banti hai
- Purani entries `IS_CURRENT = 0` ho jaati hain

**Example:**
```
Vendor: Bilal, Request: 101

Version 1: Price 80,000, IS_CURRENT = 0
Version 2: Price 75,000, IS_CURRENT = 0
Version 3: Price 70,000, IS_CURRENT = 1  ← Latest
```

---

## 🔄 Complete Flow

### 🎬 Scenario: "Dell Laptop" ki quotation mangwani hai

---

### **Step 1: Quotation Request Create Karna**

**Kahan se:** Budget List Page → Item select → "Request Quotation"

**Database Changes:**

1. `SP_QUOTATION_REQUESTS` mein insert:
```sql
INSERT INTO SP_QUOTATION_REQUESTS (
    QR_ID = 101,
    ITEM_ID = 45,
    ITEM_NAME = "Dell Laptop",
    REQUESTED_QUANTITY = 10,
    CURRENT_VERSION = 1,
    IS_REVISED = 0
)
```

2. `SP_QUOTATION_REQUEST_HISTORY` mein insert:
```sql
INSERT INTO SP_QUOTATION_REQUEST_HISTORY (
    QRH_ID = 501,
    QR_ID = 101,
    VERSION_NUMBER = 1,
    IS_CURRENT = 1,
    REQUESTED_QUANTITY = 10,
    ITEM_NAME = "Dell Laptop"
)
```

**Code Location:**
```php
File: BudgetController.php
Function: requestQuotation()
Lines: 450-520 (approx)
```

---

### **Step 2: Vendors Ko Email Bhejna**

**Kahan se:** Quotation Request Submit Page → "Email Vendors" Tab

**User Action:**
1. Vendor select karte hain (Bilal, Vendor2, Vendor3)
2. "Send Email" click karte hain

**Database Changes:**

3 vendors ko email gayi, to 3 entries:
```sql
INSERT INTO SP_VENDOR_EMAIL_LOG (
    VE_QR_ID = 101,
    VE_QR_VERSION = 1,  ← Current version track hota hai
    VE_VENDOR_ID = 11,  ← Bilal
    VE_SENT_AT = '2025-11-04 11:00:00'
)

INSERT INTO SP_VENDOR_EMAIL_LOG (
    VE_VENDOR_ID = 12,  ← Vendor2
    ...
)

INSERT INTO SP_VENDOR_EMAIL_LOG (
    VE_VENDOR_ID = 13,  ← Vendor3
    ...
)
```

**Code Location:**
```php
File: VendorQuotationController.php
Function: sendMailToVendors()
Lines: 206-258
```

---

### **Step 3: Vendor Quotation Submit Karta Hai**

**Kahan se:** Vendor ka Excel file upload karke submit

**Vendor: Bilal**
- Price: 80,000 PKR
- Delivery: 10 days
- Warranty: 1 year

**Database Changes:**

1. `SP_VENDOR_QUOTATIONS` mein insert:
```sql
INSERT INTO SP_VENDOR_QUOTATIONS (
    VQ_ID = 201,
    VENDOR_ID = 11,
    QUOTATION_REQUEST_ID = 101,
    QR_VERSION = 1,
    QUANTITY_QUOTED = 10,
    PRICE = 80000,
    CURRENCY = 'PKR',
    DELIVERY_TIME = '10 days',
    WARRANTY = '1 year',
    IS_CURRENT = 1
)
```

2. `SP_VENDOR_QUOTATIONS_HISTORY` mein insert:
```sql
INSERT INTO SP_VENDOR_QUOTATIONS_HISTORY (
    VQH_ID = 701,
    VQ_ID = 201,
    VENDOR_ID = 11,
    QUOTATION_REQUEST_ID = 101,
    QR_VERSION = 1,
    VERSION_NUMBER = 1,  ← Vendor ka pehla version
    IS_CURRENT = 1,
    PRICE = 80000,
    ...
)
```

**Code Location:**
```php
File: VendorQuotationController.php
Function: store()
Lines: 318-457
```

**Key Logic:**
```php
// Step 1: Check if vendor already submitted
$existingQuotation = VendorQuotation::where('quotation_request_id', $qrId)
    ->where('vendor_id', $vendorId)
    ->first();

if ($existingQuotation) {
    // Agar purana hai to usko IS_CURRENT = 0 kar do
    $existingQuotation->is_current = 0;
    $existingQuotation->save();
}

// Step 2: Naya quotation banao
$newQuotation = VendorQuotation::create([...]);

// Step 3: History mein save karo
DB::table('SP_VENDOR_QUOTATIONS_HISTORY')->insert([
    'VERSION_NUMBER' => $newVersionNumber,
    'IS_CURRENT' => 1,
    ...
]);
```

---

### **Step 4: Quotation Request Revise Karna**

**Scenario:** Quantity 10 se 15 karni hai

**Kahan se:** Budget List → "Revise" button

**Database Changes:**

1. `SP_QUOTATION_REQUESTS` UPDATE:
```sql
UPDATE SP_QUOTATION_REQUESTS SET
    REQUESTED_QUANTITY = 15,
    CURRENT_VERSION = 2,  ← Version increment
    IS_REVISED = 1
WHERE QR_ID = 101
```

2. `SP_QUOTATION_REQUEST_HISTORY` mein purani entries update:
```sql
UPDATE SP_QUOTATION_REQUEST_HISTORY SET
    IS_CURRENT = 0
WHERE QR_ID = 101 AND IS_CURRENT = 1
```

3. Naya version insert:
```sql
INSERT INTO SP_QUOTATION_REQUEST_HISTORY (
    QR_ID = 101,
    VERSION_NUMBER = 2,  ← New version
    IS_CURRENT = 1,
    REQUESTED_QUANTITY = 15,  ← New quantity
    REMARKS = 'Quantity increased'
)
```

**Code Location:**
```php
File: BudgetController.php
Function: editQuotation()
Lines: 673-739
```

**Key Logic:**
```php
// Step 1: Main request ka version increment karo
$quotationRequest->current_version = ($currentVersion ?? 0) + 1;
$quotationRequest->is_revised = 1;
$quotationRequest->save();

// Step 2: Purane history ka IS_CURRENT = 0 karo
DB::table('SP_QUOTATION_REQUEST_HISTORY')
    ->where('qr_id', $qrId)
    ->where('IS_CURRENT', 1)
    ->update(['IS_CURRENT' => 0]);

// Step 3: Naya history entry banao
DB::table('SP_QUOTATION_REQUEST_HISTORY')->insert([
    'VERSION_NUMBER' => $newVersion,
    'IS_CURRENT' => 1,
    ...
]);
```

---

### **Step 5: Dobara Vendors Ko Email (After Revise)**

**Database Changes:**

Naye version (2) ke sath email entries:
```sql
INSERT INTO SP_VENDOR_EMAIL_LOG (
    VE_QR_ID = 101,
    VE_QR_VERSION = 2,  ← New version!
    VE_VENDOR_ID = 11,
    VE_SENT_AT = '2025-11-04 14:00:00'
)
```

Ab email log mein:
- Version 1 ke against 3 emails
- Version 2 ke against 3 emails (dobara)

---

### **Step 6: Vendor Apna Quotation Revise Karta Hai**

**Scenario:** Bilal ne price 80,000 se 75,000 kar di

**Database Changes:**

1. `SP_VENDOR_QUOTATIONS` mein purani entry update:
```sql
UPDATE SP_VENDOR_QUOTATIONS SET
    IS_CURRENT = 0
WHERE VQ_ID = 201
```

2. Nayi entry insert:
```sql
INSERT INTO SP_VENDOR_QUOTATIONS (
    VQ_ID = 202,
    VENDOR_ID = 11,
    PRICE = 75000,  ← New price
    QR_VERSION = 2,  ← Against new request version
    IS_CURRENT = 1
)
```

3. History mein purani entries update:
```sql
UPDATE SP_VENDOR_QUOTATIONS_HISTORY SET
    IS_CURRENT = 0
WHERE VENDOR_ID = 11 AND QUOTATION_REQUEST_ID = 101
```

4. Naya history version:
```sql
INSERT INTO SP_VENDOR_QUOTATIONS_HISTORY (
    VENDOR_ID = 11,
    VERSION_NUMBER = 2,  ← Vendor's 2nd version
    PRICE = 75000,
    IS_CURRENT = 1
)
```

---

### **Step 7: Status Dekhna (Budget List mein)**

**Kahan se:** Budget List → Item ke saamne "View" button

**Kya dikhta hai:**

#### 📧 **Vendors Emailed:**
```
• Bilal (bilal@example.com) - Version 2 - 2025-11-04 14:00:00
• Bilal (bilal@example.com) - Version 1 - 2025-11-04 11:00:00
• Vendor2 (v2@email.com) - Version 2 - 2025-11-04 14:00:00
• Vendor2 (v2@email.com) - Version 1 - 2025-11-04 11:00:00
```

#### ✅ **Vendors Responded:**
```
🔵 Bilal [2 Versions] ▼
    Version 2 (Current) - ₨ 75,000 - 2025-11-04 15:00:00
    Version 1 - ₨ 80,000 - 2025-11-04 12:00:00

🔵 Vendor2 [1 Version] ▼
    Version 1 (Current) - ₨ 78,000 - 2025-11-04 13:00:00
```

**Code Location:**
```php
File: VendorQuotationController.php
Function: getItemStatus()
Lines: 563-656
```

**Key Logic:**
```php
// Email history fetch karo
$emailHistory = VendorEmailLog::where('ve_qr_id', $qrId)
    ->with('vendor')
    ->orderBy('ve_sent_at', 'desc')
    ->get();

// Vendor quotations fetch karo with ALL versions
$historyVersions = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
    ->where('VENDOR_ID', $vendorId)
    ->where('QUOTATION_REQUEST_ID', $qrId)
    ->orderBy('VERSION_NUMBER', 'desc')
    ->get();
```

---

## 💻 Code Explanation (Kids Level)

### 🎯 **Key Concept: IS_CURRENT Flag**

Samjho ek diary hai jisme har din likha hai:
```
Day 1: Today I studied 2 hours (IS_CURRENT = 1) ← Latest
Day 2: Today I studied 3 hours (Old entry, IS_CURRENT = 0)
```

Database mein bhi aisa hi:
- **IS_CURRENT = 1** means "Ye latest hai, iski value use karo"
- **IS_CURRENT = 0** means "Ye purana hai, history ke liye hai"

---

### 📊 **Example 1: Vendor Ki History Dekhna**

**Problem:** Bilal ne kitni baar quotation submit ki?

**Solution:**
```php
// Step 1: History table se saari entries nikalo
$allVersions = DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
    ->where('VENDOR_ID', 11)  // Bilal ki ID
    ->where('QUOTATION_REQUEST_ID', 101)
    ->orderBy('VERSION_NUMBER', 'desc')  // Latest pehle
    ->get();

// Result:
// Version 3: Price 70,000 (IS_CURRENT = 1)
// Version 2: Price 75,000 (IS_CURRENT = 0)
// Version 1: Price 80,000 (IS_CURRENT = 0)
```

**Frontend mein kaise dikhaye:**
```javascript
versions.forEach(function(version) {
    // Agar IS_CURRENT = 1 hai to green badge
    let badge = version.IS_CURRENT == 1 
        ? '<span class="badge badge-success">Current</span>'
        : '<span class="badge badge-secondary">Old</span>';
    
    // HTML banao
    html += `Version ${version.VERSION_NUMBER} ${badge}`;
});
```

---

### 📊 **Example 2: Unique Vendors Nikalna**

**Problem:** Duplicate vendors show na hon

**Wrong Way:**
```php
// Ye sab quotations le aayega, duplicates bhi
$quotations = VendorQuotation::where('quotation_request_id', 101)->get();
// Result: Bilal, Bilal, Bilal (3 baar!)
```

**Right Way:**
```php
// Step 1: Sirf current quotations lo
$quotations = VendorQuotation::where('quotation_request_id', 101)
    ->where('is_current', 1)  // Sirf latest
    ->get();

// Step 2: Vendor ID se group karo (safety)
$uniqueVendors = $quotations->groupBy('vendor_id')
    ->map(function($group) {
        return $group->first();  // Har vendor ka pehla (latest)
    });
```

---

### 📊 **Example 3: Version Increment Karna**

**Problem:** Naya version number kaise nikale?

**Solution:**
```php
// Step 1: Highest version number nikalo
$lastVersion = DB::table('SP_QUOTATION_REQUEST_HISTORY')
    ->where('QR_ID', 101)
    ->max('VERSION_NUMBER');  // 2

// Step 2: 1 add karo
$newVersion = ($lastVersion ?? 0) + 1;  // 3

// Agar koi version nahi tha (null), to 0 + 1 = 1
// Agar version 2 tha, to 2 + 1 = 3
```

---

### 📊 **Example 4: Purane Versions Ko IS_CURRENT = 0 Karna**

```php
// Step 1: Saare purane versions ka IS_CURRENT = 0 karo
DB::table('SP_VENDOR_QUOTATIONS_HISTORY')
    ->where('VENDOR_ID', 11)
    ->where('QUOTATION_REQUEST_ID', 101)
    ->where('IS_CURRENT', 1)  // Jo current hain
    ->update(['IS_CURRENT' => 0]);  // Unko 0 karo

// Step 2: Ab naya version 1 ke sath insert karo
DB::table('SP_VENDOR_QUOTATIONS_HISTORY')->insert([
    'VERSION_NUMBER' => 3,
    'IS_CURRENT' => 1,  // Ye latest hai
    ...
]);
```

---

## 🎯 Real World Examples

### **Example 1: Budget Item "Laptops" Ki Complete Journey**

```
DAY 1: Request Create
├─ QR_ID: 101
├─ Item: "Dell Laptops"
├─ Quantity: 10
├─ Version: 1
└─ History Entry: Version 1 (IS_CURRENT = 1)

DAY 2: Email to Vendors
├─ Bilal ko email (VE_QR_VERSION = 1)
├─ Vendor2 ko email (VE_QR_VERSION = 1)
└─ Vendor3 ko email (VE_QR_VERSION = 1)

DAY 3: Bilal Submits
├─ Price: 80,000
├─ VQ_ID: 201 (IS_CURRENT = 1)
└─ History: Version 1 (IS_CURRENT = 1)

DAY 4: Vendor2 Submits
├─ Price: 78,000
├─ VQ_ID: 202 (IS_CURRENT = 1)
└─ History: Version 1 (IS_CURRENT = 1)

DAY 5: Quantity Change (10 → 15)
├─ Request Version: 1 → 2
├─ Old History: Version 1 (IS_CURRENT = 0)
└─ New History: Version 2 (IS_CURRENT = 1)

DAY 6: Dobara Email (Version 2)
├─ Bilal ko email (VE_QR_VERSION = 2)
└─ Vendor2 ko email (VE_QR_VERSION = 2)

DAY 7: Bilal Price Revise Karta Hai
├─ Old: VQ_ID 201 (IS_CURRENT = 0)
├─ New: VQ_ID 203, Price: 75,000 (IS_CURRENT = 1)
├─ Old History: Version 1 (IS_CURRENT = 0)
└─ New History: Version 2 (IS_CURRENT = 1)

FINAL STATUS:
📧 Emails Sent:
    • Bilal - Version 2
    • Bilal - Version 1
    • Vendor2 - Version 2
    • Vendor2 - Version 1

✅ Quotations Received:
    • Bilal: 2 versions (Current: 75,000)
    • Vendor2: 1 version (Current: 78,000)
```

---

## 🔍 Important Queries

### Query 1: Item Ki Current Status
```sql
SELECT 
    qr.ITEM_NAME,
    qr.CURRENT_VERSION,
    qrh.REQUESTED_QUANTITY
FROM SP_QUOTATION_REQUESTS qr
JOIN SP_QUOTATION_REQUEST_HISTORY qrh 
    ON qr.QR_ID = qrh.QR_ID 
    AND qrh.IS_CURRENT = 1
WHERE qr.QR_ID = 101;
```

### Query 2: Kis Vendor Ko Kitni Emails
```sql
SELECT 
    v.VENDOR_NAME,
    COUNT(*) as TOTAL_EMAILS
FROM SP_VENDOR_EMAIL_LOG vel
JOIN SP_VENDORS v ON vel.VE_VENDOR_ID = v.VENDOR_ID
WHERE vel.VE_QR_ID = 101
GROUP BY v.VENDOR_NAME;
```

### Query 3: Vendor Ki Saari Versions
```sql
SELECT 
    VERSION_NUMBER,
    PRICE,
    IS_CURRENT,
    CREATED_AT
FROM SP_VENDOR_QUOTATIONS_HISTORY
WHERE VENDOR_ID = 11 
  AND QUOTATION_REQUEST_ID = 101
ORDER BY VERSION_NUMBER DESC;
```

---

## ✅ Testing Checklist

### Test Case 1: New Request
- [ ] Request create hoti hai
- [ ] Version 1 banta hai
- [ ] History mein entry aati hai
- [ ] IS_CURRENT = 1 hai

### Test Case 2: Email Send
- [ ] Email log mein entry aati hai
- [ ] Correct version number save hota hai
- [ ] Har vendor ke liye alag entry

### Test Case 3: Vendor Submit
- [ ] Quotation table mein entry
- [ ] History table mein entry
- [ ] IS_CURRENT = 1
- [ ] VERSION_NUMBER = 1

### Test Case 4: Vendor Revise
- [ ] Purani entry IS_CURRENT = 0
- [ ] Nayi entry IS_CURRENT = 1
- [ ] VERSION_NUMBER increment

### Test Case 5: Request Revise
- [ ] CURRENT_VERSION increment
- [ ] Purani history IS_CURRENT = 0
- [ ] Nayi history IS_CURRENT = 1

### Test Case 6: Status View
- [ ] Email list sahi dikhti hai
- [ ] Vendor list unique hai
- [ ] Saare versions dikhte hain
- [ ] Current version highlight hai

---

## 🚨 Common Issues & Solutions

### Issue 1: Duplicate Vendors in List
**Cause:** `IS_CURRENT` check nahi kar rahe

**Solution:**
```php
->where('is_current', 1)  // Add this
```

### Issue 2: Wrong Version Number
**Cause:** Max version sahi nahi nikala

**Solution:**
```php
$lastVersion = DB::table('...')
    ->where('VENDOR_ID', $vendorId)  // Vendor specific
    ->where('QUOTATION_REQUEST_ID', $qrId)  // Request specific
    ->max('VERSION_NUMBER');
```

### Issue 3: Old Versions Still Showing as Current
**Cause:** Update karna bhool gaye

**Solution:**
```php
// Pehle saare purane ko 0 karo
->update(['IS_CURRENT' => 0]);

// Phir naya insert karo
->insert(['IS_CURRENT' => 1]);
```

---

## 📝 Summary

**5 Main Tables:**
1. `SP_QUOTATION_REQUESTS` - Main request
2. `SP_QUOTATION_REQUEST_HISTORY` - Request ki history
3. `SP_VENDOR_EMAIL_LOG` - Email tracking
4. `SP_VENDOR_QUOTATIONS` - Vendor responses
5. `SP_VENDOR_QUOTATIONS_HISTORY` - Vendor response history

**Key Concept:**
- `IS_CURRENT = 1` → Latest/Active
- `IS_CURRENT = 0` → Old/History
- `VERSION_NUMBER` → Sequence tracking

**Main Files:**
- `BudgetController.php` - Request CRUD
- `VendorQuotationController.php` - Email & Submissions

**Flow:**
Request → Email → Submit → Revise → History

---

**Written by:** AI Assistant
**Date:** November 2025
**Purpose:** Junior developers ke liye complete guide

