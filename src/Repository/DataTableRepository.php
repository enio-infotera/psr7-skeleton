<?php

namespace App\Repository;

use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query;
use RuntimeException;

/**
 * Repository.
 */
final class DataTableRepository implements RepositoryInterface
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * Constructor.
     *
     * @param QueryFactory $queryFactory The query factory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Load data table items.
     *
     * @param Query $query The query
     * @param array $params The parameters
     *
     * @return array The table data
     */
    public function load(Query $query, array $params): array
    {
        $query = $this->buildQuery($query, $params);

        $countQuery = clone $query;
        $countQuery->select(['count' => $countQuery->func()->count('*')], true);
        $countRows = $countQuery->execute()->fetchAll('assoc') ?: [];

        $count = 0;
        foreach ($countRows as $countRow) {
            $count += $countRow['count'] ?: 0;
        }

        $draw = (int)($params['draw'] ?? 1);
        $offset = (int)($params['start'] ?? 1);
        $limit = (int)($params['length'] ?? 10);
        $offset = ($offset < 0 || empty($count)) ? 0 : $offset;

        $query->offset($offset);
        $query->limit($limit);

        return [
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'draw' => $draw,
            'data' => $query->execute()->fetchAll('assoc') ?: [],
        ];
    }

    /**
     * Returns datatable filter as query array.
     *
     * https://datatables.net/manual/server-side
     *
     * @param Query $query The query
     * @param array $params The search paremeters
     *
     * @return Query The query
     */
    private function buildQuery(Query $query, array $params): Query
    {
        $order = (array)($params['order'] ?? []);
        $searchValue = trim($params['search']['value'] ?? '');
        $columns = (array)($params['columns'] ?? []);
        $table = (string)$query->clause('from')[0];
        $fields = $this->getTableFields($table);

        if ($searchValue !== '') {
            $orConditions = [];
            $searchValue = $this->escapeLike($searchValue);

            foreach ($columns as $columnItem) {
                $searchField = (string)$columnItem['data'];

                if ($searchField === '' || empty($columnItem['searchable'])) {
                    continue;
                }

                $searchField = $this->getFieldName($table, $searchField, $fields);
                $orConditions[$searchField . ' LIKE'] = '%' . $searchValue . '%';
            }

            $query->andWhere(function (QueryExpression $exp) use ($orConditions) {
                return $exp->or_($orConditions);
            });
        }

        if (!empty($order)) {
            foreach ($order as $orderItem) {
                $columnIndex = $orderItem['column'];
                $columnName = $columns[$columnIndex]['data'];
                $columnName = $this->getFieldName($table, $columnName, $fields);
                $dir = $orderItem['dir'];

                if ($dir === 'asc') {
                    $query->order($columnName);
                }
                if ($dir === 'desc') {
                    $query->orderDesc($columnName);
                }
            }
        }

        return $query;
    }

    /**
     * Escape like string.
     *
     * @param string $value The string to escape for a like query
     *
     * @throws RuntimeException
     *
     * @return string The escaped string
     */
    private function escapeLike(string $value): string
    {
        $result = str_replace(['%', '_'], ['\%', '\_'], $value);

        if (!is_string($result)) {
            throw new RuntimeException('Escaping query failed');
        }

        return $result;
    }

    /**
     * Get query field name.
     *
     * @param string $table The table name
     * @param string $field The field name
     * @param array $fields The table fields
     *
     * @return string The full field name
     */
    private function getFieldName(string $table, string $field, array $fields): string
    {
        if (isset($fields[$field]) && strpos($field, '.') === false) {
            $field = "$table.$field";
        }

        return $field;
    }

    /**
     * Get table fields.
     *
     * @param string $table The table name
     *
     * @throws RuntimeException
     *
     * @return array The fields
     */
    private function getTableFields(string $table): array
    {
        $query = $this->queryFactory->newSelect('information_schema.columns');
        $query->select(['column_name', 'data_type', 'character_maximum_length']);
        $query->andWhere([
            'table_schema' => $query->newExpr('DATABASE()'),
            'table_name' => $table,
        ]);

        $rows = $query->execute()->fetchAll('assoc');
        if (empty($rows)) {
            throw new RuntimeException(__('Columns not found in table: %s', $table));
        }

        $result = [];
        foreach ($rows as $row) {
            $result[$row['column_name']] = $row;
        }

        return $result;
    }
}
