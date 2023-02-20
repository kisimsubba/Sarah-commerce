<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap\Collections;

use Countable;
use ArrayAccess;
use RecursiveIterator;
use Traversable;
use ReturnTypeWillChange;
use RecursiveIteratorIterator;
class NodeCollection implements Countable, ArrayAccess, RecursiveIterator
{
    protected $nodes = [];
    /**
     * @param mixed[] $nodes
     */
    public function __construct($nodes = null)
    {
        if (!(is_array($nodes) || $nodes instanceof Traversable)) {
            $nodes = [];
        }
        foreach ($nodes as $node) {
            $this->nodes[] = $node;
        }
    }
    public function count() : int
    {
        return \count($this->nodes);
    }
    public function offsetExists($offset) : bool
    {
        return isset($this->nodes[$offset]);
    }
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->nodes[$offset]) ? $this->nodes[$offset] : null;
    }
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (\is_null($offset)) {
            $this->nodes[] = $value;
        } else {
            $this->nodes[$offset] = $value;
        }
    }
    /**
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->nodes[$offset]);
    }
    public function getRecursiveIterator() : RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator($this, RecursiveIteratorIterator::SELF_FIRST);
    }
    public function getChildren() : RecursiveIterator
    {
        $nodes = [];
        if ($this->valid()) {
            $nodes = $this->current()->childNodes;
        }
        return new static($nodes);
    }
    public function hasChildren() : bool
    {
        if ($this->valid()) {
            return $this->current()->hasChildNodes();
        }
        return \false;
    }
    #[ReturnTypeWillChange]
    public function current()
    {
        return \current($this->nodes);
    }
    #[ReturnTypeWillChange]
    public function key()
    {
        return \key($this->nodes);
    }
    #[ReturnTypeWillChange]
    public function next()
    {
        return \next($this->nodes);
    }
    #[ReturnTypeWillChange]
    public function rewind()
    {
        return \reset($this->nodes);
    }
    public function valid() : bool
    {
        return \key($this->nodes) !== null;
    }
}
