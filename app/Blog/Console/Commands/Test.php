<?php

namespace App\Blog\Console\Commands;

use CjsConsole\Command;
use CjsConsole\Input\InputOption;

class Test extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test command';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            //['name_one', null, InputOption::VALUE_NONE, 'name_one', null],//接收报错
            ['name_two', null, InputOption::VALUE_REQUIRED, 'name_two', null],
            ['name_three', null, InputOption::VALUE_OPTIONAL, 'name_three', null],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

}
