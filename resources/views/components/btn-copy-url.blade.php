<button type="button" id="{{ $url  }}" class="btn btn-success button-style">{{ $btnName }}</button>
@push('scripts')
    <script>
        let copyUrlBtn = $("#{{ $url }}");

        copyUrlBtn.on('click', (event) => {
            event.preventDefault();

            $.get("{{ $urlGetData }}", (response) => {
                let defaultText = copyUrlBtn.text();
                copyUrlBtn.text("{{ __('btn.Copied') }}");

                let href = $(location).attr('href');
                href = href.split('?')[0];
                let url = href + '?' + $.param(response);

                let temp = $("<input />");
                $("body").append(temp);
                temp.val(url).select();
                document.execCommand("copy", false, temp.val());
                temp.remove();
                setTimeout(() => {
                    copyUrlBtn.text(defaultText);
                }, 1000);
            });
        });
    </script>
@endpush
