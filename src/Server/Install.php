<?php

namespace Scp\Server;

use Scp\Api\ApiModel;

class Install extends ApiModel
{
    public function path()
    {
        return sprintf(
            'server/%s/install/%s',
            $this->server_id ?: '*',
            $this->id
        );
    }
}
