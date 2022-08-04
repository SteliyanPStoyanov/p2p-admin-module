<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.Table')}}</th>
        <th scope="col">{{__('common.OldState')}}</th>
        <th scope="col">{{__('common.NewState')}}</th>
        <th scope="col">{{__('common.Changes')}}</th>
        <th scope="col">{{__('common.Action')}}</th>
        <th scope="col">{{__('common.LoanId')}}</th>
        <th scope="col">{{__('common.InvestorId')}}</th>
        <th scope="col">{{__('common.CreatedAt')}}</th>
        <th scope="col">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-all">
                {{ __('common.DeleteAll') }} <span><i aria-hidden="true" class="fa fa-trash"></i></span>
            </button>                                <!-- Modal -->
            <div class="modal fade" id="delete-all" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            Delete
                        </div>
                        <div class="modal-body">
                            Are you sure want to proceed?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <form action="{{ route('admin.mongo-logs.delete', [$adapterKey, 0]) }}" method="POST">
                                {{ admin_csrf_field() }}
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </th>
    </tr>
    </thead>
    @php $id = 0; @endphp
    <tbody id="cronLogTable">
    @foreach($mongoLogs as $mongoLog)
        @php
            $prevState = $mongoLog->object_prev_state;
            $curState = $mongoLog->object_cur_state;
            $changes = $mongoLog->changes;

        @endphp
        <tr>
            <td>{{ $mongoLog->_id }}</td>
            <td>{{ $mongoLog->table }}</td>
            <td>
                @if(!empty($prevState))
                    <x-json-modal id="{{ $id++ }}" buttonLabel="JSON" :items="$prevState"/>
                @endif
            </td>
            <td>
                @if(!empty($curState))
                    <x-json-modal id="{{ $id++ }}" buttonLabel="JSON" :items="$curState"/>
                @endif
            </td>
            <td>
                @if(!empty($changes))
                    <x-json-modal id="{{ $id++ }}" buttonLabel="JSON" :items="$changes"/>
                @endif
            </td>
            <td>{{ $mongoLog->action }}</td>
            <td><a href="{{ $mongoLog->loan_id ? route('admin.loans.overview', $mongoLog->loan_id) : "#" }}">{{ $mongoLog->loan_id }}</a></td>
            <td><a href="{{ $mongoLog->investor_id ? route('admin.investors.overview', $mongoLog->investor_id) : "#"}}">{{ $mongoLog->investor_id }}</a></td>
            <td>{{ $mongoLog->created_at }}</td>
            <td>

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-{{$mongoLog->_id}}">
                    <span><i aria-hidden="true" class="fa fa-trash"></i></span>
                </button>

                <!-- Modal -->
                <div class="modal fade" id="delete-{{$mongoLog->_id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                Delete
                            </div>
                            <div class="modal-body">
                                Are you sure want to proceed?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <form action="{{ route('admin.mongo-logs.delete', [$adapterKey, $mongoLog->_id]) }}" method="POST">
                                    {{ admin_csrf_field() }}
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td>
            {{ $mongoLogs->links() }}
        </td>
    </tr>
    </tfoot>
</table>
