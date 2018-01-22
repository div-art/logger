<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Logger</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <p>Filter</p>
                <form method="post" action="{{route('logger')}}">
                    {{ csrf_field() }}
                    <select name="type">
                        <option value=''>check type log</option>
                        <option value='save'>save</option>
                        <option value='info'>info</option>
                        <option value='danger'>danger</option>
                        <option value='success'>success</option>
                        <option value='request'>request</option>
                        <option value='input'>input</option>
                        <option value='json'>json</option>
                        <option value='post'>post</option>
                        <option value='get'>get</option>
                        <option value='php'>php://input</option>
                        <option value='server'>server</option>
                        <option value='cookies'>cookies</option>
                        <option value='headers'>headers</option>
                    </select>
                    
                    <select name="mark">
                        <option value=''>check by mark</option>
                        @foreach($marks as $mark)
                        <option value='{{$mark}}'>{{$mark}}</option>
                        @endforeach
                    </select>

                    <input type="text" name="date" placeholder="2018-01-14">
                    <input type="submit" value="search">
                </form>
                <hr>

                <p>Delete</p>
                <form method="post" action="{{route('deleteAllLogFile')}}">
                    {{ csrf_field() }}
                    <input type="submit" value="delete all log file">
                </form>

                <br>

                <form method="post" action="{{route('logDelete')}}">
                    {{ csrf_field() }}
                    <select name="filename">
                        <option value=''>check delete by file</option>
                        @foreach($logFile as $file)
                            <option value='{{$file}}'>{{$file}}</option>
                        @endforeach
                    </select>

                    <select name="type">
                        <option value=''>check delete by type</option>
                        <option value='save'>save</option>
                        <option value='info'>info</option>
                        <option value='danger'>danger</option>
                        <option value='success'>success</option>
                        <option value='request'>request</option>
                        <option value='input'>input</option>
                        <option value='json'>json</option>
                        <option value='post'>post</option>
                        <option value='get'>get</option>
                        <option value='php'>php://input</option>
                        <option value='server'>server</option>
                        <option value='cookies'>cookies</option>
                        <option value='headers'>headers</option>
                    </select>

                    <input type="text" name="datelog" placeholder="2018-01-14">
                    <input type="submit" value="delete log">
                </form>
                <hr>
                
                <p><center>Logs</center></p>
                @for($i=0; $i < count($data); $i++)
                    <form method="post" action="{{route('deleteOneLog')}}">
                        {{ csrf_field() }}
                        <input type="hidden" name="log" value="{{$data[$i]->time}}">
                        <input type="submit" value="delete this log">
                    </form>
                    <p>time: {{$data[$i]->time}}</p>
                    <p>type: {{$data[$i]->type}}</p>
                    <p>mark: {{$data[$i]->mark}}</p>
                    <p>data:</p>
<pre>
{{print_r($data[$i]->data)}}
</pre>
                @endfor
            </div>
        </div>
    </body>
</html>
