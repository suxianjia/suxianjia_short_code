<?php
namespace Suxianjia\xianjia_short_code\Util;

use ArrayIterator;
use IteratorAggregate;

/**
 * 基于 Spl 实现的集合类
 */
class Collection implements IteratorAggregate {
    private $items = [];

    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * 获取迭代器
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    /**
     * 添加元素
     * @param mixed $item 元素
     */
    public function add($item) {
        $this->items[] = $item;
    }

    /**
     * 获取所有元素
     * @return array
     */
    public function all() {
        return $this->items;
    }

    /**
     * 过滤元素
     * @param callable $callback 回调函数
     * @return Collection
     */
    public function filter(callable $callback) {
        return new self(array_filter($this->items, $callback));
    }
}