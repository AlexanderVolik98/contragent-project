<?php
namespace App\Service;

use PDO;

class SphinxSearchService
{
    private \PDO $sphinx;

    public function __construct(string $host, int $port, string $db)
    {
//        dd($host, $port, $db);
        $this->sphinx = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s', $host, $port, $db),
            '', ''
        );
        $this->sphinx->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function fetchFiltered(
        string $ident,
        array  $filters,
        int    $page,
        int    $perPage
    ): array {
        [$whereSql, $params] = $this->buildFilterQuery($ident, $filters);

        if ($filters['sort'] === 'state_status') {
            $filters['sort'] = 'state_status_order';
        }

        // Подготовка ORDER BY
        $allowedSortFields = ['state_status_order', 'capital_value', 'registration_date_int'];
        $sortField = in_array($filters['sort'] ?? '', $allowedSortFields) ? $filters['sort'] : 'state_status_order';
        $sortDir = (isset($filters['dir']) && strtolower($filters['dir']) === 'desc') ? 'DESC' : 'ASC';

        $orderSql = "ORDER BY $sortField $sortDir";

        $offset = ($page - 1) * $perPage;

        $subjectTypesStr = $this->defineIndexesForSelect($filters);

        $sql = "SELECT * FROM $subjectTypesStr
              $whereSql
              $orderSql
              LIMIT $offset, $perPage OPTION max_matches = 10000000";

        $stmt = $this->sphinx->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countFiltered(string $ident, array $filters): int
    {
        [$whereSql, $params] = $this->buildFilterQuery($ident, $filters);

        $subjectTypesStr = $this->defineIndexesForSelect($filters);

        $sql = "SELECT COUNT(*) AS cnt FROM $subjectTypesStr $whereSql  OPTION max_matches = 10000000";

        $stmt = $this->sphinx->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    /**
     * @return array{string, array<string, mixed>}
     */
    private function buildFilterQuery(string $ident, array $filters): array
    {
        $where = [];
        $params = [];

        if ($ident !== '') {
            $where[] = 'MATCH(:match)';
            $params['match'] = $ident;
        }

        if (!empty($filters['status'])) {
            $in = array_map(fn($v) => "'" . trim($v) . "'", explode(',', $filters['status']));
            $where[] = 'state_status IN (' . implode(',', $in) . ')';
        }

        if (!empty($filters['region'])) {
            $where[] = 'region_name = :region';
            $params['region'] = $filters['region'];
        }

        if (!empty($filters['dateFrom']) && !empty($filters['dateTo'])) {
            $from = (int)strtotime($filters['dateFrom']);
            $to = (int)strtotime($filters['dateTo']);
            $where[] = "registration_date_int BETWEEN $from AND $to";
        }

        if (isset($filters['capitalMin'])) {
            $where[] = 'capital_value >= :capMin';
            $params['capMin'] = $filters['capitalMin'];
        }
        if (isset($filters['capitalMax'])) {
            $where[] = 'capital_value <= :capMax';
            $params['capMax'] = $filters['capitalMax'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        return [$whereSql, $params];
    }

    private function defineIndexesForSelect(array $filters): string
    {
        $subjectTypesStr = 'all_entities';

        $allowedSubjectTypes = ['company', 'individual'];

        if (isset($filters['subjectType'])) {
            $subjectTypes = explode(',', mb_strtolower($filters['subjectType']));

            sort($allowedSubjectTypes);
            sort($subjectTypes);
            if ($subjectTypes !== $allowedSubjectTypes) {

                $subjectTypesStr = '';
                $lastIndex = count($subjectTypes) - 1;

                foreach ($subjectTypes as $i => $subjectType) {
                    if (in_array($subjectType, $allowedSubjectTypes)) {
                        $subjectTypesStr .= $subjectType . '_idx ';
                    }
                    if ($i !== $lastIndex) {
                        $subjectTypesStr .= ', ';
                    }
                }
            }
        }

        return $subjectTypesStr;
    }
}
