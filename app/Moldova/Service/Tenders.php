<?php

namespace App\Moldova\Service;


use App\Moldova\Repositories\Tenders\TendersRepositoryInterface;

class Tenders
{
    protected $tender;

    public function __construct(TendersRepositoryInterface $tender)
    {
        $this->tender = $tender;
    }

    /**
     * @return array
     */
    public function getTendersByOpenYear()
    {
        return $this->filterByYear($this->tender->getTendersByOpenYear());

    }

    /**
     * @param $tenders
     * @return array
     */
    protected function filterByYear($tenders)
    {
        $tenderByOpenYear = [];

        foreach ($tenders as $tender) {
            //$year = explode(".", $tender['tender']['tenderPeriod']['startDate']);
            $year = date('Y', strtotime($tender['tender']['tenderPeriod']['startDate']));

            if (array_key_exists($year, $tenderByOpenYear)) {
                $tenderByOpenYear[$year] += 1;
            } else {
                $tenderByOpenYear[$year] = 1;
            }

        }
        ksort($tenderByOpenYear);

        return $tenderByOpenYear;
    }

    /**
     * @param $procuringAgency
     * @return array
     */
    public function getProcuringAgencyTenderByOpenYear($procuringAgency)
    {
        return $this->filterByYear($this->tender->getProcuringAgencyTenderByOpenYear($procuringAgency));
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getAllTenders($params)
    {
        return $this->tender->getAllTenders($params);
    }

    public function getTenderDetailByID($tenderID)
    {
        return $this->tender->getTenderDetailByID($tenderID);
    }

}
