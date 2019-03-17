<?php

namespace Scp\Entity;

use Scp\Api\ApiRepository;

class EntityRepository extends ApiRepository
{
    /**
     * @var string
     */
    protected $class = Entity::class;
}
