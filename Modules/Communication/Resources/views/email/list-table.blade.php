<table class="table">
    <thead>
    <tr>
        <th class="w-25" scope="col">{{__('common.Name')}}</th>
        <th scope="col">{{__('common.Investor')}}</th>
        <th scope="col">{{__('common.Identifier')}}</th>
        <th scope="col">{{__('common.Sender')}}</th>
        <th scope="col">{{__('common.Response')}}</th>
        <th scope="col">{{__('common.Queue')}}</th>
        <th scope="col">{{__('common.QueueAt')}}</th>
        <th scope="col">{{__('common.Tries')}}</th>
        <th scope="col">{{__('common.SendAt')}}</th>
        <th style="display: none;" scope="col">{{__('common.Actions')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($emails as $email)
        <tr>
            <td>{{ $email->title }}</td>
            <td>{{ $email->investor->first_name}}</td>
            <td>{{ $email->identifier }}</td>
            <td>{{ $email->sender_from }}</td>
            <td>{{ $email->response }}</td>
            <td>{{ $email->queue }}</td>
            <td>{{ $email->queued_at }}</td>
            <td>{{ $email->tries }}</td>
            <td>{{ $email->send_at }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="13">
            {{ $emails->links() }}
        </td>
    </tr>
    </tfoot>
</table>
