# DPD API Testing - Curl Examples

## Get Your Credentials

### Method 1: Direct Database Query
```sql
-- Connect to your PrestaShop database
mysql -u USERNAME -p DATABASE_NAME

-- Get credentials
SELECT
    name,
    value
FROM ps_configuration
WHERE name IN (
    'DPD_WEB_SERVICE_USERNAME',
    'DPD_WEB_SERVICE_PASSWORD',
    'DPD_WEB_SERVICE_COUNTRY',
    'DPD_SHIPMENT_TEST_MODE'
);
```

### Method 2: Decode Password in PHP
```bash
# Get the encoded password from database
# Then decode it:
php -r "echo str_rot13('YOUR_ENCODED_PASSWORD_FROM_DB') . PHP_EOL;"
```

### Method 3: PrestaShop Admin Panel
1. Navigate to: **Modules > DPD Baltics > Configure**
2. Go to **Settings** tab
3. Copy values from:
   - Web Service Username
   - Web Service Password
   - Country

---

## API Endpoints

### Test Environment
```
LT: https://lt.integration.dpd.eo.pl/ws-mapper-rest/parcelShopSearch_
LV: https://lv.integration.dpd.eo.pl/ws-mapper-rest/parcelShopSearch_
EE: https://ee.integration.dpd.eo.pl/ws-mapper-rest/parcelShopSearch_
```

### Production Environment
```
LT: https://integracijos.dpd.lt/ws-mapper-rest/parcelShopSearch_
LV: https://integration.dpd.lv/ws-mapper-rest/parcelShopSearch_
EE: https://integration.dpd.ee/ws-mapper-rest/parcelShopSearch_
```

---

## Curl Test Commands

### 1. Full Poland Import (OLD - Will Timeout)
```bash
curl -X POST "https://integracijos.dpd.lt/ws-mapper-rest/parcelShopSearch_" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD",
    "query": {
      "country": "PL",
      "fetchGsPUDOpoint": 1,
      "retrieveOpeningHours": 1
    }
  }' \
  --max-time 30 \
  | jq '.parcelshops | length'
```

**Expected**: Timeout or very slow (30+ seconds)

---

### 2. Full Poland Without Opening Hours
```bash
curl -X POST "https://integracijos.dpd.lt/ws-mapper-rest/parcelShopSearch_" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD",
    "query": {
      "country": "PL",
      "fetchGsPUDOpoint": 1,
      "retrieveOpeningHours": 0
    }
  }' \
  --max-time 30 \
  | jq '{status: .status, count: (.parcelshops | length), time: now}'
```

**Expected**: May still timeout, but faster than with opening hours

---

### 3. Batch Import - Postal Prefix "0" (NEW METHOD)
```bash
curl -X POST "https://integracijos.dpd.lt/ws-mapper-rest/parcelShopSearch_" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD",
    "query": {
      "country": "PL",
      "pcode": "0",
      "fetchGsPUDOpoint": 1,
      "retrieveOpeningHours": 0
    }
  }' \
  --max-time 30 \
  | jq '{status: .status, count: (.parcelshops | length), sample: .parcelshops[0]}'
```

**Expected**: Success in < 20 seconds, returns shops with postal codes 00-xxx to 09-xxx

---

### 4. Test All 10 Batches (Complete Solution)
```bash
#!/bin/bash

USERNAME="YOUR_USERNAME"
PASSWORD="YOUR_PASSWORD"
ENDPOINT="https://integracijos.dpd.lt/ws-mapper-rest/parcelShopSearch_"

echo "Testing Poland import in 10 batches..."
echo ""

total=0
for prefix in {0..9}; do
  echo "Batch $((prefix+1))/10: Postal prefix '$prefix*'"

  response=$(curl -s -X POST "$ENDPOINT" \
    -H "Content-Type: application/json" \
    -d "{
      \"username\": \"$USERNAME\",
      \"password\": \"$PASSWORD\",
      \"query\": {
        \"country\": \"PL\",
        \"pcode\": \"$prefix\",
        \"fetchGsPUDOpoint\": 1,
        \"retrieveOpeningHours\": 0
      }
    }" \
    --max-time 25)

  status=$(echo "$response" | jq -r '.status // "error"')
  count=$(echo "$response" | jq '.parcelshops | length // 0')

  if [ "$status" = "ok" ]; then
    echo "  ✅ Success: $count shops"
    total=$((total + count))
  else
    error=$(echo "$response" | jq -r '.errlog // "Unknown error"')
    echo "  ❌ Failed: $error"
  fi

  echo ""
done

echo "======================================"
echo "Total shops imported: $total"
echo "======================================"
```

**Expected**: All batches succeed, total ~1000-5000+ shops depending on Poland's network

---

## Response Format

### Success Response
```json
{
  "status": "ok",
  "errlog": "",
  "parcelshops": [
    {
      "parcelshop_id": "PL12345",
      "company": "Sample Parcel Shop",
      "country": "PL",
      "city": "Warsaw",
      "pcode": "00-001",
      "street": "Example Street 1",
      "email": "shop@example.pl",
      "phone": "+48123456789",
      "longitude": "21.012229",
      "latitude": "52.229676",
      "openingHours": []  // Empty when retrieveOpeningHours=0
    }
  ]
}
```

### Error Response
```json
{
  "status": "err",
  "errlog": "Error message here",
  "parcelshops": []
}
```

---

## Testing Checklist

- [ ] Get credentials from database
- [ ] Decode password with str_rot13()
- [ ] Choose correct endpoint (test vs production)
- [ ] Test single batch (postal prefix "0")
- [ ] Verify response time < 20 seconds
- [ ] Test all 10 batches
- [ ] Calculate total shop count
- [ ] Compare with/without opening hours

---

## Troubleshooting

### Timeout Errors
```bash
# Check if batch size is too large
# Try more granular filtering (2-digit prefix):
"pcode": "00"  # Instead of "0"
```

### Authentication Errors
```bash
# Verify credentials
curl -X POST "https://integracijos.dpd.lt/ws-mapper-rest/parcelShopSearch_" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "YOUR_USERNAME",
    "password": "YOUR_PASSWORD",
    "query": {
      "country": "LT",
      "fetchGsPUDOpoint": 1,
      "retrieveOpeningHours": 0
    }
  }'

# If Lithuania works but Poland doesn't, it's a data size issue
```

### Empty Response
```bash
# Check if postal prefix exists in Poland
# Try different prefixes: "1", "2", "3", etc.
# Or remove pcode filter to test full country
```

---

## Performance Comparison

| Method | Opening Hours | Filter | Expected Time | Status |
|--------|--------------|--------|---------------|--------|
| Full import | Yes (1) | None | > 30s | ❌ Timeout |
| Full import | No (0) | None | 20-30s | ⚠️ May timeout |
| Batch (10x) | No (0) | Postal prefix | 3-10s each | ✅ Success |

---

## Integration with Module

The module automatically uses batch import for Poland:

```php
// In ParcelShopImport.php
if ($country === 'PL') {
    // Automatically splits into 10 batches
    // Each batch: pcode='0', pcode='1', ... pcode='9'
    // retrieveOpeningHours=0 for all batches
}
```

No configuration needed - it's automatic!
