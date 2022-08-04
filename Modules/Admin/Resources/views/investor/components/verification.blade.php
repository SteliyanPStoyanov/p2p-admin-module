<div class="row">
    <div class="col-5">

        <h3 class="pt-4 pl-4">
            {{__('common.VerificationChecks')}}
        </h3>
        <div class="card-body">
            @php
                $taskType = \Modules\Common\Entities\Task::TASK_TYPE_VERIFICATION;
                $taskStatus = \Modules\Common\Entities\Task::TASK_STATUS_NEW;
                $processingStatus = \Modules\Common\Entities\Task::TASK_STATUS_PROCESSING;
                $disabled = true;
                $task = $investor->verificationTask();
                if ($task && ($task->status == $taskStatus || $task->status == $processingStatus)) {
                    $disabled = false;
                }
            @endphp
            <form class="form-inline card-body p-0" method="POST"
                  action="{{route('admin.tasks.verify', $investor->verificationTask() ? $investor->verificationTask()->task_id : 0)}}#verification">
                {{ admin_csrf_field() }}
                <div class="form-row w-100">

                    <div class="form-group col-lg-12 mb-1">
                        <div class="form-check">
                            <label class="font-weight-bold" style="margin-left: -5px">
                                {{__('common.Yes')}}
                            </label>
                            <label class="ml-3 font-weight-bold">
                                {{__('common.No')}}
                            </label>
                            <label class="ml-3 font-weight-bold">
                                {{__('common.DoesTheInformationMatch')}}
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-lg-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="verify-name" name="name" value="1" class="mr-4"
                                   @if(!empty($investor->verification->name) == 1) checked="" @endif
                                   @if($disabled == true) disabled="" @endif>
                            <input type="checkbox" name="name" value="0" class="mr-2"
                                   @if(!empty($investor->verification) && $investor->verification->name === 0) checked=""
                                   @endif
                                   @if($disabled == true) disabled="" @endif>
                            <label class="pl-2" for="verify-name">
                                {{__('common.VerifyName')}}: {{ $investor->fullName()}}
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-lg-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" id="verify-birthday" name="birth_date" value="1" class="mr-4"
                                   @if(!empty($investor->verification->birth_date) == 1) checked="" @endif
                                   @if($disabled == true) disabled="" @endif>
                            <input type="checkbox" name="birth_date" value="0" class="mr-2"
                                   @if(!empty($investor->verification) && $investor->verification->birth_date  === 0) checked=""
                                   @endif
                                   @if($disabled == true) disabled="" @endif>
                            <label class="pl-2" for="verify-birthday">
                                {{__('common.VerifyBirthDay')}}
                                : {{\Carbon\Carbon::parse($investor->birth_date)->format('d-m-Y') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-lg-12 mb-3">
                        <div class="form-check">

                            <input type="checkbox" id="verify-address" name="address" value="1" class="mr-4"
                                   @if(!empty($investor->verification->address) == 1) checked="" @endif
                                   @if($disabled == true) disabled="" @endif >
                            <input type="checkbox" name="address" value="0" class="mr-2"
                                   @if(!empty($investor->verification) && $investor->verification->address === 0) checked=""
                                   @endif
                                   @if($disabled == true) disabled="" @endif >
                            <label class="pl-2" for="verify-address">
                                {{__('common.VerifyAddress')}}:
                                {{ $investor->country->name ?? ''}}, {{ $investor->city}}, {{ $investor->address}}
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-lg-12 mb-3">
                        <div class="form-check">

                            <input type="checkbox" id="verify-citizenship" name="citizenship" value="1" class="mr-4"
                                   @if(!empty($investor->verification->citizenship) == 1) checked="" @endif
                                   @if($disabled == true) disabled="" @endif >
                            <input type="checkbox" name="citizenship" value="0" class="mr-2"
                                   @if(!empty($investor->verification) && $investor->verification->citizenship === 0) checked=""
                                   @endif
                                   @if($disabled == true) disabled="" @endif>
                            <label class="pl-2" for="verify-citizenship">{{__('common.VerifyCitizenship')}}:
                                {{$investor->investorCitizenship->name ?? ''}}
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-lg-12 mb-3">
                        <div class="form-check">

                            <input type="checkbox" id="verify-photo" name="photo" value="1" class="mr-4"
                                   @if(!empty($investor->verification->photo) == 1) checked="" @endif
                                   @if($disabled == true) disabled="" @endif >
                            <input type="checkbox" name="photo" value="0" class="mr-2"
                                   @if(!empty($investor->verification) && $investor->verification->photo === 0) checked=""
                                   @endif
                                   @if($disabled == true) disabled="" @endif>
                            <label class="pl-2" for="verify-photo">{{__('common.VerifyPhoto')}}</label>
                        </div>
                    </div>
                    @if($disabled == true)
                        <div class="form-group col-lg-12 mb-3 mt-3">
                            <div class="form-check">
                                <label class="font-weight-bold">{{__('common.Comment')}}:</label>

                            </div>
                        </div>
                        <div class="form-group col-lg-12 mb-3">
                            <div class="form-check">
                                <div>
                                    {{$investor->verification ? $investor->verification->comment: ''}}
                                </div>

                            </div>
                        </div>

                    @endif
                    @if($disabled == false)
                        <div class="form-group row ml-1 mb-3 mt-3">
                            <button type="submit" name="action" value="mark_verified"
                                    class="btn btn-success float-left ml-1">{{__('common.MarkVerified')}}</button>
                            <button type="submit" name="action" value="request_documents"
                                    class="btn btn-cyan float-left ml-1">{{__('common.RequestDocuments')}}</button>
                            <button type="submit" name="action" value="reject_verification"
                                    class="btn btn-danger float-left ml-1">{{__('common.RejectVerification')}}</button>
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="comment">{{__('common.Comment')}}</label>
                        </div>
                        <div class="form-group col-lg-12 mb-3">

                            <textarea class="form-control w-100" name="comment" id="comment" cols="30"
                                      rows="5">{{$investor->verification ? $investor->verification->comment: ''}}</textarea>
                        </div>

                    @endif
                </div>
            </form>


        </div>
    </div>

    <div class="col-3">

        <h3 class="pt-4 pl-4">{{__('common.Download/View')}}:</h3>

        <div class="card-body">
            @foreach($investor->documents as $document)
                <div class="row pl-3 pr-3">

                    <a href="{{ route('file.get', $document->file_id) }}"
                       class="" target="_blank">
                        {{$document->documentType->name}}
                        @if($document->document_type_id == 1)
                            @if($loop->iteration == 1)
                                front
                            @endif
                            @if($loop->iteration == 2)
                                back
                            @endif
                        @endif
                    </a>

                </div>
            @endforeach
        </div>
    </div>
    @if($task && $task->status === $processingStatus)
        <div class="col-3">
            <form action="{{route('admin.tasks.exit-task', $task->task_id)}}">
                <button class="btn btn-danger">{{__('common.ExitTask')}}</button>
            </form>
        </div>
    @endif
</div>
@push('scripts')
    <script>
        $(document).ready(function () {
            $("input:checkbox").on('click', function() {
                let box = $(this);
                if (box.is(":checked")) {
                    let group = "input:checkbox[name='" + box.attr("name") + "']";
                    $(group).prop("checked", false);
                    box.prop("checked", true);
                } else {
                    box.prop("checked", false);
                }
            });
        });
    </script>
@endpush
