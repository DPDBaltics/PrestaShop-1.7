#!/bin/bash

# DPD Baltics Poland Parcel Import Test Script
# Tests batch import functionality with postal code filtering

# INSTRUCTIONS:
# 1. Get your credentials from PrestaShop admin -> DPD Baltics module settings
# 2. Update the USERNAME and PASSWORD variables below
# 3. Choose TEST_MODE (true for test API, false for production)
# 4. Run: bash test_poland_batch_import.sh

# ===== CONFIGURATION =====
# TODO: Replace with your actual credentials from database
USERNAME="YOUR_USERNAME_HERE"
PASSWORD="YOUR_PASSWORD_HERE"
TEST_MODE=true  # true = test API, false = production API
COUNTRY="LT"    # Your configured country (LT, LV, or EE)

# ===== API ENDPOINTS =====
if [ "$TEST_MODE" = true ]; then
    case $COUNTRY in
        "LT") BASE_URL="https://lt.integration.dpd.eo.pl" ;;
        "LV") BASE_URL="https://lv.integration.dpd.eo.pl" ;;
        "EE") BASE_URL="https://ee.integration.dpd.eo.pl" ;;
        *) echo "Invalid country: $COUNTRY"; exit 1 ;;
    esac
else
    case $COUNTRY in
        "LT") BASE_URL="https://integracijos.dpd.lt" ;;
        "LV") BASE_URL="https://integration.dpd.lv" ;;
        "EE") BASE_URL="https://integration.dpd.ee" ;;
        *) echo "Invalid country: $COUNTRY"; exit 1 ;;
    esac
fi

API_ENDPOINT="${BASE_URL}/ws-mapper-rest/parcelShopSearch_"

# ===== HELPER FUNCTIONS =====
function test_api_call() {
    local prefix="$1"
    local retrieve_hours="$2"
    local description="$3"

    echo ""
    echo "========================================="
    echo "$description"
    echo "========================================="
    echo "Postal prefix: ${prefix:-ALL}"
    echo "Opening hours: $retrieve_hours"
    echo "Endpoint: $API_ENDPOINT"
    echo ""

    # Build JSON request
    local json_request="{\"username\":\"$USERNAME\",\"password\":\"$PASSWORD\""

    # Add query parameters
    json_request="${json_request},\"query\":{"
    json_request="${json_request}\"country\":\"PL\""
    json_request="${json_request},\"fetchGsPUDOpoint\":1"
    json_request="${json_request},\"retrieveOpeningHours\":$retrieve_hours"

    if [ ! -z "$prefix" ]; then
        json_request="${json_request},\"pcode\":\"$prefix\""
    fi

    json_request="${json_request}}}"

    echo "Request JSON:"
    echo "$json_request" | jq '.' 2>/dev/null || echo "$json_request"
    echo ""

    # Make API call and measure time
    start_time=$(date +%s)

    response=$(curl -s -w "\n%{http_code}\n%{time_total}" -X POST "$API_ENDPOINT" \
        -H "Content-Type: application/json" \
        -d "$json_request" \
        --max-time 25)

    end_time=$(date +%s)
    elapsed=$((end_time - start_time))

    # Parse response
    http_code=$(echo "$response" | tail -2 | head -1)
    time_total=$(echo "$response" | tail -1)
    body=$(echo "$response" | head -n -2)

    echo "HTTP Status: $http_code"
    echo "Time: ${time_total}s"
    echo ""

    # Check if response is JSON
    if echo "$body" | jq empty 2>/dev/null; then
        status=$(echo "$body" | jq -r '.status // "unknown"')
        error_log=$(echo "$body" | jq -r '.errlog // ""')
        parcel_count=$(echo "$body" | jq '.parcelshops | length // 0')

        echo "API Status: $status"

        if [ "$status" = "ok" ]; then
            echo "✅ SUCCESS - Fetched $parcel_count parcel shops"

            # Show sample parcel shop
            if [ "$parcel_count" -gt 0 ]; then
                echo ""
                echo "Sample parcel shop:"
                echo "$body" | jq '.parcelshops[0] | {parcelshop_id, company, city, pcode, street}' 2>/dev/null
            fi
        else
            echo "❌ ERROR - $error_log"
        fi
    else
        echo "Response (first 500 chars):"
        echo "$body" | head -c 500
    fi

    echo ""

    # Return shop count for summary
    echo "$parcel_count"
}

