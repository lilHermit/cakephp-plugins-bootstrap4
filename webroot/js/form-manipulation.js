jQuery(function () {


    // Setup the correct class for any error fields
    $(".form-group.has-danger").each(function (index) {
        $(this).find("input").addClass('form-control-danger')
    });
});