<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration extends MY_Controller
{
    public function migrate()
    {
        $this->message = 'Completed successfully!';
        if (($version = $this->migration->current()) === FALSE)
        {
            $this->message = $this->migration->error_string();
        }
        if($version == MIGRATION_VERSION)
        {
            $this->message = 'Currently migrated.';
        }
        $this->twig->render('admin/migration.twig', array(
            'version'=>$version,
            'message'=>$this->message
        ));
    }
}