<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait ListHelpers
{
    /**
     * @param Closure|Builder|null $builder
     *
     * @return Collection|array
     */
    public function lister(Closure|Builder $builder = null): Collection|array
    {
        if ($builder instanceof Closure) {
            $query = $this->newQuery();
            $builder = call_user_func($builder, $query) ?? $query;
        }

        $builder = $builder ?? $this->newQuery();

        if (request()->boolean('pageable')) {
            $page = intval(request('page'));
            $size = intval(request('size'));

            $pagination = $builder->paginate($size, ['*'], 'page', $page);

            if ($pagination->lastPage() < $pagination->currentPage()) {
                $pagination = $builder->paginate($size, ['*'], 'page', $pagination->lastPage());
            }

            $result = [
                'pageable' => true,
                'page' => $pagination->currentPage(),
                'size' => $pagination->perPage(),
                'total' => $pagination->total(),
                'data' => $pagination->items(),
                'more' => $pagination->hasMorePages(),
                'from' => $pagination->firstItem(),
                'to' => $pagination->lastItem(),
            ];
        } else {
            $data = $builder->get()->toArray();
            $result = [
                'pageable' => false,
                'data' => $data,
                'total' => count($data),
            ];
        }

        return $result;
    }
}
