export function _AJAX(data, callback, method = "POST", dataType = "json", error_callback = "") {
    let m = (String(method).toUpperCase() === "POST" || String(method).toUpperCase() === "GET") ? String(method).toUpperCase() : "POST";

    return $.ajax({
        url: "/wp-admin/admin-ajax.php",
        method: m,
        dataType,
        data,
        success: callback,
        error: function (jqXHR, exception) {
            if (!!error_callback.length) {
                error_callback();
            }
            if (jqXHR.status === 404) {
                alert("Requested page not found (404).");
            } else if (jqXHR.status === 500) {
                alert("Internal Server Error (500).");
            } else if (exception === "parsererror") {
                alert("Requested JSON parse failed.");
            } else if (exception === "timeout") {
                alert("Time out error.");
            }

            console.log(data, jqXHR, exception);
        }
    });
}