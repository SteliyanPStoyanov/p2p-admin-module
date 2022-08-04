@if(session('fail'))
    {{session('fail')}}
@endif
<div class="row" id="container-row">
    <div class="col-lg-12">
        <div id="main-table" class="card">
            <div class="card-body">
                <div class="row">
                    @foreach($commands as $key => $value)
                        <div class="col">
                            <h3><strong>{{ __('common.Command') }}</strong>: {{ $key }}</h3>
                            <p><label for="{{$key}}"><strong>OUTPUT:</strong></label></p>
                            <textarea cols="30" id="{{$key}}" rows="10" disabled> {{ $value }}</textarea>
                            <br>
                            <br>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
