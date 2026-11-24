<?php
declare(strict_types=1);

interface ReportFormatterInterface
{
    public function format(array $data): string;
}

interface ReportHeaderInterface
{
    public const SEPARATOR = "==============================";

    public function getHeader(): string;
}

class HtmlFormatter implements ReportFormatterInterface
{
    public function format(array $data): string
    {
        $items = '';
        foreach ($data as $item) {
            $items .= "<li>" . htmlspecialchars((string)$item) . "</li>";
        }
        return "<ul>{$items}</ul>";
    }
}

class CsvFormatter implements ReportFormatterInterface
{
    public function format(array $data): string
    {
        return implode(";", $data);
    }
}

class SalesReport implements ReportFormatterInterface, ReportHeaderInterface
{
    /** @var array<string, float> продукт => сумма */
    private array $sales;

    public function __construct(array $sales)
    {
        $this->sales = $sales;
    }

    public function getHeader(): string
    {
        return "Отчёт о продажах за текущий период";
    }

    public function format(array $data = []): string
    {
        // Если данные не переданы — используем собственные продажи
        $source = $data ?: $this->sales;
        $lines = [];
        foreach ($source as $product => $sum) {
            $lines[] = "Продукт: {$product} — сумма: {$sum} руб.";
        }
        return implode(PHP_EOL, $lines);
    }
}

function renderReport(ReportFormatterInterface $report, array $data = []): void
{
    echo $report->format($data) . PHP_EOL;
}

// Демонстрация
$sales = [
    "Ноутбук" => 75000.0,
    "Мышь"    => 1500.0,
    "Монитор" => 22000.0,
];

$salesReport = new SalesReport($sales);

echo $salesReport->getHeader() . PHP_EOL;
echo ReportHeaderInterface::SEPARATOR . PHP_EOL;
renderReport($salesReport); // один и тот же объект через интерфейс форматтера

echo PHP_EOL . "--- HTML-формат ---" . PHP_EOL;
renderReport(new HtmlFormatter(), $sales);

echo PHP_EOL . "--- CSV-формат ---" . PHP_EOL;
renderReport(new CsvFormatter(), ["Ноутбук", "Мышь", "Монитор"]);

echo PHP_EOL . "Константа SEPARATOR: " . ReportHeaderInterface::SEPARATOR . PHP_EOL;