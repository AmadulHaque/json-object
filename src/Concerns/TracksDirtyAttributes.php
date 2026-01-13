<?php

namespace Laravel\JsonObject\Concerns;

trait TracksDirtyAttributes
{
    protected array $original = [];

    public function initializeTracksDirtyAttributes(): void
    {
        $this->syncOriginal();
    }

    protected function syncOriginal(): void
    {
        $this->original = $this->toArray();
    }

    public function dirty(): array
    {
        return array_diff_assoc($this->toArray(), $this->original);
    }
}
