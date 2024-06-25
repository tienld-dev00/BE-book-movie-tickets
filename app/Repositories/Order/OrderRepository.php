<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatus;
use App\Interfaces\Order\OrderRepositoryInterface;
use App\Models\Order;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    /**
     * Apply filter to builder
     * 
     * @param Builder $builder
     * @param string $column
     * @param string $value
     * @param string $operator
     * 
     * @return Builder
     */
    public function applyFilter(
        Builder $builder,
        string $column,
        string $value,
        string $operator = '='
    ) {
        return $builder->where($column, $operator, $value);
    }

    /**
     * Apply search to builder
     * 
     * @param Builder $builder
     * @param string $value
     * 
     * @return Builder
     */
    public function applySearch(
        Builder $builder,
        string $value
    ) {
        return $builder->where(function ($builder) use ($value) {
            $builder->orWhere('id', $value);
            $builder->orWhereHas('user', function ($query) use ($value) {
                $query->where(function ($query) use ($value) {
                    $query->orWhere('stripe_id', $value);
                    $query->orWhere('email', 'like', '%' . $value . '%');
                });
            });
            $builder->orWhereHas('payments', function ($query) use ($value) {
                $query->where('payment_intent_id', $value);
            });
            $builder->orWhere('user_id', $value);
        });
    }

    /**
     * Apply sort to builder
     * 
     * @param Builder $builder
     * @param string $column
     * @param string $direction
     * 
     * @return Builder
     */
    public function applySort(
        Builder $builder,
        string $column,
        string $direction = 'asc'
    ) {
        return $builder->orderBy($column, $direction);
    }

    /**
     * Update or create order
     * 
     * @param array $checkData
     * @param array $updateData
     * 
     * @return Order
     */
    public function updateOrCreate(array $checkData, array $updateData)
    {
        return $this->model->updateOrCreate($checkData, $updateData);
    }

    /**
     * list order have showtime in 30min later
     *
     * @return Array
     */
    public function orderHaveShowtimes30min()
    {
        $currentTime = Carbon::now();
        $addtime = $currentTime->addMinutes(30);
        $addtimeYMDHM = $addtime->format('Y-m-d H:i');

        return $this->model->select('orders.*')
            ->leftJoin('showtimes', 'showtimes.id', '=', 'orders.showtime_id')
            ->where('orders.status', OrderStatus::PAYMENT_SUCCEEDED)
            ->whereRaw("DATE_FORMAT(showtimes.start_time, '%Y-%m-%d %H:%i') = ?", [$addtimeYMDHM])
            ->groupBy('orders.id')
            ->get();
    }
}
