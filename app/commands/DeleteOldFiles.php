<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DeleteOldFiles extends Command {

    protected static $STORE_DOWNLOAD_FOLDER = 'downloads/';
    protected static $STORE_UPLOAD_FOLDER = 'uploads/';

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'delete-old-files';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete temporary files into uploads and download folders.';

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
	 * @return mixed
	 */
	public function fire()
	{
        /*
         * Delete uploaded temp files
         * */
        $tempPath = public_path(static::$STORE_UPLOAD_FOLDER);
        $tempHours = intval($_ENV["tempfileexpirationhours"]) * 3600; //convert hours into seconds
        $this->DeleteTempFiles($tempPath, $tempHours);


        /*
         * Delete downloaded temp files
         * */
        $tempPath = public_path(static::$STORE_DOWNLOAD_FOLDER);
        $tempHours = intval($_ENV["tempfileexpirationhours"]) * 3600; //convert hours into seconds
        $this->DeleteTempFiles($tempPath, $tempHours);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}



    protected function DeleteTempFiles($tempPath, $tempHours){
        $now = time();
        if ($handle = opendir($tempPath)) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if(!is_dir($tempPath.$entry)) {
                    if($now - filemtime($tempPath.$entry) >= $tempHours) {
                        //delete files
                        unlink($tempPath.$entry);
                    }
                }
            }

            closedir($handle);
        }
    }
}
