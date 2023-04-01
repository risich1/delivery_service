<?php

namespace App\Interface;

interface IContainer {

    public function has(string $ids);
    public function get(string $id);

}
