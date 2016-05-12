<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 5:32 PM
 */

namespace wp_db_deployment\app\admin\actions;

use wp_db_deployment\app\admin\AdminPage;

abstract class Action
{

    /**
     * Hnadles the action
     */
    public function handle()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
    }

    /**
     * @param bool   $successful
     * @param string $messageIfSuccessful
     * @param string $messageIfNot
     *
     */
    public function redirectToOptionWithMessage($successful, $messageIfSuccessful = 'Setting Successfully Saved', $messageIfNot = 'Validation Failed')
    {
        $url = "";
        if ($successful)
            $url = site_url('wp-admin/options-general.php?page=' . AdminPage::$optionsPageUri . '&settings-updated=true&message=' . urlencode($messageIfSuccessful));
        else {
            $url = site_url('wp-admin/options-general.php?page=' . AdminPage::$optionsPageUri . '&settings-updated=false&message=' . urlencode($messageIfNot));
        }
        wp_redirect($url);
    }
    
    /**
     * Returns the paramers that were posted and expected
     * @param string[] $expected
     *
     * @return array
     */
    protected function requiresSome($expected)
    {
        $paramsWeHave = [];
        foreach($expected as $param)
        {
            if (isset($_POST[$param]))
                $paramsWeHave[$param] = $_POST[$param];
        }
        if(sizeof($paramsWeHave) == 0 )
            wp_die(__('invalid parameters given'));
        return $paramsWeHave;
    }


    /**
     * Makes sure all the parameters are sent
     * @param string[] $expected
     *
     * @return bool
     */
    protected function requiresAll($expected)
    {
        $paramsWeHave = [];
        foreach($expected as $param)
        {
            if (!isset($_POST[$param]))
                wp_die(__('invalid parameters given'));
            else
            {
                $paramsWeHave[$param] = $_POST[$param];
            }

        }
        return $paramsWeHave;
    }
    protected  function requiresFiles($expected)
    {
        $paramsWeHave = [];
        foreach($expected as $param)
        {
            if (!isset($_FILES[$param]))
                wp_die(__('invalid parameters given'));
            else
            {
                $paramsWeHave[$param] = $_FILES[$param];
            }

        }
        return $paramsWeHave;
    }
}