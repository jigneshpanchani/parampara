<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait QueryTrait {

    public function searchable($query, $request, $fields = [])
    {
        $searchText = $request->input('search');
        if (!empty($searchText)) {
            $query->where(function ($subQuery) use ($fields, $searchText) {
                foreach ($fields as $field) {
                    if (strpos($field, '.') !== false) {
                        $parts = explode('.', $field);
                        if (count($parts) === 2) {
                            // Handle simple relationship, e.g. purchaseOrder.po_number
                            list($relation, $column) = $parts;
                            $subQuery->orWhereHas($relation, function ($q) use ($column, $searchText) {
                                $q->where($column, 'like', "%{$searchText}%");
                            });
                        } elseif (count($parts) === 3) {
                            // Handle nested relationship, e.g. purchaseOrder.company.name
                            list($relation, $nestedRelation, $column) = $parts;
                            $subQuery->orWhereHas($relation, function ($q) use ($nestedRelation, $column, $searchText) {
                                $q->whereHas($nestedRelation, function ($q2) use ($column, $searchText) {
                                    $q2->where($column, 'like', "%{$searchText}%");
                                });
                            });
                        }
                        // Optionally, add further elseif blocks if you need to handle deeper nesting
                    } else {
                        $normalizedSearch = strtolower(str_replace('.', ' ', $searchText));
                        $subQuery->orWhereRaw("REPLACE($field, '.', ' ') LIKE ?", ["%$normalizedSearch%"]);
                        // $subQuery->orWhere($field, 'like', "%{$searchText}%");
                    }
                }
            });
        }
    }

    public function sortable($query, $request)
    {
        if ($request->has('sort_by') && $request->has('sort_direction')) {
            $sortBy = $request->input('sort_by');       // e.g. "purchase_orders.po_number" or "customers.customer_name" or "status"
            $sortDirection = $request->input('sort_direction');
            $model = $query->getModel();
            $baseTable = $model->getTable();

            // Check if sortBy uses dot notation.
            if (strpos($sortBy, '.') !== false) {
                $parts = explode('.', $sortBy);
                if (count($parts) == 2) {
                    // One-level relation, e.g. "purchase_orders.po_number"
                    $relation = $parts[0];       // e.g. "purchase_orders"
                    $finalColumn = $parts[1];      // e.g. "po_number"

                    // Try to get the relation method name.
                    $relationMethod = Str::camel($relation);
                    if (!method_exists($model, $relationMethod)) {
                        // Try singular version if the plural form isn't found.
                        $relationMethod = Str::camel(Str::singular($relation));
                        if (!method_exists($model, $relationMethod)) {
                            // If still not found, fallback to ordering by the base table column.
                            $query->orderBy("{$baseTable}.{$sortBy}", $sortDirection);
                            return;
                        }
                    }

                    $relationInstance = $model->{$relationMethod}();
                    $relatedModel = $relationInstance->getRelated();
                    $relatedTable = $relatedModel->getTable();
                    $foreignKey = $relationInstance->getForeignKeyName();
                    $ownerKey = $relationInstance->getOwnerKeyName();

                    // Build a subquery to fetch the desired column from the related table.
                    $subQuery = DB::table($relatedTable)
                        ->select($finalColumn)
                        ->whereColumn("{$relatedTable}.{$ownerKey}", "{$baseTable}.{$foreignKey}")
                        ->limit(1);

                    $rawSql = "(" . $subQuery->toSql() . ")";
                    $query->orderByRaw($rawSql . " " . $sortDirection);
                    $query->addBinding($subQuery->getBindings(), 'order');
                } elseif (count($parts) == 3) {
                    // Two-level nested relation, e.g. "purchase_orders.company.name"
                    $firstRelation  = $parts[0];   // e.g. "purchase_orders"
                    $secondRelation = $parts[1];   // e.g. "company"
                    $finalColumn    = $parts[2];   // e.g. "name"

                    // Attempt first-level relation: try plural, then singular.
                    $relationMethod1 = Str::camel($firstRelation);
                    if (!method_exists($model, $relationMethod1)) {
                        $relationMethod1 = Str::camel(Str::singular($firstRelation));
                        if (!method_exists($model, $relationMethod1)) {
                            $query->orderBy("{$baseTable}.{$sortBy}", $sortDirection);
                            return;
                        }
                    }
                    $relationInstance1 = $model->{$relationMethod1}();
                    $relatedModel1 = $relationInstance1->getRelated();
                    $table1 = $relatedModel1->getTable();
                    $foreignKey1 = $relationInstance1->getForeignKeyName();
                    $ownerKey1 = $relationInstance1->getOwnerKeyName();

                    // Now get the second (nested) relation from the first related model.
                    $relationMethod2 = Str::camel($secondRelation);
                    if (!method_exists($relatedModel1, $relationMethod2)) {
                        $relationMethod2 = Str::camel(Str::singular($secondRelation));
                        if (!method_exists($relatedModel1, $relationMethod2)) {
                            $query->orderBy("{$baseTable}.{$sortBy}", $sortDirection);
                            return;
                        }
                    }
                    $relationInstance2 = $relatedModel1->{$relationMethod2}();
                    $relatedModel2 = $relationInstance2->getRelated();
                    $table2 = $relatedModel2->getTable();
                    $foreignKey2 = $relationInstance2->getForeignKeyName();
                    $ownerKey2 = $relationInstance2->getOwnerKeyName();

                    // Build inner subquery: select final column from the second related table.
                    $subQuery1 = DB::table($table2)
                        ->select($finalColumn)
                        ->whereColumn("{$table2}.{$ownerKey2}", "{$table1}.{$foreignKey2}")
                        ->limit(1);

                    // Build outer subquery tying the first relation to the base table.
                    $subQuery2 = DB::table($table1)
                        ->select(DB::raw("(" . $subQuery1->toSql() . ")"))
                        ->whereColumn("{$table1}.{$ownerKey1}", "{$baseTable}.{$foreignKey1}")
                        ->limit(1);

                    $rawSql = "(" . $subQuery2->toSql() . ")";
                    $query->orderByRaw($rawSql . " " . $sortDirection);
                    $query->addBinding($subQuery2->getBindings(), 'order');
                } else {
                    // Fallback for deeper nesting.
                    $query->orderBy("{$baseTable}.{$sortBy}", $sortDirection);
                }
            } else {
                // If not using dot notation, simply qualify the base table column.
                $query->orderBy("{$baseTable}.{$sortBy}", $sortDirection);
            }
        } else {
            // Default ordering.
            $model = $query->getModel();
            $baseTable = $model->getTable();
            // $query->orderBy("{$baseTable}.id", 'desc');
        }
    }

    public function searchAndSort($query, $request, $fields = [])
    {
        $this->searchable($query, $request, $fields);
        $this->sortable($query, $request);
    }

    public function getResult($query)
    {
        $perPage = Config::get('constants.perPage');
        return $query->paginate($perPage);
    }

    public function formatIndianRupees($amount)
    {
        $amountStr = number_format($amount, 2, '.', '');
        list($integerPart, $decimalPart) = explode('.', $amountStr);
        $lastThree = substr($integerPart, -3);
        $otherNumbers = substr($integerPart, 0, -3);
        $formattedIntegerPart = $otherNumbers !== ''
            ? preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $otherNumbers) . ',' . $lastThree
            : $lastThree;
        return $formattedIntegerPart . '.' . $decimalPart;
    }

}
