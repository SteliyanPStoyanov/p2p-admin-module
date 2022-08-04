@foreach($investors as $investor)
    <tr>
        <td class="text-center">
            <a href="{{route('admin.investors.overview', $investor->investor_id)}}">{{ $investor->investor_id }}</a>
        </td>
        <td class="text-center">{{$investor->email}}</td>
        <td class="text-center">{{ showDate($investor->created_at) ?? ''}}</td>
        <td class="text-center">{{ $investor->first_name }} {{ $investor->middle_name }} {{ $investor->last_name }} </td>
        <td class="text-center">{{ $investor->status }}</td>
        <td class="text-center">{{ $investor->type }}</td>
        <td class="text-center">{{ config('common.currencySimbol.'.$investor->currency_id). ' '.$investor->total_amount }}</td>
        <td class="text-center">{{ config('common.currencySimbol.'.$investor->currency_id). ' '.$investor->uninvested }}</td>
    </tr>
@endforeach


<tr id="pagination-nav">
    <td colspan="10">
        {{ $investors->links() }}
    </td>
</tr>

