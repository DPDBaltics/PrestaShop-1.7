/*
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

function DPDZoneRangesData() {
    this.zoneRanges = [];
}

/**
 * Add new zone range object
 *
 * @param {object} zoneRange
 */
DPDZoneRangesData.prototype.addZoneRange = function (zoneRange)
{
    if (!zoneRange.hasOwnProperty('countryId') ||
        !zoneRange.hasOwnProperty('allRanges') ||
        !zoneRange.hasOwnProperty('zipFrom') ||
        !zoneRange.hasOwnProperty('zipTo') ||
        !zoneRange.hasOwnProperty('id')
    ) {
        return false;
    }

    this.zoneRanges.push(zoneRange);

    return true;
};

/**
 * Update zone range values
 *
 * @param {integer|string} zoneRangeId
 * @param {object} valueObject Object containing updated values
 */
DPDZoneRangesData.prototype.updateZoneRange = function (zoneRangeId, valueObject) {
    var self = this;

    this.zoneRanges.forEach(function (zoneRange, i) {
        if (zoneRange.id === zoneRangeId) {
            for (var property in valueObject) {
                if (zoneRange.hasOwnProperty(property)) {
                    self.zoneRanges[i][property] = valueObject[property];
                }
            }
        }
    });
};

/**
 * Remove zone range
 *
 * @param {integer} zoneRangeId
 */
DPDZoneRangesData.prototype.removeZoneRange = function (zoneRangeId) {
    var self = this;

    this.zoneRanges.forEach(function (zoneRange, i) {
        if (zoneRange.id === zoneRangeId) {
            self.zoneRanges.splice(i, 1);
        }
    });
};
