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
                    url="{{ route('admin.re-buying-loans.delete', $loanDoc->file_id) }}"/>

                <x-btn-download
                    url="{{ route('admin.re-buying-loans.download', $loanDoc->file_id) }}"
                />
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
