<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PollsterBot</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="top-right links">
                @auth
                    <a href="https://t.me/hubPollsterBot">Open Bot</a>
                    <a href="{{ url('/logout') }}">Logout</a>
                @else
                    <a href="">Open Bot</a>
                @endauth
            </div>
            <div class="content">
                <div class="title m-b-md">
                    PollsterBot
                </div>

                    @auth
                        You are logged in as {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    @else
                        <div class="title m-b-md">
                            <script async src="https://telegram.org/js/telegram-widget.js?4" data-telegram-login="hubPollsterBot" data-size="large" data-auth-url="auth/telegram/callback" data-request-access="write"></script>
                        </div>
                    @endauth

            </div>
        </div>

    </body>
</html>
