<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;

class YearlyUsersChart extends ChartWidget
{
    protected static ?string $heading = 'Yearly Users Overview';

    protected function getData(): array
    {
        $data = Trend::query(
            User::query()
                ->whereDoesntHave('roles', function (Builder $roleQuery) {
                    $roleQuery->where('name', 'admin');
                })
        )->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
            ->perMonth()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Registered users per month',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#008080',
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
