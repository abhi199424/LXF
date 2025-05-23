$(document).ready(function () {
    function loadTargets() {
        var targetSelect = $("select[name='target_ids[]']");
        var type = $("select[name='type']").val();
        var idCustomText = $("input[name='id_custom_text']").val(); // Get id_custom_text

        $.ajax({
            url: prospectAjaxUrl,
            type: "POST",
            data: { action: "getTargets", type: type, id_custom_text: idCustomText },
            dataType: "json",
            success: function (data) {
                targetSelect.empty();

                $.each(data, function (index, item) {
                    var option = $("<option></option>")
                        .attr("value", item.id)
                        .text(item.name);

                    // Check if item should be pre-selected
                    if (item.selected) {
                        option.prop("selected", true);
                    }

                    targetSelect.append(option);
                });
            }
        });
    }

    // Load targets on page load
    loadTargets();

    // Reload targets when the "type" select field changes
    $("select[name='type']").on("change", loadTargets);
});
