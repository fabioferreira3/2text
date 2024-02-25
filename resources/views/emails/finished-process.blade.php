<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8" />
</head>

<body>
    <div style="background-color: #080B53">
        <img src="https://go.experior.ai/logo.png" style="width:25%" />
    </div>
    @if($content)
    {!!$content!!}
    @else <p>Hey {{$name}}! We are letting you know you're recent <strong>{{$jobName}}</strong> is completed!</p>
    <p>You may check the results in the link below:</p>
    <p>{{$link}}</p>
    <p><br></p>
    <p>Cheers!</p>
    <p>Experior</p>
    @endif
</body>

</html>
