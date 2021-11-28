<?php
namespace Nubersoft\Admin;

/**
 * @description 
 */
class Controller extends \Nubersoft\SearchEngine
{
    /**
     * @description 
     */
    public function paginate($table, $REQ, $searchCols, $filter = false, $max_range = [5, 10, 20, 50, 100], $orderB = 'ID', $orderH = 'DESC')
    {
        $this->fetch(
            [
                'columns' => $searchCols,
                'sort' => $orderH,
                'order' => $orderB,
                'max_range' => $max_range
            ],
            function ($nQuery, $Pagination, $REQ) {

                if (!empty($REQ['search'])) {
                    $bind = array_fill(0, count($Pagination->columns), '%' . $Pagination->dec(urldecode($REQ['search'])) . '%');

                    foreach ($Pagination->columns as $col) {
                        $where[] = $col . " LIKE ?";
                    }

                    $where = implode(' ', array_merge([" WHERE "], [implode(" OR ", $where)]));
                } else
                    $where = '';

                $sql = "SELECT COUNT(*) as count FROM users {$where}";

                return $Pagination->query($sql, (!empty($bind) ? $bind : null))->getResults(1)['count'];
            },
            function ($REQ, $Pagination, $page, $limit, $orderB, $orderH) use ($filter) {
                if (!empty($REQ['search'])) {
                    $bind = array_fill(0, count($Pagination->columns), '%' . $Pagination->dec(urldecode($REQ['search'])) . '%');

                    foreach ($Pagination->columns as $col) {
                        $where[] = $col . " LIKE ?";
                    }

                    $where = implode(' ', array_merge([" WHERE "], [implode(" OR ", $where)]));
                } else
                    $where = '';

                $sql = "SELECT
                    *
                    FROM
                    users
                    {$where}
                    ORDER BY
                    " . $orderB . " " . $orderH . "
                    LIMIT
                    {$page}, {$limit}";

                $results = $Pagination->query($sql, (!empty($bind) ? $bind : null))->getResults();

                if (is_callable($filter))
                    return $filter($results);

                return (!empty($results)) ? $results : [];
            }
        );
    }
}
