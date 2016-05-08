jQuery(function () {


    // Setup the correct class for any error fields
    $(".form-group.has-danger").each(function (index) {
        $(this).find("label").addClass('form-control-label')
        $(this).find("input").addClass('form-control-danger')
    });

    // Add a prefix or suffix to the input
    $("input[prefix], input[suffix]").each(function (index) {
        var prefix = $(this).attr('prefix');
        var suffix = $(this).attr('suffix');

        $(this).wrap(function () {
            return "<div class='input-group'>" + $(this).text() + "</div>"
        });

        if (typeof prefix !== typeof undefined && prefix !== false) {
            $(this).before("<span class='input-group-addon'>" + prefix + "</span>");
        }

        if (typeof suffix !== typeof undefined && suffix !== false) {
            $(this).after("<span class='input-group-addon'>" + suffix + "</span>");
        }
    });

});