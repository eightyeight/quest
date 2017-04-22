<?php

namespace Quest;

use PDO;

class PaymentReport extends report
{
    /**
     * @inheritdoc
     */
    public function initData()
    {
        (new dataSource())->loadFixtures();
    }

    /**
     * @inheritdoc
     */
    public function createReport()
    {
        $dbConnection =  new PDO('sqlite:memory', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $this->data = $dbConnection->query("
            SELECT strftime('%Y-%m', p.finish_time) AS month_year, COUNT(p.id) AS payments, SUM(amount) AS sum
            FROM payments p
                LEFT JOIN documents d ON d.payment_id = p.id
                WHERE d.id IS NULL
            GROUP BY month_year
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $template
     */
    public function printResult(string $template)
    {
        switch ($template) {
            case 'csv' :
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=data.csv');

                $output = fopen('php://output', 'w');
                foreach($this->data as $row) {
                    fputcsv($output, $row, "\t");
                }
                break;
            default:
                break;
        }
    }
}