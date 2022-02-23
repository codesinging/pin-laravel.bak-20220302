<?php
/**
 * Author: codesinging <codesinging@gmail.com>
 * Github: https://github.com/codesinging
 */

namespace App\Support\Model;

use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\Builder as QueryBuild;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;

/**
 * @mixin Builder
 * @mixin QueryBuild
 *
 * @method static Model|object|Builder|null first(array|string $columns = ['*'])
 * @method static Builder withGlobalScope(string $identifier, Scope|Closure $scope)
 * @method static Builder withoutGlobalScope(Scope|string $scope)
 * @method static Builder withoutGlobalScopes(array $scopes = null)
 * @method static Builder whereKey(Model|array|Arrayable|string|integer $id)
 * @method static Builder whereKeyNot(Model|array|Arrayable|string|integer $id)
 * @method static Builder where(Closure|string|array|Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static Model|Builder|null firstWhere(Closure|string|array|Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static Builder orWhere(Closure|array|string|Expression $column, mixed $operator = null, mixed $value = null)
 * @method static Builder latest(string|Expression $column = null)
 * @method static Builder oldest(string|Expression $column = null)
 * @method static Model|Collection|Builder[]|Builder|null find(Model|array|Arrayable|string|integer $id, array $columns = ['*'])
 * @method static Collection findMany(Arrayable|array $ids, array $columns = ['*'])
 * @method static Model|Collection|Builder[]|Builder findOrFail(Model|array|Arrayable|string|integer $id, array $columns = ['*'])
 * @method static Model|Builder findOrNew(Model|array|Arrayable|string|integer $id, array $columns = ['*'])
 * @method static Model|Builder firstOrNew(array $attributes = [], array $values = [])
 * @method static Model|Builder firstOrCreate(array $attributes = [], array $values = [])
 * @method static Model|Builder updateOrCreate(array $attributes = [], array $values = [])
 * @method static Model|Builder firstOrFail(array $columns = ['*'])
 * @method static Model|Builder|mixed firstOr(Closure|array $columns = ['*'], Closure $callback = null)
 * @method static mixed value(string|Expression $column)
 * @method static mixed valueOrFail(string|Expression $column)
 * @method static Collection|Builder[] get(array|string $columns = ['*'])
 * @method static Collection pluck(string|Expression $column, string|null $key = null)
 * @method static LengthAwarePaginator paginate(int|null $perPage = null, array $columns = ['*'], string $pageName = 'page', int|null $page = null)
 * @method static Paginator simplePaginate(int|null $perPage = null, array $columns = ['*'], string $pageName = 'page', int|null $page = null)
 * @method static Model|Builder create(array $attributes = [])
 * @method static Model|Builder forceCreate(array $attributes = [])
 * @method static int update(array $values)
 * @method static int upsert(array $values, array|string $uniqueBy, array|null $update = null)
 * @method static int increment(string|Expression $column, float|int $amount = 1, array $extra = [])
 * @method static int decrement(string|Expression $column, float|int $amount = 1, array $extra = [])
 * @method static Builder with(string|array $relations, string|Closure|null $callback = null)
 * @method static Builder without(string|array $relations)
 * @method static Builder withOnly(string|array $relations)
 * @method static Builder withCasts(array $casts)
 *
 * @method static Builder select(array|mixed $columns = ['*'])
 * @method static Builder addSelect(array|mixed $column)
 * @method static Builder distinct()
 * @method static Builder join(string $table, Closure|string $first, string|null $operator = null, string|null $second = null, string $type = 'inner', bool $where = false)
 * @method static Builder joinWhere(string $table, Closure|string $first, string $operator, string $second, string $type = 'inner')
 * @method static Builder whereColumn(string|array $first, string|null $operator = null, string|null $second = null, string|null $boolean = 'and')
 * @method static Builder orWhereColumn(string|array $first, string|null $operator = null, string|null $second = null)
 * @method static Builder whereRaw(string $sql, mixed $bindings = [], string $boolean = 'and')
 * @method static Builder orWhereRaw(string $sql, mixed $bindings = [])
 * @method static Builder whereIn(string $column, mixed $values, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereIn(string $column, mixed $values)
 * @method static Builder whereNotIn(string $column, mixed $values, string $boolean = 'and')
 * @method static Builder orWhereNotIn(string $column, mixed $values)
 * @method static Builder whereIntegerInRaw(string $column, Arrayable|array $values, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereIntegerInRaw(string $column, Arrayable|array $values)
 * @method static Builder whereIntegerNotInRaw(string $column, Arrayable|array $values, string $boolean = 'and')
 * @method static Builder orWhereIntegerNotInRaw(string $column, Arrayable|array $values)
 * @method static Builder whereNull(string|array $columns, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereNull(string|array $column)
 * @method static Builder whereNotNull(string|array $columns, string $boolean = 'and')
 * @method static Builder whereBetween(string|Expression $column, iterable $values, string $boolean = 'and', bool $not = false)
 * @method static Builder whereBetweenColumns(string $column, array $values, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereBetween(string $column, iterable $values)
 * @method static Builder orWhereBetweenColumns(string $column, array $values)
 * @method static Builder whereNotBetween(string $column, iterable $values, string $boolean = 'and')
 * @method static Builder whereNotBetweenColumns(string $column, array $values, string $boolean = 'and')
 * @method static Builder orWhereNotBetween(string $column, iterable $values)
 * @method static Builder orWhereNotBetweenColumns(string $column, array $values)
 * @method static Builder orWhereNotNull(string $column)
 * @method static Builder whereDate(string $column, string $operator, DateTimeInterface|string|null $value = null, string $boolean = 'and')
 * @method static Builder orWhereTime(string $column, string $operator, DateTimeInterface|string|null $value = null)
 * @method static Builder orWhereMonth(string $column, string $operator, DateTimeInterface|string|null $value = null)
 * @method static Builder orWhereYear(string $column, string $operator, DateTimeInterface|string|int|null $value = null)
 * @method static Builder whereExists(Closure $callback, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereExists(Closure $callback, bool $not = false)
 * @method static Builder whereNotExists(Closure $callback, string $boolean = 'and')
 * @method static Builder orWhereNotExists(Closure $callback)
 * @method static Builder addWhereExistsQuery(Builder $query, string $boolean = 'and', bool $not = false)
 * @method static Builder whereRowValues(array $columns, string $operator, array $values, string $boolean = 'and')
 * @method static Builder orWhereRowValues(array $columns, string $operator, array $values)
 * @method static Builder whereJsonContains(string $column, mixed $value, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereJsonContains(string $column, mixed $value)
 * @method static Builder whereJsonDoesntContain(string $column, mixed $value, string $boolean = 'and')
 * @method static Builder orWhereJsonDoesntContain(string $column, mixed $value)
 * @method static Builder whereJsonLength(string $column, mixed $operator, mixed $value = null, string $boolean = 'and')
 * @method static Builder orWhereJsonLength(string $column, mixed $operator, mixed $value = null)
 * @method static Builder whereFullText(string|string[] $columns, string $value, array $options = [], string $boolean = 'and')
 * @method static Builder orWhereFullText(string|string[] $columns, string $value, array $options = [])
 * @method static Builder groupBy(array|string ...$groups)
 * @method static Builder groupByRaw(string $sql, array $bindings = [])
 * @method static Builder having(Closure|string $column, string|null $operator = null, string|null $value = null, string $boolean = 'and')
 * @method static Builder orHaving(Closure|string $column, string|null $operator = null, string|null $value = null)
 * @method static Builder havingNull(string|array $columns, string $boolean = 'and', bool $not = false)
 * @method static Builder orHavingNull(string $column)
 * @method static Builder havingNotNull(string|array $columns, string $boolean = 'and')
 * @method static Builder orHavingNotNull(string $column)
 * @method static Builder havingBetween(string $column, array $values, string $boolean = 'and', bool $not = false)
 * @method static Builder havingRaw(string $sql, array $bindings = [], string $boolean = 'and')
 * @method static Builder orHavingRaw(string $sql, array $bindings = [])
 * @method static Builder orderBy(Closure|Builder|Expression|string $column, string $direction = 'asc')
 * @method static Builder orderByDesc(Closure|Builder|Expression|string $column)
 * @method static Builder inRandomOrder(string $seed = '')
 * @method static Builder orderByRaw(string $sql, array $bindings = [])
 * @method static Builder skip(int $value)
 * @method static Builder offset(int $value)
 * @method static Builder take(int $value)
 * @method static Builder limit(int $value)
 * @method static Builder forPage(int $page, int $perPage = 15)
 * @method static Builder forPageBeforeId(int $perPage = 15, int|null $lastId = 0, string $column = 'id')
 * @method static Builder forPageAfterId(int $perPage = 15, int|null $lastId = 0, string $column = 'id')
 * @method static Builder reorder(Closure|Builder|Expression|string|null $column = null, string $direction = 'asc')
 * @method static bool insert(array $values)
 * @method static int insertOrIgnore(array $values)
 * @method static int insertGetId(array $values, string|null $sequence = null)
 * @method static int delete(mixed $id = null)
 * @method static void truncate()
 */
trait IdeHelpers
{

}
