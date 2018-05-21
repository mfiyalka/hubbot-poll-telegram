<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Start: injected by AdGuard -->
    <script src="//local.adguard.com/adguard-ajax-api/injections/content-script.js?ts=1526622113.705740&amp;sb=0&amp;domain=www.bootstrap-3.ru&amp;mask=103" type="text/javascript" nonce="0913D85FEEBF476D8D613D0E948C52BE" crossorigin="anonymous"></script>
    <script src="//local.adguard.com/adguard-ajax-api/injections/userscripts/Adguard Assistant?ts=1525687627.642333" type="text/javascript" nonce="0913D85FEEBF476D8D613D0E948C52BE" crossorigin="anonymous"></script>
    <!-- End: injected by AdGuard -->
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>PollsterBot</title>

    <!-- Bootstrap core CSS -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- Custom styles for this template -->
    <link href="/css/starter-template.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">PollsterBot</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                @auth
                    <li><a href="https://t.me/hubPollsterBot">Відкрити бота</a></li>
                    <li><a href="{{ url('/polls') }}">Опитування</a></li>
                    <li><a href="{{ url('/logout') }}">Вихід</a></li>
                @else
                    <li><a href="https://t.me/hubPollsterBot">Відкрити бота</a></li>
                @endauth
            </ul>
        </div><!--/.nav-collapse -->
    </div>
    </div>
</div>

<div class="container">

    <div class="starter-template">
        <h1>PollsterBot</h1>
        @auth
            Ви ввійшли як {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
        @else
            <div class="title m-b-md">
                <script async src="https://telegram.org/js/telegram-widget.js?4" data-telegram-login="hubPollsterBot" data-size="large" data-auth-url="auth/telegram/callback" data-request-access="write"></script>
            </div>
        @endauth
    </div>

</div><!-- /.container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</body>
</html>