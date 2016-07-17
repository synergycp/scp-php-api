<?php

namespace Scp\Server;
use Scp\Api\ApiModel;

/**
 * Server Access storage.
 */
class Access extends ApiModel
{
    /**
     * @return string
     */
    public function path()
    {
        return sprintf(
            'server/%s/access/%s',
            $this->server_id ?: '*',
            $this->id
        );
    }
}
