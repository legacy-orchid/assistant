<br>

**I collected similar queries for you, is there any solution :question:**
<ul>
@foreach($issues as $issue)
        <li><a href="{{$issue['html_url']}}">{{$issue['title']}}</a> [{{$issue['comments']}}]</li>
@endforeach
</ul>

