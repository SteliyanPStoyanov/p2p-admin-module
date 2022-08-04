<div class="scroll-sidebar" style="max-height: 390px">
    <p>
        <a class="btn btn-cyan"
           data-toggle="collapse"
           href="#{{$url}}"
           role="button"
           aria-expanded="false"
           aria-controls="collapseExample">
            {{ $btnName }}
        </a>
    </p>
    <div class="collapse" id="{{ $url }}">
        <div class="form-check">
            @if($showSelectAll === 'true')
                <input type="checkbox" id="selectAll">
                <label for="selectAll"><strong>Select all</strong></label><br/>
            @endif
        </div>
        @stack('colapsing-content' . $url)

    </div>
</div>
@if($showSelectAll === 'true')
    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#selectAll').click(function (event) {
                    if (this.checked) {
                        $('#{{$url}} :checkbox').each(function () {
                            this.checked = true;
                        });
                    } else {
                        $('#{{$url}} :checkbox').each(function () {
                            this.checked = false;
                        });
                    }
                });
            })
        </script>
    @endpush
@endif
