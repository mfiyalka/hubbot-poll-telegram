<b>{{ $poll->title }}</b>

@foreach(unserialize($poll->questions) as $key => $item)
{{ $key+1 }}. {{ $item }}
@endforeach
