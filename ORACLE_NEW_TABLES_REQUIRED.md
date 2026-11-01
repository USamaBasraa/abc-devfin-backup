# Kya New Table Banani Hai? (Short Answer)

## Direct Answer

Agar aap `.env` me `QUEUE_CONNECTION=database` use kar rahe ho, to **haan** 2 infrastructure tables ka hona zaroori hai:

1. `jobs`
2. `failed_jobs`

Business side pe (vendor email feature ke liye) **koi new custom table mandatory nahi**,
kyun ke hum already `SP_VENDOR_EMAIL_LOG` use kar rahe hain.

---

## Script Kahan Hai?

Oracle SQL script yahan rakhi hai:

- `database_scripts/oracle_queue_tables.sql`

Is script me yeh create hota hai:

1. `jobs` table + indexes
2. `failed_jobs` table + unique index on `uuid`

---

## Kab run karna hai?

Run tab karo jab:

- `QUEUE_CONNECTION=database` ho
- aur DB me `jobs` / `failed_jobs` already exist na karti hon

---

## Quick verify SQL

```sql
SELECT table_name
FROM user_tables
WHERE table_name IN ('JOBS', 'FAILED_JOBS');
```

Agar output me dono aa jayein to setup complete.

---

## Important Note

Agar aap `QUEUE_CONNECTION=sync` use karte ho to in tables ki zarurat nahi hoti,
lekin production/stable flow ke liye queue worker + database queue better hai.
