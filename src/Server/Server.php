<?php

namespace Scp\Server;

use Scp\Api;
use Scp\Client\Client;
use Scp\Entity\Entity;
use Scp\Support\Collection;

/**
 * Server storage representation.
 */
class Server
extends Api\ApiModel
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
     * @return Api\ApiQuery <Install>
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
     * DO NOT use this method for auto wipes: use autoWipe instead.
     *
     * @return $this
     */
    public function wipe()
    {
        return $this->patch(['wiped' => '1']);
    }

    /**
     * Wipe the Server on Synergy.
     *
     * Specifies that this action was done automatically instead of manually.
     * This can change the functionality of billing suspensions for VIP clients.
     *
     * @return $this
     *
     * @throws Api\ApiResponseError
     * @throws Exceptions\AutoWipeIgnored
     */
    public function autoWipe()
    {
        try {
            $this->patch([
                'wiped' => '1',
                'auto' => true,
            ]);

            return $this;
        } catch (Api\ApiResponseError $exc) {
            if ($exc->response->data()->ignored) {
                throw new Exceptions\AutoWipeIgnored();
            }

            throw $exc;
        }
    }

    /**
     * @return Api\ApiQuery <Access>
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
     * DO NOT use this method for auto suspensions: use autoSuspend instead.
     *
     * @param string $reason
     *
     * @return $this
     */
    public function suspend($reason)
    {
        $this->access()->patch([
            'is_active' => false,
            'suspension_reason' => $reason,
        ]);

        return $this;
    }

    /**
     * Suspend the Server on Synergy.
     *
     * Specifies that this action was done automatically instead of manually.
     * This can change the functionality of billing suspensions for VIP clients.
     *
     * @param string $reason
     *
     * @return $this
     * @throws Api\ApiResponseError
     * @throws Exceptions\AutoSuspendIgnored
     */
    public function autoSuspend($reason)
    {
        $this->autoSuspendAccess($this->access(), $reason);
    }

    /**
     * @param Access $access
     * @param        $reason
     *
     * @return $this
     * @throws Api\ApiResponseError
     * @throws Exceptions\AutoSuspendIgnored
     */
    public function autoSuspendAccess(Access $access, $reason)
    {
        try {
            $access->patch([
                'is_active' => false,
                'auto' => true,
                'suspension_reason' => $reason,
            ]);

            return $this;
        } catch (Api\ApiResponseError $exc) {
            if ($exc->response->data()->ignored) {
                throw new Exceptions\AutoSuspendIgnored();
            }

            throw $exc;
        }
    }

    /**
     * @param $reason
     *
     * @return Api\Pagination\ApiPaginator
     */
    public function autoSuspendSubClients($reason)
    {
        return $this->accesses()->where('is_primary', false)->get()->each(function (Access $access) use ($reason) {
            $this->autoSuspendAccess($access, $reason);
        });
    }

    /**
     * @return Api\Pagination\ApiPaginator
     */
    public function unsuspendSubClients()
    {
        return $this->accesses()->where('is_primary', false)->get()->each(function (Access $access) {
            $this->unsuspendAccess($access);
        });
    }

    /**
     * Unsuspend the Server on Synergy.
     *
     * @return $this
     */
    public function unsuspend()
    {
        return $this->unsuspendAccess($this->access());
    }

    /**
     * Unsuspend the Server on Synergy.
     *
     * @param Access $access
     *
     * @return $this
     */
    public function unsuspendAccess(Access $access)
    {
        $access->patch(['is_active' => true]);

        return $this;
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

    /**
     * @param Client $client
     */
    public function grantAccess(Client $client)
    {
        $this->api()->post($this->path().'/access', [
            'client' => [
                'id' => $client->getId(),
            ],
            'pxe' => true,
            'ipmi' => true,
            'switch' => true,
        ]);
    }
}
