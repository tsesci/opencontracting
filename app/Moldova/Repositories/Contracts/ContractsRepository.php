<?php

namespace App\Moldova\Repositories\Contracts;


use App\Moldova\Entities\Contracts;
use App\Moldova\Entities\OcdsRelease;
use App\Moldova\Service\StringUtil;
use MongoRegex;

class ContractsRepository implements ContractsRepositoryInterface
{
    /**
     * @var Contracts
     */
    private $contracts;
    /**
     * @var OcdsRelease
     */
    private $ocdsRelease;

    /**
     * ContractsRepository constructor.
     * @param Contracts   $contracts
     * @param OcdsRelease $ocdsRelease
     */
    public function __construct(Contracts $contracts, OcdsRelease $ocdsRelease)
    {
        $this->contracts   = $contracts;
        $this->ocdsRelease = $ocdsRelease;
    }

    /**
     * {@inheritdoc}
     */
    public function getContractsByOpenYear()
    {
        $result = OcdsRelease::raw(function ($collection) {
            return $collection->find([], [
                    "contract.dateSigned" => 1,
                    "_id"                 => 1
                ]
            );
        });

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getContractorsByOpenYear()
    {
        $result = OcdsRelease::raw(function ($collection) {
            return $collection->find([], [
                    "buyer" => 1
                ]
            );
        });

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcuringAgency($type, $limit, $condition, $column)
    {
        $query  = [];
        $filter = [];

        if ($condition !== '') {
            $filter = [
                '$match' => [
                    $column => $condition
                ]
            ];
        }

        if (!empty($filter)) {
            array_push($query, $filter);
        }

        $groupBy = [
            '$group' => [
                '_id'    => '$buyer.name',
                'count'  => ['$sum' => 1],
                'amount' => ['$sum' => ['$sum' => '$contract.value.amount']]
            ]
        ];

        array_push($query, $groupBy);
        $sort = ['$sort' => [$type => - 1]];
        array_push($query, $sort);
        $limit = ['$limit' => $limit];
        array_push($query, $limit);

        $result = OcdsRelease::raw(function ($collection) use ($query) {
            return $collection->aggregate($query);
        });

        return ($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getContractors($type, $limit, $condition, $column)
    {
        $query  = [];
        $filter = [];

        if ($condition !== '') {
            $filter = [
                '$match' => [
                    $column => $condition
                ]
            ];
        }

        $unwind = [
            '$unwind' => '$award'
        ];
        array_push($query, $unwind);

        if (!empty($filter)) {
            array_push($query, $filter);
        }

        $groupBy =
            [
                '$group' => [
                    '_id'    => '$award.suppliers.name',
                    'count'  => ['$sum' => 1],
                    'amount' => ['$sum' => ['$sum' => '$contract.value.amount']]
                ]
            ];

        array_push($query, $groupBy);
        $sort = ['$sort' => [$type => - 1]];
        array_push($query, $sort);
        $limit = ['$limit' => $limit];
        array_push($query, $limit);

        $result = OcdsRelease::raw(function ($collection) use ($query) {
            return $collection->aggregate($query);
        });

        return ($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalContractAmount()
    {
        $groupBy =
            [
                '$group' => [
                    '_id'    => null,
                    'amount' => ['$sum' => ['$sum' => '$contract.value.amount']]
                ]
            ];

        $result = OcdsRelease::raw(function ($collection) use ($groupBy) {
            return $collection->aggregate($groupBy);
        });

        return ($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getGoodsAndServices($type, $limit, $condition, $column)
    {
        $query  = [];
        $filter = [];

        if ($condition !== '') {
            $filter = [
                '$match' => [
                    $column => $condition
                ]
            ];
        }

        $unwind = [
            '$unwind' => '$award'
        ];
        array_push($query, $unwind);

        if (!empty($filter)) {
            array_push($query, $filter);
        }

        $groupBy =
            [
                '$group' => [
                    '_id'    => '$award.items.classification.description',
                    'count'  => ['$sum' => 1],
                    'amount' => ['$sum' => ['$sum' => '$contract.value.amount']]
                ]
            ];

        array_push($query, $groupBy);
        $sort = ['$sort' => [$type => - 1]];
        array_push($query, $sort);
        $limit = ['$limit' => $limit];
        array_push($query, $limit);

        $result = OcdsRelease::raw(function ($collection) use ($query) {
            return $collection->aggregate($query);
        });

        return ($result);
    }

    /**
     * {@inheritdoc}
     */
    public function getContractsList($params)
    {
        $orderIndex = $params['order'][0]['column'];
        $ordDir     = $params['order'][0]['dir'];
        $column     = $params['columns'][$orderIndex]['data'];
        $startFrom  = $params['start'];
        $ordDir     = (strtolower($ordDir) == 'asc') ? 1 : - 1;
        $search     = $params['search']['value'];

        return ($this->ocdsRelease
            ->project(['contract.id' => 1, 'contract.title' => 1, 'contract.dateSigned' => 1, 'contract.status' => 1, 'contract.period.endDate' => 1, 'contract.value.amount' => 1, 'award' => 1])
            ->where(function ($query) use ($search) {

                if (!empty($search)) {
                    return $query->where('award.items.classification.description', 'like', '%' . $search . '%');
                }

                return $query;
            })
            ->take($params['length'])
            ->skip($startFrom)
            ->orderBy($column, $ordDir)
            ->get());


    }

    /**
     * {@inheritdoc}
     */
    public function getContractorsList($params)
    {
        $orderIndex  = $params['order'][0]['column'];
        $ordDir      = $params['order'][0]['dir'];
        $column      = $params['columns'][$orderIndex]['data'];
        $startFrom   = $params['start'];
        $ordDir      = (strtolower($ordDir) == 'asc') ? 1 : - 1;
        $search      = $params['search']['value'];
        $limitResult = $params['length'];

        $query  = [];
        $filter = [];

        $unwind = [
            '$unwind' => '$award'
        ];
        array_push($query, $unwind);

        if ($search != '') {
            $filter = [
                '$match' => ['award.suppliers.name' => $search]
            ];
        }

        if (!empty($filter)) {
            array_push($query, $filter);
        }

        $groupBy =
            [
                '$group' => [
                    '_id'    => '$award.suppliers.name',
                    'count'  => ['$sum' => 1],
                    'scheme' => ['$addToSet' => '$award.suppliers.additionalIdentifiers.scheme'],
                ]
            ];

        array_push($query, $groupBy);
        $sort = ['$sort' => [$column => $ordDir]];
        array_push($query, $sort);
        $skip = ['$skip' => (int) $startFrom];
        array_push($query, $skip);
        $limit = ['$limit' => (int) $limitResult];
        array_push($query, $limit);

        $result = OcdsRelease::raw(function ($collection) use ($query) {
            return $collection->aggregate($query);
        });

        return ($result['result']);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetailInfo($parameter, $column)
    {
        return ($this->ocdsRelease->where($column, $parameter)->project(['contract' => 1, 'award' => 1])->get());
    }

    /**
     * {@inheritdoc}
     */
    public function getContractDetailById($contractId)
    {
        $result = $this->ocdsRelease->where('contract.id', (int) $contractId)->project(['contract.$' => 1, 'award' => 1, 'tender.id' => 1, 'tender.title' => 1, 'buyer.name' => 1])->first();

        $contract                    = ($result['contract'][0]);
        $contract['tender_title']    = $result['tender']['title'];
        $contract['tender_id']       = $result['tender']['id'];
        $contract['procuringAgency'] = $result['buyer']['name'];

        foreach ($result['award'] as $award) {
            if ($award['id'] === $contract['awardID']) {
                $contract['goods']      = (!empty($award['items'])) ? $award['items'][0]['classification']['description'] : "-";
                $contract['contractor'] = (!empty($award['suppliers'])) ? $award['suppliers'][0]['name'] : "-";
                break;
            }
        }

        return $contract;
    }

    /**
     * {@inheritdoc}
     */
    public function search($search)
    {
        $q          = (!empty($search['q'])) ? $search['q'] : '';
        $contractor = (!empty($search['contractor'])) ? $search['contractor'] : '';
        $agency     = (!empty($search['agency'])) ? $search['agency'] : '';
        $range      = (!empty($search['amount'])) ? explode("-", $search['amount']) : '';

        if (!empty($q)) {
            $search = StringUtil::accentToRegex($q);
            $query  = array('award.items.classification.description' => new MongoRegex("/.*{$search}.*/i"));
            $query2 = array('award.suppliers.name' => new MongoRegex("/.*{$search}.*/i"));
            $query3 = array('buyer.name' => new MongoRegex("/.*{$search}.*/i"));

            $cursor = OcdsRelease::raw(function ($collection) use ($query, $query2, $query3) {
                return $collection->find([
                    '$or' => [
                        $query,
                        $query2,
                        $query3
                    ]
                ]);
            });

            return ($cursor);
        }

        return ($this->ocdsRelease
            ->project(['contract.id' => 1, 'contract.title' => 1, 'contract.dateSigned' => 1, 'contract.status' => 1, 'contract.period.endDate' => 1, 'contract.value.amount' => 1, 'award' => 1])
            ->where(function ($query) use ($contractor, $range, $agency, $search) {
                if (!empty($contractor)) {
                    $query->where('award.suppliers.name', "=", $contractor);
                }

                if (!empty($agency)) {
                    $query->where('buyer.name', "=", $agency);
                }

                if (!empty($search['amount']) && $range[1] != 'Above') {
                    $range[0] = (int) $range[0];
                    $range[1] = (int) $range[1];
                    $query->whereBetween('contract.value.amount', $range);
                }

                return $query;
            })->get());
    }

    /**
     * {@inheritdoc}
     */
    public function getAllContractTitle()
    {
        $query  = [];
        $unwind = [
            '$unwind' => '$award'
        ];
        array_push($query, $unwind);

        $groupBy =
            [
                '$group' => [
                    '_id'   => '$award.suppliers.name',
                    'count' => ['$sum' => 1]
                ]
            ];

        array_push($query, $groupBy);
        $result = OcdsRelease::raw(function ($collection) use ($query) {
            return $collection->aggregate($query);
        });

        return ($result['result']);

    }

    /**
     * {@inheritdoc}
     */
    public function getContractDataForJson($contractId)
    {
        return $this->ocdsRelease->where('contract.id', (int) $contractId)->project(['contract.$' => 1, 'award' => 1, 'tender' => 1, 'buyer' => 1])->first();
    }

}
