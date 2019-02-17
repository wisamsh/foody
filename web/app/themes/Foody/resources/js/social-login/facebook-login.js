/**
 * Created by moveosoftware on 9/29/18.
 */


let $facebookLoginButton = $('.facebook-btn');

if ($facebookLoginButton.length) {

    facebookLogin();
}

function facebookLogin() {
    FB.login(function (response) {
        if (response.status === 'connected') {
            // Logged into foody and Facebook.
        } else {

        }
    }, {scope: 'public_profile,email'});
}