# ===== VALIDATION =====
if [ "$USERNAME" = "YOUR_USERNAME_HERE" ] || [ "$PASSWORD" = "YOUR_PASSWORD_HERE" ]; then
    echo "❌ ERROR: Please update USERNAME and PASSWORD in the script"
    echo ""
    echo "To get your credentials:"
    echo "1. Access PrestaShop database"
    echo "2. Run: SELECT * FROM ps_configuration WHERE name IN ('DPD_WEB_SERVICE_USERNAME', 'DPD_WEB_SERVICE_PASSWORD', 'DPD_WEB_SERVICE_COUNTRY');"
    echo "3. Password is stored with str_rot13() encoding, decode it"
    echo ""
    exit 1
fi

# Check if jq is installed
if ! command -v jq &> /dev/null; then
    echo "⚠️  Warning: 'jq' is not installed. JSON output will not be formatted."
    echo "Install with: brew install jq"
    echo ""
fi

# ===== MAIN TEST =====
echo "╔════════════════════════════════════════════════════════════╗"
echo "║   DPD Baltics Poland Parcel Import - Batch Test           ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "Configuration:"
echo "  Base URL: $BASE_URL"
echo "  Country: $COUNTRY"
echo "  Mode: $([ "$TEST_MODE" = true ] && echo "TEST" || echo "PRODUCTION")"
echo ""

# Test 1: Full Poland import (OLD METHOD - will likely timeout)
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "TEST 1: Full Poland Import (Old Method)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
full_count=$(test_api_call "" 1 "Fetching ALL Poland parcels WITH opening hours")

# Test 2: Full Poland without opening hours
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "TEST 2: Full Poland Import Without Opening Hours"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
full_no_hours=$(test_api_call "" 0 "Fetching ALL Poland parcels WITHOUT opening hours")

# Test 3: Batch imports (NEW METHOD)
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "TEST 3: Batch Import by Postal Code Prefix (New Method)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

total_shops=0
prefixes=("0" "1" "2")  # Test with first 3 prefixes only

for prefix in "${prefixes[@]}"; do
    count=$(test_api_call "$prefix" 0 "Batch $((prefix+1))/10: Postal prefix '$prefix*'")
    total_shops=$((total_shops + count))
done

# ===== SUMMARY =====
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║                      TEST SUMMARY                          ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "Test 1 (Full import WITH hours):    $full_count shops"
echo "Test 2 (Full import WITHOUT hours): $full_no_hours shops"
echo "Test 3 (Batch import, 3 batches):   $total_shops shops"
echo ""
echo "Expected behavior:"
echo "  ✅ Test 1: Likely TIMEOUT (response too large)"
echo "  ✅ Test 2: May succeed or timeout (large response)"
echo "  ✅ Test 3: Should succeed (small batches)"
echo ""
echo "Recommendation:"
echo "  - Use batch import (Test 3) with retrieveOpeningHours=0"
echo "  - Split Poland into 10 batches (postal prefixes 0-9)"
echo "  - Each batch should complete within 20 seconds"
echo ""

# ===== DATABASE QUERY HELPER =====
echo "╔════════════════════════════════════════════════════════════╗"
echo "║          How to Get Credentials from Database             ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "Option 1: MySQL Command Line"
echo "------------------------------------------------------------"
echo "mysql -u root -p prestashop << EOF"
echo "SELECT
    (SELECT value FROM ps_configuration WHERE name = 'DPD_WEB_SERVICE_USERNAME') as username,
    (SELECT value FROM ps_configuration WHERE name = 'DPD_WEB_SERVICE_PASSWORD') as password_encoded,
    (SELECT value FROM ps_configuration WHERE name = 'DPD_WEB_SERVICE_COUNTRY') as country;
EOF"
echo ""
echo "Then decode password in PHP:"
echo "  php -r \"echo str_rot13('ENCODED_PASSWORD_FROM_DB') . PHP_EOL;\""
echo ""
echo "Option 2: PrestaShop Admin"
echo "------------------------------------------------------------"
echo "1. Go to Modules -> DPD Baltics -> Configure"
echo "2. Settings tab"
echo "3. Copy Web Service Username and Password"
echo ""
