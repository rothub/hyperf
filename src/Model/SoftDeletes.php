<?php

namespace RotHub\Hyperf\Model;

use Hyperf\Database\Model\SoftDeletes as HyperfSoftDeletes;
use Psr\EventDispatcher\StoppableEventInterface;
use RotHub\Hyperf\Model\SoftDeletingScope;

trait SoftDeletes
{
    use HyperfSoftDeletes;

    /**
     * @var int 删除后值.
     */
    public $deletedValue = -1;
    /**
     * @var int 恢复后值.
     */
    public $restoredValue = 0;

    /**
     * @inheritdoc
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope);
    }

    /**
     * @inheritdoc
     */
    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($event = $this->fireModelEvent('restoring')) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return false;
            }
        }

        $this->{$this->getDeletedAtColumn()} = $this->restoredValue;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored');

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function trashed()
    {
        return $this->{$this->getDeletedAtColumn()} === $this->deletedValue;
    }

    /**
     * @inheritdoc
     */
    protected function runSoftDelete()
    {
        $query = $this->newModelQuery()->where($this->getKeyName(), $this->getKey());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => $this->deletedValue];

        $this->{$this->getDeletedAtColumn()} = $this->deletedValue;

        if ($this->timestamps && !is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);
    }
}
