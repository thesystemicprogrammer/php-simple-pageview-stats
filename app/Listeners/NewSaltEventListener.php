<?php

namespace App\Listeners;

Use App\Events\NewSaltEvent;
use App\Services\Consolidator\PageviewRefererConsolidationService;


class NewSaltEventListener
{
    private PageviewRefererConsolidationService $consolidationService;

    public function __construct(PageviewRefererConsolidationService $consolidationService) {
        $this->consolidationService = $consolidationService;
    }

    public function handle(NewSaltEvent $event): void
    {
        $this->consolidationService->consolidatePageviews();
    }
}
