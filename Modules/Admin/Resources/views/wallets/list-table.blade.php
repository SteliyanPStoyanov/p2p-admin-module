@foreach($wallets as $wallet)
    <tr>
        <td class="text-center">
            <a href="{{route('admin.investors.overview', $wallet->investor_id)}}#wallet">{{ $wallet->wallet_id }}</a>
        </td>
        <td class="text-center">{{ $wallet->created_at != null ? showDate($wallet->created_at, 'H:i') : '' }}</td>
        <td class="text-center">{{ $wallet->investor->fullName() }}</td>
        <td class="text-center">{{ $wallet->investor_id }}</td>
        <td class="text-center">{{ amount($wallet->total_amount) }}</td>
        <td class="text-center">{{ amount($wallet->invested) }}</td>
        <td class="text-center">{{ amount($wallet->uninvested) }}</td>
        <td class="text-center">{{ amount($wallet->blocked_amount) }}</td>
        <td class="tableRow text-center">{{ $wallet->investor->type }}</td>
    </tr>
@endforeach
<tr id="pagination-nav">
    <td colspan="10">
        {{ $wallets->links() }}
    </td>
</tr>
