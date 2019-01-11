<?php

namespace Solspace\Freeform\Events\Integrations;

use Solspace\Freeform\Events\CancelableArrayableEvent;
use Solspace\Freeform\Models\IntegrationModel;

class SaveEvent extends CancelableArrayableEvent
{
    /** @var IntegrationModel */
    private $model;

    /** @var bool */
    private $new;

    /**
     * @param IntegrationModel $model
     * @param bool             $new
     */
    public function __construct(IntegrationModel $model, bool $new)
    {
        $this->model = $model;
        $this->new   = $new;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return array_merge(parent::fields(), ['model', 'new']);
    }

    /**
     * @return IntegrationModel
     */
    public function getModel(): IntegrationModel
    {
        return $this->model;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->new;
    }
}
