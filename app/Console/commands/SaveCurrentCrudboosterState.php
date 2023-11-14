<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SaveCurrentCrudboosterState extends Command
{

//    static $CMS_MENUS = "cms_menus";

    static $TABLES = ["cms_menus","cms_menus_privileges","cms_moduls","cms_privileges","cms_privileges_roles"];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crudbooster:saveCurrentState';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $tables = SaveCurrentCrudboosterState::$TABLES;

        foreach ( $tables as $e){
            file_put_contents("database/seeders/current_state/".$e."_state.json", json_encode(DB::table($e)->get()->toArray()));
            $this->info( $e." Completed.");
        }
        $this->info("Success");

        return 0;


    }
}
