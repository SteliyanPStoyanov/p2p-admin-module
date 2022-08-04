<table class="table">
    <thead>
    <tr>

        <th scope="col">{{__('common.DocumentName')}}</th>
        <th scope="col">{{__('common.UploadedBy')}}</th>
        <th scope="col">{{__('common.CreatedAt')}}</th>
        <th scope="col">{{__('common.FileSize')}}</th>
        <th scope="col">{{__('common.Action')}}</th>
    </tr>
    </thead>
    <tbody id="administratorsTable">

    @foreach($allFiles as $loanDoc)
        <tr>
            <td>{{$loanDoc->file_name}}</td>
            <td>{{$loanDoc->getCreateAdmin()}}</td>
            <td>{{$loanDoc->created_at}}</td>
            <td>{{$loanDoc->file_size}} {{__('common.Bytes')}}</td>
            <td>
                <x-btn-delete
                    url="{{ route('admin.delete-loan-document', $loanDoc->file_id) }}"/>

                <x-btn-download
                    url="{{ route('admin.download-loan-document', $loanDoc->file_id) }}"
                />
                <form class="addToAfranga d-inline-block" action="{{ route('admin.new-loans.execute', $loanDoc->file_id) }}" method="POST">
                    {{ admin_csrf_field() }}
                    <button class="btn btn-info"><i class="fa fa-play"></i></button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
