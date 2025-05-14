<?php

namespace App\Livewire;

use App\Models\Deposit;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WalletOverview extends BaseWidget
{
    protected static ?int $sort = -2;
    use InteractsWithPageFilters;

    /**
     * @return array|Stat[]
     */
    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
    
        $setting = \Helper::getSetting();
        $depositQuery = Deposit::query();
        $withdrawalQuery = Withdrawal::query();
    
        if(empty($startDate) && empty($endDate)) {
            $depositQuery->whereMonth('created_at', Carbon::now()->month);
        } else {
            $depositQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
    
        $sumDepositMonth = $depositQuery
            ->where('status', 1)
            ->sum('amount');
    
        $discountValue = $sumDepositMonth * 0.02;
        $totalDepositsAfterDiscount = $sumDepositMonth - $discountValue;
    
        $withdrawalQuery->where('status', 1);
    
        if(empty($startDate) && empty($endDate)) {
            $withdrawalQuery->whereMonth('created_at', Carbon::now()->month);
        } else {
            $withdrawalQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
    
        $sumWithdrawalMonth = $withdrawalQuery->sum('amount');
    
        return [
            Stat::make('Lucro Total', \Helper::amountFormatDecimal($totalDepositsAfterDiscount))
                ->description('Total de Lucro')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Saques', \Helper::amountFormatDecimal($sumWithdrawalMonth))
                ->description('Total de saques')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Stat::make('GGR Pago', \Helper::amountFormatDecimal($discountValue))
                ->description('Perrier')
                ->descriptionIcon('heroicon-o-currency-dollar') // Substituído pelo ícone disponível
                ->color('warning'),
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
