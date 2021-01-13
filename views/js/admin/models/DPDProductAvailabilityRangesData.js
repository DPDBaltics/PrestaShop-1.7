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

function DPDProductAvailabilityData() {
    this.productAvailability = [];
}

/**
 * Add new zone range object
 *
 * @param {object} availabilityRange
 */
DPDProductAvailabilityData.prototype.addAvailabilityRange = function (availabilityRange)
{
    if (!availabilityRange.hasOwnProperty('day') ||
        !availabilityRange.hasOwnProperty('from') ||
        !availabilityRange.hasOwnProperty('to') ||
        !availabilityRange.hasOwnProperty('id')
    ) {
        return false;
    }

    this.productAvailability.push(availabilityRange);

    return true;
};

/**
 * Update zone range values
 *
 * @param {integer|string} availabilityRangeId
 * @param {object} valueObject Object containing updated values
 */
DPDProductAvailabilityData.prototype.updateProductAvailabilityRange = function (availabilityRangeId, valueObject) {
    var self = this;

    this.productAvailability.forEach(function (availabilityRange, i) {
        if (availabilityRange.id === availabilityRangeId) {
            for (var property in valueObject) {
                if (availabilityRange.hasOwnProperty(property)) {
                    self.productAvailability[i][property] = valueObject[property];
                }
            }
        }
    });
};

/**
 * Remove zone range
 *
 * @param availabilityRangeId
 */
DPDProductAvailabilityData.prototype.removeAvailabilityRange = function (availabilityRangeId) {
    var self = this;

    this.productAvailability.forEach(function (availabilityRange, i) {
        if (availabilityRange.id === availabilityRangeId) {
            self.productAvailability.splice(i, 1);
        }
    });
};
