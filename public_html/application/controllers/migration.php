<?php defined('BASEPATH') OR exit('No direct script access allowed');

class migration extends MY_Controller
{
    public function migrate()
    {
        $message = 'Completed successfully!';
        if (($version = $this->migration->current()) === FALSE)
        {
            $message = $this->migration->error_string();
        }
        if($version == MIGRATION_VERSION)
        {
            $message = 'Currently migrated.';
        }
        $this->twig->render('admin/migration.twig', array(
            'version'=>$version,
            'message'=>$message
        ));
    }
}