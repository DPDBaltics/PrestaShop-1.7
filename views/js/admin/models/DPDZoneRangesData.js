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
