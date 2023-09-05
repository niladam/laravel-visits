<?php

namespace Niladam\LaravelVisits\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JsonException;

class RecordVisitUrlJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private $visitable,
        private readonly array $record,
        private readonly int $count = 1,
    ) {
    }

    public function handle()
    {
        if ($this->count === 1) {
            return $this->model()::create($this->record);
        }

        return $this->model()::insert(
            array_fill(
                start_index: 0,
                count: $this->count,
                value: $this->record
            )
        );
    }

    /**
     * @throws JsonException
     */
    public function uniqueId(): string
    {
        return md5(json_encode($this->record, JSON_THROW_ON_ERROR));
    }

    protected function model()
    {
        return config('laravel-visits.overwrites.model');
    }
}
