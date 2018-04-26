<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logs</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
          integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4"
          crossorigin="anonymous">

    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-weight: 100;
            margin: 0;
        }

        .main-table {
            max-width: 1200px;
        }

        .main-table td:nth-child(6) {
            max-width: 400px;
            /*height: 400px;*/
            overflow-x: auto;
            /*overflow-y: scroll;*/
        }

        .main-table tr td:last-child {
            overflow: auto;
            max-width: 600px;
        }

        .main-table tr td:first-child {
            max-width: 350px;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if(count($files) > 0)
                <a href="{{route('all-logs', ['type'=>'all'])}}">All logs</a>
                <h2>Filtering</h2>
                <form method="post" action="{{route('filter-logs')}}">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <select class="form-control col-md-2" name="date">
                            <option value="">Choose date</option>
                            @foreach($dates as $date)
                                <option value="{{$date}}">{{$date}}</option>
                            @endforeach
                        </select>
                        <select class="form-control col-md-2" name="type">
                            <option value="">Choose type</option>
                            @foreach($types as $type)
                                <option value='{{ $type }}'>{{ $type }}</option>
                            @endforeach
                        </select>

                        <select class="form-control col-md-2" name="mark">
                            <option value=''>Choose mark</option>
                            @foreach($marks as $mark)
                                <option value='{{$mark}}'>{{$mark}}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-info" type="submit">Apply</button>
                    </div>
                </form>

                <h2>Deleting</h2>
                <form method="post" action="{{route('delete-log-file-by-param')}}">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <select class="form-control col-md-2" name="date">
                            <option value="">Delete by date</option>
                            @foreach($dates as $date)
                                <option value="{{$date}}">{{$date}}</option>
                            @endforeach
                        </select>
                        <select class="form-control col-md-2" name="type">
                            <option value="">Choose type</option>
                            @foreach($types as $type)
                                <option value='{{ $type }}'>{{ $type }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </div>
                    <br>
                </form>

                <form method="post" action="{{ route('delete-all-log-files') }}">
                    <button title="Delete all log files" class="btn btn-danger" type="submit">Delete all</button>
                </form>

                <br>

                @foreach($files as $file => $logs)
                        <table class="table main-table">
                            <thead>
                                <tr>
                                    <th colspan="6">Date: {{ $file }}</th>
                                    <th>
                                        <form method="post"
                                              action="{{ route('delete-log-file', ['date'=>$file]) }}">
                                            {{ csrf_field() }}
                                            <button title="Delete this file" type="submit" class="btn btn-danger">
                                                Del
                                            </button>
                                        </form>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <th>Mark</th>
                                    <th>Time</th>
                                    <th>File</th>
                                    <th>Line</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            @php($logsCounter = 0)
                            @if( ! is_null($logs))
                                @foreach($logs as $log)
                                    @if($log)
                                        <tr>
                                            @foreach($log as $part)
                                                <td>
                                                    {{ print_r($part, true)}}
                                                </td>
                                            @endforeach
                                            <td>
                                                <form method="post"
                                                      action="{{route('delete-log')}}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="date" value="{{$file}}">
                                                    <input type="hidden" name="type" value="{{$log->type}}">
                                                    <input type="hidden" name="time" value="{{$log->time}}">
                                                    <input type="hidden" name="line" value="{{$log->line}}">
                                                    <input type="hidden" name="file" value="{{$log->file}}">
                                                    <button title="Delete this log" type="submit" class="btn btn-danger">
                                                        Del
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                    @php($logsCounter++)
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        Not found
                                    </td>
                                </tr>
                            @endif
                        </table>
                    <br>
                    <br>
                @endforeach
            @else
                <h1>Logs not found</h1>
            @endif
        </div>
    </div>
</div>

</body>
</html>
