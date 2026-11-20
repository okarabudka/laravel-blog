<?php

namespace App\Filament\Resources\Posts\Widgets;

use App\Models\PostView;
use App\Models\UpvoteDownvote;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class PostOverview extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if ($this->record === null) {
            return [];
        }

        return [
            Stat::make('Views', PostView::where('post_id', '=', $this->record->getKey())->count()),
            Stat::make('Upvotes', UpvoteDownvote::where('post_id', '=', $this->record->getKey())->where('is_upvote', '=', 1)->count()),
            Stat::make('Downvotes', UpvoteDownvote::where('post_id', '=', $this->record->getKey())->where('is_upvote', '=', 0)->count()),
        ];
    }
}
