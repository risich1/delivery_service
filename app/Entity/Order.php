<?php

namespace App\Entity;

use App\Repository\Repository;

class Order {

    const STATUS_PENDING = 'pending',
          STATUS_APPROVED = 'approved',
          STATUS_SUCCESS = 'success';

    protected Repository $repository;

    protected int $id;
    protected int $clientId;
    protected int $sellerId;
    protected int $pointA;
    protected int $pointB;

}
