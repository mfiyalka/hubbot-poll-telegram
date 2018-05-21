Ваші останні 10 опитувань:

@foreach($polls as $key => $poll)
<b>{{ $poll->title }}</b>
/questions_p{{ $poll->id }} - показувати питання
/results_p{{ $poll->id }} - показати результати
~~~~~~~~~~~~~~~~~~~~~~~~~
@endforeach
