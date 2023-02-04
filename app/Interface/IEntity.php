<?php

namespace App\Interface;

interface IEntity {

    public function toArray():array;

    public function getId(): int;

    public function setId(int $id): void;

}
