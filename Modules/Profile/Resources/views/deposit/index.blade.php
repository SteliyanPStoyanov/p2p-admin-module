@extends('profile::layouts.app')

@section('title',  'Deposit - ')

@section('content')
    <div class="col-lg-12 pt-4 pb-4 deposit-buttons">
        <div class="btn ui teal button deposit-btn"
             style="pointer-events: none;cursor: default;">{{__('common.AddFunds')}}</div>
        <a href="{{route('profile.withdraw')}}"
           class="btn ui basic button withdraw-btn" style="letter-spacing: 0">{{__('common.WithdrawFunds')}}</a>

    </div>
    <div class="row">
        <div class="col-lg-5 text-black">
            <h3 class="mb-3 text-black">{{__('common.HowToDepositFunds')}}</h3>
            <p class="text-black">{{__('common.TransferTheSumOf')}}</p>
            <p class="text-black">{{__('common.YouWillReceiveAnEmail')}}</p>
            <p class="text-black">{{__('common.PleaseMakeTransfers')}}</p>
            <h3 class="mt-5 mb-4 text-black">{{__('common.ImportantNotice')}}</h3>
            <p class="text-black">{{__('common.TheTransferMustBeMade')}}</p>
            <p class="text-black">{{__('common.TheBankAccountFromWhich')}}</p>
            <p class="text-black">{{__('common.DoNotForgetToIncludeYour')}}</p>
            <p class="text-black">{{__('common.TheTransactionCanTakeUp')}}</p>
        </div>
        <div class="col-lg-6 trans-details ml-auto text-black">
            <h4 class="mt-5 text-black">{{__('common.TransferDetails')}}</h4>
            <div class="row border-bottom pb-2 pt-3">
                <div class="col-lg-4">
                    {{__('common.Beneficiary')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.AfrangaAD')}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="row border-bottom pb-2 pt-3">
                <div class="col-lg-4">
                    {{__('common.IBAN')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.IBANDetails')}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="row border-bottom pb-2 pt-3">
                <div class="col-lg-4">
                    {{__('common.RegistrationNo')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.RegistrationNoDetails')}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="row border-bottom pb-2 pt-3">
                <div class="col-lg-4">
                    {{__('common.Bank')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.BankDetails')}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="row border-bottom pb-2 pt-3">
                <div class="col-lg-4">
                    {{__('common.BIC/SWIFT')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.BIC/SWIFTDetails')}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="row border-bottom pb-2 pt-3">
                <div class="col-lg-4">
                    {{__('common.RegisteredAddress')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.RegisteredAddressDetails')}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="row border-bottom pb-2 pt-3">
                <div class="col-lg-4">
                    {{__('common.BankAddress')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.BankAddressDetails')}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="row border-bottom pb-2 pt-3 no-border">
                <div class="col-lg-4">
                    {{__('common.PaymentReason')}}
                </div>
                <div class="col-lg-8">
                    {{__('common.PaymentReasonDetails')}} {{$investor->investor_id}}
                    <a class="copy-to-clipboard float-right" href="#" data-toggle="tooltip" data-placement="top"
                       data-original-title="Copied">
                        <svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clipboard"
                             fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                            <path fill-rule="evenodd"
                                  d="M9.5 1h-3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip('dispose');
        });

        $('.copy-to-clipboard').click(function (e) {
            e.preventDefault()
            $('.copy-to-clipboard').removeClass('iscopy');
            let text = $(this).parent();
            var elem = document.createElement("textarea");
            document.body.appendChild(elem);
            elem.value = text.text().trim();
            elem.select();
            document.execCommand("copy");
            document.body.removeChild(elem);
            $(this).addClass('iscopy');
            $(this).parent().find('[data-toggle="tooltip"]').tooltip('show');
            window.setTimeout(function () {
                $('[data-toggle="tooltip"]').tooltip('dispose');
                $('.copy-to-clipboard').removeClass('iscopy');
            }, 500);

        });
    </script>
@endpush
