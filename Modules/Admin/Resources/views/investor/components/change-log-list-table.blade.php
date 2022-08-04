@foreach($investorChangeLogs as $investorChangeLog)
    <tr>
        <td>{{ $investorChangeLog->key }}</td>
        <td>
            <div data-toggle="tooltip" data-placement="top" title=""
                 data-original-title="{{$investorChangeLog->old_value}}">
                {{ \Illuminate\Support\Str::limit($investorChangeLog->old_value, 40, '...') }}
            </div>
        </td>
        <td>
            <div data-toggle="tooltip" data-placement="top" title=""
                 data-original-title="{{$investorChangeLog->new_value}}">
                {{ \Illuminate\Support\Str::limit($investorChangeLog->new_value, 40, '...') }}
            </div>
        </td>
        <td>{{ $investorChangeLog->created_at }}</td>
        <td>{{ $investorChangeLog->getCreatorNames() . '(' . $investorChangeLog->created_by_type . ')' }}</td>
    </tr>
@endforeach
<tr id="pagination-nav">
    <td colspan="10">
        {{ $investorChangeLogs->links() }}
    </td>
</tr>

