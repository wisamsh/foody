/**
 * Created by moveosoftware on 10/7/18.
 */

window.foodyAjax = function (settings, cb) {

    let data = {
        action: settings.action
    };

    data = _.extend(data, settings.data);

    let url = foodyGlobals.ajax;
    if(settings.query){
        url = `${url}${settings.query}`
    }
    $.ajax({
        url: foodyGlobals.ajax,
        data: data,
        type: 'POST',
        beforeSend: function (xhr) {

        },
        success: function (data) {
            cb(null, data);
        },
        error: function (err) {
            cb(err);
        }
    });

};