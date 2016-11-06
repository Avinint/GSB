<?php

namespace Core\View;

class Helper
{
    protected $slots = array();
    protected $openSlots = array();

    public function start($name)
    {
        if (in_array($name, $this->openSlots)) {
            throw new \InvalidArgumentException(sprintf('Cannot open slot "%s" twice', $name));
        }

        $this->openSlots[] = $name;
        $this->slots[$name] = '';

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * Stops a helper
     *
     * @throws \LogicException for invalid slot
     *
     */
    public function stop()
    {
        if (!$this->openSlots) {
            throw new \LogicException('No slot started.');
        }

        $name = array_pop($this->openSlots);

        $this->slots[$name] = ob_get_clean();
    }

    public function getName()
    {
        return 'slots';
    }


}