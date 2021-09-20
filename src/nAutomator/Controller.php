<?php

namespace Nubersoft\nAutomator;

use \Nubersoft\{
    nAutomator,
    nAutomator\Observer
};

class Controller extends nAutomator
{
    public function createWorkflow($name, $type = 'work', $action = false)
    {
        if (empty($action))
            $action    =    (defined('NBR_ACTION_KEY')? NBR_ACTION_KEY : 'action');
        # Set the name
        $method    =    "set" . ucfirst($type) . "flow";
        # Start the creation
        (new Observer)
            # Set the name of workflow file
            ->{$method}($name)
            # Listen for the "action" key
            ->setActionKey($action)
            # Run the automator
            ->listen();
    }

    public function createBlockflow($name, $action = false)
    {
        $this->createWorkflow($name, 'block', $action);
    }
}
