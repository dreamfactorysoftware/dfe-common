<?php namespace DreamFactory\Enterprise\Common\Utility;

use Illuminate\Support\Collection;

class RestrictedCollection extends Collection
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array The keys that should be hidden for arrays.
     */
    protected $hidden = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Impose key restrictions
     *
     * @param array $items
     *
     * @return array
     */
    protected function restrict($items = [])
    {
        $_hidden = $this->getHidden();

        if (empty($items) || empty($_hidden)) {
            return $items;
        }

        $_cleaned = [];

        foreach ($items as $_key => $_value) {
            if (!in_array($_key, $this->hidden)) {
                $_cleaned[$_key] = $_value;
            }
        }

        return $_cleaned;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->restrict(parent::toArray());
    }

    /**
     * Get the hidden keys
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set the hidden keys
     *
     * @param  array $hidden
     *
     * @return $this
     */
    public function setHidden(array $hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Add to hidden keys
     *
     * @param  array|string|null $list
     *
     * @return $this
     */
    public function addHidden($list = null)
    {
        $list = is_array($list) ? $list : func_get_args();

        $this->hidden = array_merge($this->hidden, $list);

        return $this;
    }
}