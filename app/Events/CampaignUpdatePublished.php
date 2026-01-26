<?php

namespace App\Events;

use App\Models\CampaignUpdate;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignUpdatePublished
{
    use Dispatchable, SerializesModels;

    public CampaignUpdate $update;

    public function __construct(CampaignUpdate $update)
    {
        $this->update = $update;
    }
}
