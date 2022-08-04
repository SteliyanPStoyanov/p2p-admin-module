<h2 class="ui large header">
    @foreach ($blogPages as $blogPage)
        <h1><p class="help-text" id="blogTitle">{{$blogPage->title}}</p></h1>
        <div class="content" id="blogContent" content>{{strip_tags($blogPage->content)}}</div>
        <div class="col-sm text-center mb-1">
            <br>
            @foreach($blogPage->files as $blogPageFile)
                <img src="{{ url($blogPageFile->file_path.$blogPageFile->file_name) }}"
                     style="max-width: 100%;
                        display: block;
                        margin: auto;
                        box-shadow: none;
                        border: none;
                        padding: 0px;" alt=""
                     title="">
                <br><br><br>
            @endforeach
        </div>
        <div class="sub header">
            {{Carbon\Carbon::parse($blogPage->date)->format('d F Y')}} <a
                href="/">Afranga</a>
        </div><br>
    @endforeach
</h2>
<div class="ui hidden divider"></div>
<div class="ui hidden divider"></div>
<div class="ui hidden divider"></div>

