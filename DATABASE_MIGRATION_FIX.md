# 🔧 Database Migration Fix - Smart Bags

## Problem

The existing `bag_items` table uses `travel_bag_id` (from the old system), but Smart Bags needs `bag_id`.

## Solution

A migration has been created to fix this: `2026_01_01_121238_update_bag_items_table_for_smart_bags.php`

---

## What the Migration Does

1. **Renames Column:** `travel_bag_id` → `bag_id`
2. **Adds New Columns:**
   - `name` - Item name
   - `weight` - Weight in kg
   - `category` - Item category
   - `essential` - Is it essential?
   - `packed` - Is it packed?
   - `notes` - Notes
   - `quantity` - Quantity
   - `deleted_at` - Soft delete

---

## How to Run

### Step 1: Check Current Database

```bash
# Check if bag_items table exists
php artisan tinker
>>> Schema::hasTable('bag_items');
>>> exit
```

### Step 2: Run Migration

```bash
php artisan migrate
```

**Expected Output:**
```
Migrating: 2026_01_01_121238_update_bag_items_table_for_smart_bags

   INFO  Migration successful!
```

### Step 3: Verify

```bash
php artisan tinker
```

```php
// Check table structure
>>> Schema::getColumnListing('bag_items');
// Should show: id, bag_id, name, weight, category, essential, packed, notes, quantity, created_at, updated_at, deleted_at

// Check if bag_id exists
>>> Schema::hasColumn('bag_items', 'bag_id');
// Should return: true

// Check if travel_bag_id is gone
>>> Schema::hasColumn('bag_items', 'travel_bag_id');
// Should return: false

>>> exit
```

---

## Alternative: Fresh Migration

If you don't have important data, you can start fresh:

```bash
# Drop old migrations and start fresh
php artisan migrate:fresh

# Or drop specific table
php artisan tinker
>>> Schema::dropIfExists('bag_items');
>>> exit

# Then run migrations
php artisan migrate
```

---

## Test After Migration

```bash
# Test API
curl -X GET "http://localhost:8000/api/smart-bags" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Should work without errors!

---

## Rollback (if needed)

```bash
# Rollback last migration
php artisan migrate:rollback --step=1

# This will rename bag_id back to travel_bag_id
# (but won't drop the new columns to preserve data)
```

---

## Summary

✅ Migration file created
✅ Handles column rename
✅ Adds required columns
✅ Safe (checks if columns exist before adding)
✅ Reversible (rollback available)

Just run: `php artisan migrate`

Done! 🎉

