<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#jsonModal-{{$id}}">
    {{ $buttonLabel }}
</button>

<!-- Modal -->
<div class="modal fade" id="jsonModal-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <table class="table table-responsive">
                            @foreach($items as $key=>$item)
                                <tr>
                                    <td>{{$key}}:</td>
                                    <td>{{ is_array($item) ? json_encode($item) : $item }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
