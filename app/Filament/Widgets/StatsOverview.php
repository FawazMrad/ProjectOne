<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends BaseWidget
{

    public function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $previousWeekStart = Carbon::now()->subWeek()->startOfWeek();

        $currentWeekUserCount = User::query()
            ->whereDoesntHave('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin'))
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekStart->copy()->endOfWeek()])
            ->count();

        $previousWeekUserCount = User::query()
            ->whereDoesntHave('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin'))
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekStart->copy()->endOfWeek()])
            ->count();

        $userIcon = $currentWeekUserCount > $previousWeekUserCount
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
        $userColor = $currentWeekUserCount > $previousWeekUserCount ? 'success' : 'danger';

        $currentWeekEventCount = Event::query()
            ->whereBetween('created_at', [$currentWeekStart, $currentWeekStart->copy()->endOfWeek()])
            ->count();

        $previousWeekEventCount = Event::query()
            ->whereBetween('created_at', [$previousWeekStart, $previousWeekStart->copy()->endOfWeek()])
            ->count();

        $eventIcon = $currentWeekEventCount > $previousWeekEventCount
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';
        $eventColor = $currentWeekEventCount > $previousWeekEventCount ? 'success' : 'danger';

        return [
            Stat::make('Users', User::query()->whereDoesntHave('roles', fn(Builder $roleQuery) => $roleQuery->where('name', 'admin'))->count())
                ->description('All registered users')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->descriptionIcon($userIcon)
                ->color($userColor),
            Stat::make('Events', Event::query()->count())
                ->description('All created events')
                ->chart([7, 2, 2, 15, 3, 17, 4])
                ->descriptionIcon($eventIcon)
                ->color($eventColor),
        ];
    }
}
