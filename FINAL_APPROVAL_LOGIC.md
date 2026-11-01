# FINAL APPROVAL SLABS LOGIC - UPDATED

## Overview
Har request ke liye 2 types ke slabs hote hain:
1. **1st Slot (School/Dept Specific)** - Lower range (typically 0-35,000)
2. **Common Slots (For Everyone)** - Higher ranges (35K+, 200K+, 500K+)

---

## Database Structure Example

```sql
sp_approval_masters:

-- 1st Slot: School/Dept Specific (0-35K)
ID | school_id | dept_id | min_price | max_price | approver_id | Description
1  | 5         | NULL    | 0         | 35000     | 6001        | School HOD
2  | NULL      | 101     | 0         | 35000     | 5001        | Dept HOD

-- 2nd Slot: Common (35K-200K)
3  | NULL      | NULL    | 35001     | 200000    | 7001        | Dean

-- 3rd Slot: Common (200K-500K)
4  | NULL      | NULL    | 200001    | 500000    | 7002        | Director

-- 4th Slot: Common (500K+)
5  | NULL      | NULL    | 500001    | NULL      | 7003        | Vice Chancellor
```

---

## Logic Flow

### Step 1: Get FIRST Slot (School/Dept Specific)

**Priority Order:**
1. School first (if exists)
2. Then Department (if school not found)

```php
// Try School ID first
WHERE school_id = 5 
AND school_id IS NOT NULL
AND min_price <= total_price
AND max_price >= total_price

// If empty, try Dept ID
WHERE dept_id = 101
AND dept_id IS NOT NULL  
AND min_price <= total_price
AND max_price >= total_price
```

### Step 2: Get ALL Common Slots

```php
// Get all common slabs that cover this price
WHERE dept_id IS NULL
AND school_id IS NULL
AND min_price <= total_price
AND max_price >= total_price
```

**Note:** Ye sab common slabs ko lega jo price range ko cover karte hain, regardless of specific slot.

### Step 3: Merge Both

```php
$allSlabs = $specificSlabs->merge($commonSlabs);
```

---

## Example Scenarios

### Scenario 1: Amount = 20,000 (Only 1st slot)

**Database:**
- Slot 1: School 5, 0-35K → User 6001
- Slot 2: Common, 35K-200K → User 7001

**Query Results:**
- **Specific:** School slot (0-35K) ✓ MATCH
- **Common:** 35K-200K slot ✗ NO MATCH (min_price 35,001 > 20,000)

**Final Approvals:**
```
1. User 6001 (School HOD) - Slot 1
```

---

### Scenario 2: Amount = 50,000 (1st + 2nd slot)

**Database:**
- Slot 1: School 5, 0-35K → User 6001
- Slot 2: Common, 35K-200K → User 7001
- Slot 3: Common, 200K-500K → User 7002

**Query Results:**
- **Specific:** School slot (0-35K) ✓ MATCH
- **Common:** 
  - 35K-200K ✓ MATCH (min=35,001 <= 50,000, max=200,000 >= 50,000)
  - 200K-500K ✗ NO MATCH (min=200,001 > 50,000)

**Final Approvals:**
```
1. User 6001 (School HOD) - Slot 1
2. User 7001 (Dean) - Slot 2
```

---

### Scenario 3: Amount = 350,000 (1st + 2nd + 3rd slot)

**Database:**
- Slot 1: School 5, 0-35K → User 6001
- Slot 2: Common, 35K-200K → User 7001
- Slot 3: Common, 200K-500K → User 7002
- Slot 4: Common, 500K+ → User 7003

**Query Results:**
- **Specific:** School slot (0-35K) ✓ MATCH
- **Common:**
  - 35K-200K ✓ MATCH (min=35,001 <= 350,000, max=200,000 >= 350,000? NO!)
    Actually: max=200,000 < 350,000 ✗ NO MATCH
  - 200K-500K ✓ MATCH (min=200,001 <= 350,000, max=500,000 >= 350,000)
  - 500K+ ✗ NO MATCH (min=500,001 > 350,000)

**Final Approvals:**
```
1. User 6001 (School HOD) - Slot 1
2. User 7002 (Director) - Slot 3
```

**Wait! Slot 2 is skipped?**

Yes! Because the query checks:
- `max_price >= total_price` OR `max_price IS NULL`

For 35K-200K slot:
- max_price = 200,000
- total_price = 350,000
- 200,000 >= 350,000? ✗ FALSE

So it won't match!

---

### Scenario 4: Amount = 750,000 (Multiple high slots)

**Query Results:**
- **Specific:** School slot (0-35K) ✓ MATCH
- **Common:**
  - 35K-200K ✗ (200K < 750K)
  - 200K-500K ✗ (500K < 750K)
  - 500K+ ✓ MATCH (max is NULL, so matches any)

**Final Approvals:**
```
1. User 6001 (School HOD) - Slot 1
2. User 7003 (Vice Chancellor) - Slot 4
```

---

## Key Points

### 1. Query Logic
```php
WHERE min_price <= total_price    // Slab starts before or at price
AND (
    max_price >= total_price      // Slab ends after or at price
    OR max_price IS NULL          // Or unlimited
)
```

### 2. Priority
- School first, then Dept for specific slot
- All matching common slabs

### 3. Matching Criteria
A slab matches if it **covers** the total price:
- Starts at or before the price (min_price <= total_price)
- Ends at or after the price (max_price >= total_price OR NULL)

### 4. Result
Multiple approvals based on price:
- **Low price (< 35K):** Only 1st slot (School/Dept)
- **Medium price (35K-200K):** 1st + 2nd slot
- **High price (200K-500K):** 1st + 3rd slot (2nd skipped!)
- **Very high (500K+):** 1st + 4th slot

---

## Important Notes

⚠️ **Intermediate slots can be skipped!**

If amount is 350K:
- 1st slot (0-35K) ✓
- 2nd slot (35K-200K) ✗ Skipped
- 3rd slot (200K-500K) ✓

This is because each slot must **fully cover** the amount.

✅ **This ensures:**
- Right approvers for the price range
- No unnecessary approvals
- Correct hierarchy based on amount


