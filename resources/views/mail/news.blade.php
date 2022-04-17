<div class="container" style="text-align: center">
    <h1>{{$news->title}}</h1>
    <p>{{$news->content}}</p>
    <p><small>Для того, чтобы отписаться от нашей рассылки - перейдите по адресу: {{route('news.unsub')}}</small></p>
</div>
