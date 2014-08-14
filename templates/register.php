<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Control My Budget, Please!</title>

    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '1446089528999129',
                xfbml      : true,
                version    : 'v2.0'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>

    <script>
        var checkLoginState = function() {
            FB.getLoginStatus(function(response) {
                if (response.status == 'connected') {
                    FB.api('/me', function(user) {
                        create_user.name.value = user.first_name + ' ' + user.last_name
                        create_user.email.value = user.email;
                        create_user.facebook_user_id.value = user.id;

                        create_user.submit();
                    });
                }
            });
        }
    </script>
</head>
<body>
<h1>Willkommem zu Control My Budget</h1>
<h2>Ja, Ich spreche Deutsch weil Ich kann</h2>

<form action="" name="create_user" method="post">
    <ul>
        <li>
            <label for="facebook">Create an account using facebook Login</label>
            <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
            </fb:login-button>

            <input type="hidden" name="name" />
            <input type="hidden" name="email" />
            <input type="hidden" name="facebook_user_id" />
        </li>
    </ul>
</form>

</body>
</html>