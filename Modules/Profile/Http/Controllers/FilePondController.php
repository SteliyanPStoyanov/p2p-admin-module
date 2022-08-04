<?php

namespace Modules\Profile\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Controllers\BaseController;

class FilePondController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        $investor = $this->getInvestor();

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $folder = $investor->investor_id;
            $fileName = $file[0]->getClientOriginalName();
            $file[0]->storeAs('pond-upload/' . $folder, $fileName);
            return Response::make(
                'pond-upload/' . $folder . '/' . $fileName,
                200,
                [
                    'Content-Type' => 'text/plain',
                ]
            );
        }

        return Response::make(
            '',
            500,
            [
                'Content-Type' => 'text/plain',
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request): \Illuminate\Http\Response
    {
        if (Storage::delete($request->getContent())) {
            return Response::make(
                '',
                200,
                [
                    'Content-Type' => 'text/plain',
                ]
            );
        }

        return Response::make(
            '',
            500,
            [
                'Content-Type' => 'text/plain',
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeOldFile(Request $request): \Illuminate\Http\Response
    {
        if (Storage::delete($request['data'])) {
            return Response::make(
                '',
                200,
                [
                    'Content-Type' => 'text/plain',
                ]
            );
        }

        return Response::make(
            '',
            500,
            [
                'Content-Type' => 'text/plain',
            ]
        );
    }

}
