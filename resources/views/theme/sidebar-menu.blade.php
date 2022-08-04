@php
    $permissions = [
        'administrationGroup' => ['administrators', 'roles', 'settings'],
        'investorGroup' => ['investors', 'investors-referrals'],
        'loanGroup' => ['loans', 'loans.upload', 're-buying-loans', 're-buying-loans.upload'],
        'transactionGroup' => ['transactions'],
        'walletGroup' => ['wallets'],
        'logGroup' => ['cron-logs', 'crons'],
        'communicationGroup' => ['emailTemplate', 'email'],
        'taskGroup' => ['tasks'],
        'userAgreement' => ['user-agreement'],
        'blockedGroup' => ['blocked-ip','login-attempt','registration-attempt','investor-login-log'],
        'investStrategyGroup' => ['invest-strategy'],
        'blogPage'  => ['blog-page'],
        'mongoLogs'  => ['mongo-logs'],
    ];
    $allowed = Auth::user()->resolvePermissions($permissions);
@endphp
<aside class="left-sidebar" data-sidebarbg="skin6">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" data-sidebarbg="skin6">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item ">
                    <a class="sidebar-link sidebar-link" href="{{ route('admin.dashboard') }}"
                       aria-expanded="false"><span class="hide-menu">{{ __('common.Dashboard') }}</span>
                    </a>
                </li>
                <li class="list-divider"></li>

                {{-- Tasks --}}
                @if(!empty($allowed['taskGroup']) && $allowed['taskGroup']['tasks'])
                    <li class="sidebar-item">
                        <a href="{{ route('admin.tasks.list') }}" class="sidebar-link">
                            <span class="hide-menu">{{ __('common.Tasks') }}</span>
                        </a>
                    </li>
                @endif
                {{-- End Tasks --}}

                {{-- Investors --}}
                @if(!empty($allowed['investorGroup']) && $allowed['investorGroup'])
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="hide-menu">{{ __('common.Investors') }}</span>
                        </a>
                        <ul aria-expanded="false" class="collapse  first-level base-level-line">
                            @if($allowed['investorGroup']['investors'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.investors.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.List') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if($allowed['investorGroup']['investors-referrals'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.investors-referrals.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.Referrals') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                {{-- End Investors --}}

                {{-- Loans --}}
                @if(!empty($allowed['loanGroup']) && $allowed['loanGroup'])
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="hide-menu">{{ __('common.Loans') }}</span>
                        </a>
                        <ul aria-expanded="false" class="collapse  first-level base-level-line">
                            @if($allowed['loanGroup']['loans'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.loans.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.List') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if($allowed['loanGroup']['loans.upload'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.loans.upload.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.LoansUpload') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if($allowed['loanGroup']['re-buying-loans'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.re-buying-loans.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.UnlistedLoans') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if($allowed['loanGroup']['re-buying-loans.upload'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.re-buying-loans.upload.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.ImportNewUnlistedLoans') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                {{-- End Loans --}}

                {{-- Transactions --}}
                @if(!empty($allowed['transactionGroup']) && $allowed['transactionGroup']['transactions'])
                    <li class="sidebar-item">
                        <a href="{{ route('admin.transactions.list') }}" class="sidebar-link">
                            <span class="hide-menu">{{ __('common.Transactions') }}</span>
                        </a>
                    </li>
                @endif
                {{-- End Transactions --}}

                {{-- Wallets --}}
                @if(!empty($allowed['walletGroup']) && $allowed['walletGroup']['wallets'])
                    <li class="sidebar-item">
                        <a href="{{ route('admin.wallets.list') }}" class="sidebar-link">
                            <span class="hide-menu">{{ __('common.Wallets') }}</span>
                        </a>
                    </li>
                @endif
                {{-- End wallets --}}

                {{-- Administration --}}
                @if(!empty($allowed['administrationGroup']) && $allowed['administrationGroup'])
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="hide-menu">{{ __('common.Administration') }}</span>
                        </a>
                        <ul aria-expanded="false" class="collapse  first-level base-level-line">
                            @if($allowed['administrationGroup']['administrators'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.administrators.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.Administrators') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if($allowed['administrationGroup']['roles'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.roles.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.Roles & Permissions') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if($allowed['administrationGroup']['settings'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.settings.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.AllSettings') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                {{-- End Administration --}}

                {{-- Communication--}}
                @if(!empty($allowed['communicationGroup']) && $allowed['communicationGroup'])
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="hide-menu">{{ __('common.Communication') }}</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a href="javascript:void(0)" class="sidebar-link has-arrow">
                                    <span class="hide-menu">{{ __('common.Email') }}</span>
                                </a>
                                <ul aria-expanded="false" class="collapse  first-level base-level-line"
                                    style="margin-left: 30px">
                                    @if($allowed['communicationGroup']['emailTemplate'])
                                        <li class="sidebar-item">
                                            <a href="{{ route('admin.emailTemplate.list') }}" class="sidebar-link">
                                                <span class="hide-menu">{{ __('common.emailTemplate') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if($allowed['communicationGroup']['email'])
                                        <li class="sidebar-item">
                                            <a href="{{ route('admin.email.list') }}" class="sidebar-link">
                                                <span class="hide-menu">{{ __('common.emailsSend') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- Communication end--}}


                {{-- Log --}}
                @if(!empty($allowed['logGroup']) && $allowed['logGroup']['cron-logs'])
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="hide-menu">{{ __('common.Crons') }}</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a href="{{ route('admin.crons.list') }}" class="sidebar-link">
                                    <span class="hide-menu">{{ __('common.ManualRun') }}</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('admin.cron-logs.list') }}" class="sidebar-link">
                                    <span class="hide-menu">{{ __('common.Log') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- Log --}}

                {{-- User agreement --}}
                @if(!empty($allowed['userAgreement']) && $allowed['userAgreement']['user-agreement'])
                    <li class="sidebar-item">
                        <a href="{{ route('admin.user-agreement.list') }}" class="sidebar-link">
                            <span class="hide-menu">{{ __('common.UserAgreements') }}</span>
                        </a>
                    </li>
                @endif
                {{-- User agreement --}}

                {{--BlockGroup--}}
                @if(!empty($allowed['blockedGroup']) && $allowed['blockedGroup'])
                    {{--                    <li class="sidebar-item">--}}
                    {{--                        <a href="{{ route('admin.blocked.list') }}" class="sidebar-link">--}}
                    {{--                            <span class="hide-menu">{{ __('common.Blocked') }}</span>--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="hide-menu">{{ __('common.Blocked') }}</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            {{-- Blocked IPs --}}
                            @if($allowed['blockedGroup']['blocked-ip'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.blocked-ip.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.BlockedIps') }}</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Login Attempt --}}
                            @if($allowed['blockedGroup'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.login-attempt.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.LoginAttempts') }}</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Login Attempt --}}

                            {{-- Registration Attempt --}}
                            @if($allowed['blockedGroup'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.registration-attempt.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.RegistrationAttempts') }}</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Registration Attempt --}}

                            {{-- Investor Login List --}}
                            @if($allowed['blockedGroup'])
                                <li class="sidebar-item">
                                    <a href="{{ route('admin.investor-login-log.list') }}" class="sidebar-link">
                                        <span class="hide-menu">{{ __('common.InvestorLoginLog') }}</span>
                                    </a>
                                </li>
                            @endif
                            {{-- Investor Login List --}}
                        </ul>
                    </li>
                @endif
                {{--BlockGroup--}}

                {{-- Log --}}
                @if(!empty($allowed['investStrategyGroup']) && $allowed['investStrategyGroup']['invest-strategy'])
                    <li class="sidebar-item">
                        <a href="{{ route('admin.invest-strategy.list') }}" class="sidebar-link">
                            <span class="hide-menu">{{ __('common.AutoInvestStategy') }}</span>
                        </a>
                    </li>
                @endif
                {{-- Log --}}

                {{-- Blog Page --}}
                @if(!empty($allowed['blogPage']) && $allowed['blogPage']['blog-page'])
                    <li class="sidebar-item">
                        <a href="{{ route('admin.blog-page.list') }}" class="sidebar-link">
                            <span class="hide-menu">{{ __('common.BlogPage') }}</span>
                        </a>
                    </li>
                @endif
                {{-- Blog Page --}}

                {{-- Mongo logs --}}
                @if(!empty($allowed['mongoLogs']) && $allowed['mongoLogs']['mongo-logs'])
                    <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                            <span class="hide-menu">{{ __('common.MongoLogs') }}</span>
                        </a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item">
                                <a href="{{ route('admin.mongo-logs.list', 'investor_logger') }}" class="sidebar-link">
                                    <span class="hide-menu">{{ __('common.InvestorLogs') }}</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('admin.mongo-logs.list', 'system_logger') }}" class="sidebar-link">
                                    <span class="hide-menu">{{ __('common.SystemLogs') }}</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('admin.mongo-logs.list', 'pivot_logger') }}" class="sidebar-link">
                                    <span class="hide-menu">{{ __('common.RelationLogs') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                @endif
                {{-- Mongo logs --}}

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
