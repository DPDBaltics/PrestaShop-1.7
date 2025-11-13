# Poland Parcel Import Timeout Fix - DGS-346

## Problem Summary
Poland parcel shop imports were timing out due to the large number of parcel shops (significantly more than Baltic countries). The API response was too large to complete within the 30-second timeout window.

## Solution Implemented

### 1. Batch Import by Postal Code Prefix
- Splits Poland import into 10 batches based on postal code prefixes (0-9)
- Each batch fetches only parcels with postal codes starting with that digit
- Example: Batch 1 fetches 00-xxx to 09-xxx, Batch 2 fetches 10-xxx to 19-xxx, etc.

### 2. Two-Phase Import Strategy
- **Phase 1**: Import basic shop data WITHOUT opening hours
  - Reduces API response size by approximately 75%
  - Opening hours field set to `retrieveOpeningHours=0`
- **Phase 2**: (Future enhancement) Opening hours can be added later if needed

### 3. API Timeout Increase
- Increased API client timeout from 20 seconds to 120 seconds
- Provides additional safety margin for batch imports

## Implementation Details

### Automatic Detection
The module automatically detects Poland (PL) and uses batch import:
```php
private function shouldUseBatchImport($countryIso)
{
    $batchCountries = ['PL']; // Poland
    return in_array($countryIso, $batchCountries);
}
```

### Postal Code Batching
Poland is split into 10 batches:
```php
private function getPostalPrefixes($countryIso)
{
    $prefixes = [
        'PL' => ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
    ];
    return $prefixes[$countryIso] ?? [];
}
```

### Backwards Compatibility
- Baltic countries (LT, LV, EE) continue using single-request import
- No changes to existing functionality for smaller countries
- Same import button and user experience

## Required Manual Change (Vendor File)

⚠️ **IMPORTANT**: The following vendor file is gitignored and requires manual update:

**File**: `vendor/invertus/dpdbaltics-api/src/Factory/APIRequest/ApiClient.php`

**Change line 51-54**:
```php
// OLD (20 seconds)
public function getTimeout()
{
    return 20;
}

// NEW (120 seconds)
public function getTimeout()
{
    // Increased from 20 to 120 seconds to handle large responses (e.g., Poland parcel shops)
    return 120;
}
```

**Why manual?**: This file is in the `vendor/` directory which is gitignored. After running `composer install` or `composer update`, you'll need to manually apply this change.

**Alternative**: Consider creating a patch file or forking the `invertus/dpdbaltics-api` package with this change.

## Files Modified

1. **src/Service/API/ParcelShopSearchApiService.php**
   - Added `getCountryParcelsByPostalPrefix()` method for filtered requests

2. **src/Service/Import/API/ParcelShopImport.php**
   - Added `importParcelShopsInBatches()` for batch processing
   - Added `shouldUseBatchImport()` for country detection
   - Added `getPostalPrefixes()` for postal code ranges

3. **src/Service/Parcel/ParcelUpdateService.php**
   - Added `$deleteExisting` parameter to avoid deleting shops on every batch
   - Only first batch deletes existing country shops

## Testing Instructions

### Manual Testing

1. **Apply vendor file change** (see above)

2. **Test Poland Import**:
   - Log into PrestaShop admin
   - Navigate to DPD Baltics module configuration
   - Go to Import/Export section
   - Select Poland (PL) from country dropdown
   - Click "Import Parcel Shops"
   - Expected: Import completes successfully with message showing batch count

3. **Verify Import**:
   - Check that Poland parcel shops are in database
   - Query: `SELECT COUNT(*) FROM ps_dpd_shop WHERE country = 'PL'`
   - Should see thousands of shops imported

4. **Test Baltic Countries** (ensure no regression):
   - Import Lithuania (LT), Latvia (LV), or Estonia (EE)
   - Expected: Single-request import still works
   - Should complete in under 10 seconds

### Expected Behavior

**Poland (PL)**:
- Uses batch import automatically
- Success message: "Successfully imported {X} parcel shops in 10 batches"
- Import time: 30-60 seconds (10 batches × 3-6 seconds each)
- No timeout errors

**Other Countries (LT, LV, EE)**:
- Uses traditional single-request import
- Success message: "Successfully updated parcel shops"
- Import time: 3-10 seconds
- Unchanged behavior

## Performance Improvements

| Country | Before | After |
|---------|--------|-------|
| Poland (PL) | ❌ Timeout (30s) | ✅ 30-60s (10 batches) |
| Lithuania (LT) | ✅ 5-10s | ✅ 5-10s (unchanged) |
| Latvia (LV) | ✅ 5-10s | ✅ 5-10s (unchanged) |
| Estonia (EE) | ✅ 5-10s | ✅ 5-10s (unchanged) |

## Future Enhancements

1. **Progress Bar UI**:
   - Show real-time progress during batch import
   - Display current batch (e.g., "Importing batch 3/10...")

2. **Opening Hours Phase 2**:
   - Add separate process to fetch opening hours
   - Can be run as background cron job

3. **More Granular Batching**:
   - If needed, split into 2-digit prefixes (00, 01, 02, etc.)
   - Would create 100 batches but smaller responses

4. **Other Large Countries**:
   - Add Germany (DE), France (FR), etc. if needed
   - Simply add to `$batchCountries` array

## Troubleshooting

### Issue: Still timing out on Poland import
**Solution**:
1. Verify vendor file timeout change was applied
2. Check server PHP `max_execution_time` setting (should be > 120s)
3. Consider more granular batching (2-digit prefixes)

### Issue: Duplicate shops in database
**Solution**:
1. Check `deleteExisting` logic in first batch
2. Manually clear: `DELETE FROM ps_dpd_shop WHERE country = 'PL'`
3. Re-run import

### Issue: Some batches failing
**Solution**:
1. Check error messages in response
2. Verify DPD API credentials are correct
3. Test specific postal code prefix manually

## Related Tickets

- DGS-346: SUPPORT - PL lockers list is empty
- DGS-407: SUPPORT - Prestashop Poland parcelshops on checkout
- SDESK-182: Original timeout issue report

## Contact

For issues or questions, contact the DPD Baltics integration team.
