/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
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
