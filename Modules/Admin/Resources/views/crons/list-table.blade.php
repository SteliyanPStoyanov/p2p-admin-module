<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Command')}}</th>
        <th scope="col">{{__('common.TotalExecTime')}}</th>
        <th scope="col">{{__('common.Message')}}</th>
        <th scope="col">{{__('common.LastExecutedAt')}}</th>
        <th scope="col">{{__('common.Actions')}}</th>
    </tr>
    </thead>
{{--    //Command | Execution time | Message | Last executed at | Action--}}
    <tbody id="cronTable">
    @foreach($commands as $command)
        <tr>
            <td>{{ $command->getNameForDb() }}</td>
            <td>{{ $command->lastDbRecord() ? $command->lastDbRecord()->total_exec_time : '' }}</td>
            <td>{{ $command->lastDbRecord() ? $command->lastDbRecord()->message : '' }}</td>
            <td>{{ $command->lastDbRecord() ? $command->lastDbRecord()->created_at : '' }}</td>
            <td>
                <form action="{{ route('admin.crons.execute') }}" method="POST">
                    {{ admin_csrf_field() }}

                    @php
                        $btnText = 'common.Run';
                        $class = 'primary';
                        $disabled = '';
                    @endphp
                    @if($command->isInManualExecution())
                        @php
                            $btnText = 'common.Running';
                            $class = 'warning';
                            $disabled = 'disabled';
                        @endphp
                    @endif

                    <button class="btn btn-{{$class}}" {{$disabled}} type="submit" name="command" value="{{$command->getNameForDb()}}" style="width: 120px;">
                        {{__($btnText)}}
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    </tfoot>
</table>
