<?php

namespace Scp\Api;

abstract class ApiRepository
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var Api
     */
    protected $api;

    public function __construct(Api $api = null)
    {
        $this->api = $api ?: Api::instance();
    }

    /**
     * @param array $info
     *
     * @return ApiModel
     */
    public function make(array $info = [])
    {
        return new $this->class($info, $this->api);
    }

    public function create(array $info = [])
    {
        $item = $this->make($info);

        $item->save();

        return $item;
    }

    public function path()
    {
        return with(new $this->class)->path();
    }

    /**
     * @return ApiQuery
     */
    public function query()
    {
        return new ApiQuery($this->make());
    }

    /**
     * @param int $id
     *
     * @return null|ApiModel
     * @throws ApiError
     */
    public function findById($id)
    {
        return $this->make(['id' => $id])->full();
    }
}
