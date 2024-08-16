<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;

class MonthlyUsersChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Users Overview';

    protected function getData(): array
    {
        $data = Trend::query(
            User::query()
                ->whereDoesntHave('roles', function (Builder $roleQuery) {
                    $roleQuery->where('name', 'admin');
                })
        )->between(
            start: now()->startOfMonth(),
            end: now()->endOfMonth(),
        )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Registered users per day',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
