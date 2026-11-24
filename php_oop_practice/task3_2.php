<?php
declare(strict_types=1);

trait JsonExportTrait
{
    public function export(): string
    {
        return json_encode(get_object_vars($this), JSON_UNESCAPED_UNICODE);
    }

    public function getFormat(): string
    {
        return "JSON";
    }
}

trait XmlExportTrait
{
    public function export(): string
    {
        $xml = "<product>";
        foreach (get_object_vars($this) as $key => $value) {
            $xml .= "<{$key}>" . htmlspecialchars((string)$value) . "</{$key}>";
        }
        $xml .= "</product>";
        return $xml;
    }

    public function getFormat(): string
    {
        return "XML";
    }
}

trait PriceTrait
{
    public function getPriceWithTax(float $tax): float
    {
        return $this->price * (1 + $tax);
    }
}

class Product
{
    use JsonExportTrait, XmlExportTrait, PriceTrait {
        // Конфликт export(): основной — из JsonExportTrait
        JsonExportTrait::export insteadof XmlExportTrait;
        // XML-версию сохраняем под псевдонимом
        XmlExportTrait::export as exportToXml;

        // Конфликт getFormat(): основной — JSON
        JsonExportTrait::getFormat insteadof XmlExportTrait;
        // XML-версию сохраняем под псевдонимом
        XmlExportTrait::getFormat as getXmlFormat;
    }

    public function __construct(
        public string $name,
        public float $price
    ) {}

    // Метод самого класса имеет приоритет над методами трейтов
    public function getFormat(): string
    {
        return "Product format";
    }
}

// Демонстрация
$product = new Product("Ноутбук", 75000.0);

echo "JSON-экспорт: " . $product->export() . PHP_EOL;
echo "XML-экспорт:  " . $product->exportToXml() . PHP_EOL;
echo "getFormat():  " . $product->getFormat() . PHP_EOL;
echo "getXmlFormat(): " . $product->getXmlFormat() . PHP_EOL;
echo "Цена с налогом 20%: " . $product->getPriceWithTax(0.2) . PHP_EOL;

/*
 * Приоритет методов в PHP:
 *   1) метод текущего класса  >
 *   2) метод трейта           >
 *   3) унаследованный метод родителя.
 *
 * Поэтому getFormat() вернёт "Product format": собственная реализация
 * в классе Product перекрывает и JSON-, и XML-версии из трейтов.
 * При этом псевдонимы exportToXml() и getXmlFormat() продолжают
 * работать, так как это отдельные имена методов.
 */