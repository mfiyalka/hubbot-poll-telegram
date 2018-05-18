Your last 10 polls:

@foreach($polls as $key => $poll)
<b>{{ $poll->title }}</b>
/questions_p{{ $poll->id }} - show questions
/results_p{{ $poll->id }} - show results
~~~~~~~~~~~~~~~~~~~~~~~~~
@endforeach
