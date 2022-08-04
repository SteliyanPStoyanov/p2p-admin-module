@foreach($loans as $loan)
    <tr>
        <td><a target="_blank" href="{{route('admin.loans.overview', $loan->loan_id)}}"> {{$loan->loan_id}}</a></td>
        <td>{{amount($loan->amount)}}</td>
        <td>{{rate($loan->percent)}}</td>
    </tr>
@endforeach
<tr id="pagination-nav">
    <td colspan="14">
        {{ $loans->onEachSide(1)->links() }}
    </td>
</tr>
