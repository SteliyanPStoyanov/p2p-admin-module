@foreach($investments as $investment)

    <tr>
        <td class="text-center">{{ formatDate($investment->investment_created_at , 'd.m.Y H:i:s')}}</td>
        <td class="text-center"> <a target="_blank" href="{{route('admin.loans.overview', $investment->loan_id)}}"> {{ $investment->loan_id}} </a></td>
        <td class="text-center">
            <a target="_blank"
               href="{{route('admin.loans.overview', $investment->loan_id)
.'#investor-instalments&investor_id='. $investor->investor_id . '&investment_id='.$investment->investment_id}}">
                {{ $investment->investment_id}}
            </a>
        </td>
        <td class="text-center"> {{ showDate($investment->loan_created_at) }} </td>
        <td class="text-center"> {{ $investment->country_name }} </td>
        <td class="text-center"> {{ loanType($investment->type) }} </td>
        <td class="text-center"> {{ $investment->originator_name }} </td>
        <td class="text-center"> {{ amount($investment->loan_remaining_principal) }} </td>
        <td class="text-center"> {{ rate($investment->interest_rate_percent) }} </td>
        <td class="text-center"> {{ termFormat($investment->final_payment_date) }} </td>
        <td class="text-center"> {{ amount($investment->amount) }} </td>
        <td class="text-center"> {{ amount($investment->invested_sum)}} </td>
        <td class="text-center"> {{ ucfirst($investment->status) }} </td>
        <td class="text-center"> {{ ucfirst($investment->payment_status) }} </td>
        <td class="text-center"> {{ $investment->unlisted == 1 ? 'Unlisted' : 'Listed' }} </td>
    </tr>
@endforeach
<tr id="pagination-nav">
    <td colspan="15">
        {{ $investments->links() }}
    </td>
</tr>
