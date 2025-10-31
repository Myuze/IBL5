# Production Deployment Guide - Character Encoding Fix

## Problem
Characters with accent marks (like "é") display as "�" in production due to incorrect character encoding configuration.

## Changes Made
This PR updates the application to use UTF-8 (utf8mb4) character encoding throughout:

1. **Database Connection Files**: Updated PHP code to set UTF-8 encoding after connecting to MySQL
2. **Database Schema**: Updated `schema.sql` to use `utf8mb4` charset for all tables

## Deployment Steps

### 1. Update Application Code (SAFE - No Data Impact)
Deploy the updated PHP files from this PR:
- `ibl5/classes/MySQL.php`
- `ibl5/db/db.php`
- `ibl5/db/mysql4.php`

These changes are safe and will take effect immediately without requiring database changes.

### 2. Convert Existing Database Tables (REQUIRES PLANNING)

**IMPORTANT**: The schema.sql file shows the target state, but you need to convert existing tables. This requires careful planning:

#### Option A: Automated Conversion (Recommended)
Run this SQL script to convert all existing tables to utf8mb4:

```sql
-- Generate ALTER TABLE statements for all tables with latin1 charset
SELECT CONCAT('ALTER TABLE `', table_name, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;')
FROM information_schema.TABLES
WHERE table_schema = 'your_database_name'
AND table_collation LIKE 'latin1%';
```

This will generate ALTER TABLE statements for each table. Review them, then execute.

#### Option B: Manual Conversion (For Large Databases)
For large production databases, convert tables during a maintenance window:

```sql
-- Example for a single table
ALTER TABLE ibl_hist CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Note**: This operation locks tables during conversion. For large tables, consider:
- Converting tables in batches
- Using pt-online-schema-change tool
- Scheduling during low-traffic periods

### 3. Verify Data After Conversion

After converting tables, verify that existing data displays correctly:

```sql
-- Check for players with accent marks
SELECT name FROM ibl_hist WHERE name LIKE '%é%' OR name LIKE '%ñ%' OR name LIKE '%ü%';
```

If names still show as "Bobby Hurley�" after conversion, the data may be double-encoded. In that case:
1. The original data might already be corrupted in the database
2. You may need to re-import clean data from a backup that has correct UTF-8 encoding

## Testing

1. Deploy the PHP code changes first
2. Test the application - it should work with both latin1 and utf8mb4 tables
3. Convert one small test table to utf8mb4
4. Verify the converted table displays correctly
5. Convert remaining tables in batches

## Rollback Plan

If issues occur:
- PHP code changes can be rolled back via git revert
- Table conversion is harder to rollback - ensure you have backups before converting

## Expected Results

After deployment:
- "Bobby Hurleyé" will display correctly with the accent mark
- All other international characters and accent marks will display properly
- No impact on existing functionality
