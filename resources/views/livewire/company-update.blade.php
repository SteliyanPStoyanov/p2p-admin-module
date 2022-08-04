<div>
    <style>
        .text-error {
            color: #CB603B;
            font-size: 0.8rem;
        }

        button {
            border: none;
            background: none;
            margin-top: -2px;
            float: left;
        }

        .alert.alert-success {
            color: #009c95;
            background: none;
            border: none;
            padding: 0;
            font-size: 0.8rem;
        }
    </style>
    <h3 class="card-header pt-4 pl-4"><b>{{__('common.CompanyDetails')}}</b></h3>
    <form wire:submit.prevent="submit">
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            <div class="card-text w-100 mb-3 float-left">
                @include('livewire.company-component.input-name')
            </div>
            <div class="card-text w-100 mb-3 float-left">
                @include('livewire.company-component.input-number')
            </div>
            <div class="card-text w-100 mb-3 float-left">
                @include('livewire.company-component.input-address')
            </div>
            <div class="card-text w-100 mb-3 float-left">
                @include('livewire.company-component.select-country')
            </div>
        </div>
    </form>
    <script>
        document.addEventListener('company-update', event => {
            window.setTimeout(function () {
                Livewire.emit('companyUpdate');
            @this.field
                = '';
            }, 2000);

        })
    </script>
</div>
