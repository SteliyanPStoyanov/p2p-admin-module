<div class="col-6">
    <form action="{{route('admin.investors.add-document', $investor->investor_id)}}#documents"
          method="POST" class="form-control border-0 w-100" style="height: 50px;"
          enctype="multipart/form-data">
        {{ admin_csrf_field() }}
        <div class="input-group mb-2">
            <div class="custom-file">
                <label for="import_file" class="custom-file-label w-100"
                       style="left:auto; text-align: left">
                </label>
                <input type="file" name="document_file[]" class="custom-file-input" id="customFile"
                       size="4080">
                <input id="document_type_id" name="document_type_id" class="form-control w-100"
                       value="{{\Modules\Common\Entities\DocumentType::DOCUMENT_TYPE_FROM_ADMIN_ID}}"
                       hidden>
            </div>
            <button
                class="btn btn-success default-btn-last"
                type="submit"
                style="margin-left: 1%">
                {{__('common.AddDocument')}}
            </button>
        </div>
    </form>
    <h3 class="card-header pt-4 pl-4"><b>{{__('common.AttachedDocuments')}}</b></h3>
    <ul class="list-group list-group-flush">
        @foreach($investor->documents as $document)
            <li class="list-group-item">
                <a href="{{ route('file.get', $document->file_id) }}"
                   class="" target="_blank">
                    {{$document->name}}
                </a>
            </li>
        @endforeach
    </ul>
</div>
