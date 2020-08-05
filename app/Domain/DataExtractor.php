<?php

namespace App\Domain;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;

class DataExtractor
{
    /** @var ConnectionInterface */
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function extract(): iterable
    {
        foreach ($this->fetchRows() as $row) {
            if (preg_match('/GEBURTSDATUM : ([^\s<]+)/', $row->zusatzinformationen, $matches) === 0)
                continue;

            $birthDate = DateTimeImmutable::createFromFormat('d.m.Y', $matches[1]);
            if (!$birthDate) continue;

            yield [
                'id' => $row->id,
                'customerNumber' => $row->customernumber,
                'salutation' => $row->salutation,
                'birthDate' => $birthDate->format('Y-m-d'),
            ];
        }
    }

    private function fetchRows(): iterable
    {
        $query = $this->db->table('adu_inkasso_bonitaetdatensatz', 'aib')
            ->select(['s_user.id', 's_user.customernumber', 's_user.salutation', 'aib.zusatzinformationen'])
            ->join('s_user', 's_user.id', '=', 'aib.customer_id');

        return $query->cursor();
    }
}
