<?php

namespace HelloPrint\Core;

/**
 * Class OptionsWorker
 * @package HelloPrint\Models
 */
class WorkerOptions
{
    /**
     * @var bool|mixed
     */
    public bool $broker = false;

    /**
     * @var bool|mixed
     */
    public bool $verbose = true;

    /**
     * @var string|false|mixed|null
     */
    public ?string $topic;

    /**
     * @var string|mixed|null
     */
    public ?string $message;

    /**
     * @var int
     */
    public int $ms = 50;

    /**
     * OptionsWorker constructor.
     * @param $args
     */
    public function __construct(?array $args)
    {
        $this->broker   = $args['broker'] ?? false;
        $this->verbose  = $args['verbose'] ?? false;
        $this->message  = $args['message'] ?? false;
        $this->topic    = $args['topic'] ?? false;
        if (isset($args['ms'])) {
            $this->ms = $args['ms'];
        }
    }
}