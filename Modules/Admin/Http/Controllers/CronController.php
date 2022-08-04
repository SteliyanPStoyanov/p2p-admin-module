<?php

namespace Modules\Admin\Http\Controllers;

use App;
use Artisan;
use Illuminate\Http\Request;
use Modules\Common\Console\CommonCommand;
use Modules\Core\Controllers\BaseController;
use Throwable;


class CronController extends BaseController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        return view(
            'admin::crons.list',
            ['commands' => $this->getCommands()]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function execute(Request $request)
    {
        $commandName = $this->getCommandClass($request->input('command'));

        try {
            $command = App::make($commandName);
            if ($command->isInManualExecution()) {
                return back()->with('fail', 'Command is in execution. Please try again later.');
            }

            $status = $command->createRunStatus();

            // TODO: when implemented arguments in commands to implement them in this call
            Artisan::call($command->getName());

            $output = Artisan::output();
        } catch (Throwable $e) {
            return back()->with('fail', 'Sorry, something went wrong. Please contact your administrator');
        } finally {
            $command->closeRunStatus($status);
        }

        return view(
            'admin::crons.output',
            compact('command', 'output')
        );
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function getCommandClass(string $fileName)
    {
        return CommonCommand::NAMESPACE . preg_replace('/\.php$/', '', $fileName);
    }

    /**
     * @return array
     */
    protected function getCommands()
    {
        $commandsDir = module_path('Common') . '/Console';
        $files = scandir($commandsDir);
        $commands = [];

        foreach ($files as $file) {
            //skip current and parent folder entries and non-php files
            if ($file == '.' || $file == '..' || !preg_match('/\.php$/', $file)) {
                continue;
            }
            try {
                //create class
                $class = App::make($this->getCommandClass($file));
                if (empty($class->getSignature())) {
                    continue;
                }
                $commands[] = $class;
            } catch (Throwable $e) {
                continue;
            }
        }

        return $commands;
    }
}
