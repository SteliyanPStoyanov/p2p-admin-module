<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.Command')}}</th>
        <th scope="col">{{__('common.Message')}}</th>
        <th scope="col">{{__('common.Total')}}</th>
        <th scope="col">{{__('common.Handled')}}</th>
        <th scope="col">{{__('common.Attempt')}}</th>
        <th scope="col">{{__('common.LastExecTime')}}</th>
        <th scope="col">{{__('common.TotalExecTime')}}</th>
        <th scope="col">{{__('common.ExecutedAt')}}</th>
    </tr>
    </thead>
    <tbody id="cronLogTable">
    @foreach($cronLogs as $log)
        <tr>
            <td>{{$log->cron_log_id}}</td>
            <td>{{$log->command}}</td>
            <td>{{$log->message}}</td>
            <td>{{$log->total}}</td>
            <td>{{$log->imported}}</td>
            <td>{{$log->attempt}}</td>
            <td>{{$log->last_exec_time}} sec</td>
            <td>{{$log->total_exec_time}} sec</td>
            <td>{{$log->created_at->format('Y-m-d H:i')}}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="9">
            {{ $cronLogs->links() }}
        </td>
    </tr>
    </tfoot>
</table>
