<html>
<head>
    <title>連結到LineNotify</title>
	<meta charset="big-5" />
    <script>
        function oAuth2() {
            var URL = 'https://notify-bot.line.me/oauth/authorize?';
            URL += 'response_type=code';
            URL += '&client_id=yl9TKHqCaSDrqG0d3U1Anq';
            URL += '&redirect_uri=http://dabo.xyz/fios/index.php';
            URL += '&scope=notify';
            URL += '&state=abcde';
            window.location.href = URL;
        }
    </script>
</head>
<body>
    <button onclick="oAuth2();">連結到LineNotify按鈕</button>
</body>
</html>