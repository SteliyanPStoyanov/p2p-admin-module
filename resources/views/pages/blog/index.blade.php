@extends('pages.layouts.app')

<link rel="stylesheet" href="{{ assets_version(url('/') . '/css/blog-styles.css') }}">

@section('content')

    <div class="ui vertical segment features-container inner-title" id="blog-container">
        <div class="ui vertical segment stackable left aligned grid container">
            <div class="ui grid stackable container" id="blog-content">
                <div class="row" id="article">
                    <div id="list-wraper" class="eleven wide column">
                        @include('pages.blog.list')
                    </div>
                    <div class="four wide right floated column">
                        <h4 class="ui header">Archives</h4>
                        <div class="ui list" id="blogContainerData">
                            @foreach($archives as $archive)
                                <a onclick="return getBlog('{{$archive->month}}','{{$archive->year}}');"
                                   class="item"
                                   id="blogArchive">{{$archive->month}}{{$archive->year}}
                                    ({{$archive->published}})</a>
                            @endforeach
                        </div>
                        <h4 class="ui header">Follow us</h4>
                        <div class="ui list">
                            <a href="https://www.facebook.com/afranga/" class="item">Facebook</a>
                            <a href="https://twitter.com/afranga1" class="item">Twitter</a>
                            <a href="https://www.linkedin.com/company/afranga/" class="item">Linkedin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#blogContainerData a').click(scrollToTopAnimation);

        function scrollToTopAnimation() {
            if (window.outerWidth < 768) {
                $("body, html").animate({scrollTop: $("#header-container").scrollTop()}, 150);
            }
        }

        function getBlog(month, year) {
            let routeGetBlogPages = '{{ route('blog-page.ajax-blog-pages')}}';

            $.ajax({
                url: routeGetBlogPages,
                method: 'GET',
                data: {month: month, year: year},
                success: function (data) {
                    $('#list-wraper').html(data);
                }
            })
            return false;
        }
    </script>
@endpush
