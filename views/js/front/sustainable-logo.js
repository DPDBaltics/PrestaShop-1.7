$(document).ready(function () {
    let dpdCarriers = $('.custom-radio.float-xs-left > input');
    let dpdCarriersTextBox = $('.carrier');

    dpdCarriers.each(function (index, input) {
        let carrierId = parseInt($(input).attr('value'));
        let isDpdCarrier = dpd_carrier_ids.includes(carrierId)

        if(isDpdCarrier) {
            var sustainableBox = document.createElement("div");
            $(sustainableBox).attr("id", "sustainable-box");

            var sustainableImg = document.createElement("img");
            $(sustainableImg).attr("src", lapinas_img);

            var sustainableText = document.createElement("span");
            sustainableText.innerHTML = lapinas_text;

            sustainableBox.append(sustainableImg);
            sustainableBox.append(sustainableText)

            dpdCarriersTextBox[index].append(sustainableBox)
        }
    })
});
