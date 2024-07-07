<?php

declare(strict_types=1);

namespace App\Controllers\History;

use App\Controllers\Controller;
use App\Services\HistoryService;

class BaseHistoryController extends Controller
{
    /** @var HistoryService */
    protected HistoryService $history;

    /**
     * Inject the service in the base controller.
     *
     * @param HistoryService $history
     */
    public function __construct(HistoryService $history)
    {
        $this->history = $history;
    }
}
