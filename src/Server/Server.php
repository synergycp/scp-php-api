<?php

namespace Scp\Server;

use Scp\Api\ApiModel;
use Scp\Api\ApiQuery;
use Scp\Entity\Entity;
use Scp\Support\Collection;

/**
 * Server storage representation.
 */
class Server extends ApiModel
{
    /**
     * @var int
     */
    const NOT_CACHED = -1;

    /**
     * @var Collection|null
     */
    protected $entities;

    /**
     * @var Access|null|self::NOT_CACHED
     */
    protected $access = self::NOT_CACHED;

    /**
     * @return string
     */
    public function path()
    {
        return sprintf(
            'server/%s',
            $this->id
        );
    }

    /**
     * @return Collection
     */
    public function entities()
    {
        if ($this->entities !== null) {
            return $this->entities;
        }

        $this->entities = Entity::query()
            ->where('server', $this->getId())
            ->all()
            ;

        return $this->entities;
    }

    /**
     * @return ApiQuery <Install>
     */
    public function installs()
    {
        $query = Install::query();

        $query->model()->server_id = $this->id;

        return $query;
    }

    /**
     * Wipe the Server on Synergy.
     *
     * @return $this
     */
    public function wipe()
    {
        return $this->patch(['wiped' => '1']);
    }

    /**
     * @return ApiQuery <Access>
     */
    public function accesses()
    {
        $query = Access::query();

        $query->model()->server = (object) [
            'id' => $this->id,
        ];

        return $query;
    }

    /**
     * The primary Access, if it exists.
     *
     * @return Access|null
     */
    public function access()
    {
        return $this->access = $this->access === self::NOT_CACHED
            ? $this->accesses()->where('is_primary', true)->first()
            : $this->access
            ;
    }

    /**
     * Suspend the Server on Synergy.
     *
     * @return $this
     */
    public function suspend()
    {
        return $this->access()->patch(['is_active' => false]);
    }

    /**
     * Unsuspend the Server on Synergy.
     *
     * @return $this
     */
    public function unsuspend()
    {
        return $this->access()->patch(['is_active' => true]);
    }

    /**
     * Alias for unsuspend.
     *
     * @return $this
     */
    public function activate()
    {
        return $this->unsuspend();
    }
}
