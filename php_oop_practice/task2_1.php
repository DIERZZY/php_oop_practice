<?php
declare(strict_types=1);

interface PaymentInterface
{
    public function pay(float $amount): bool;
    public function refund(float $amount): bool;
    public function getMethodName(): string;
}

class CreditCard implements PaymentInterface
{
    private float $limit;
    private float $spent = 0.0;

    public function __construct(float $limit)
    {
        $this->limit = $limit;
    }

    public function pay(float $amount): bool
    {
        if ($this->spent + $amount > $this->limit) {
            return false;
        }
        $this->spent += $amount;
        return true;
    }

    public function refund(float $amount): bool
    {
        $this->spent = max(0.0, $this->spent - $amount);
        return true;
    }

    public function getMethodName(): string
    {
        return "Кредитная карта";
    }
}

class PayPal implements PaymentInterface
{
    private float $balance;

    public function __construct(float $balance)
    {
        $this->balance = $balance;
    }

    public function pay(float $amount): bool
    {
        if ($this->balance < $amount) {
            return false;
        }
        $this->balance -= $amount;
        return true;
    }

    public function refund(float $amount): bool
    {
        $this->balance += $amount;
        return true;
    }

    public function getMethodName(): string
    {
        return "PayPal";
    }
}

class Cash implements PaymentInterface
{
    public function pay(float $amount): bool
    {
        return true; // наличные — всегда успешно
    }

    public function refund(float $amount): bool
    {
        return true;
    }

    public function getMethodName(): string
    {
        return "Наличные";
    }
}

function checkout(PaymentInterface $method, float $amount): void
{
    $ok = $method->pay($amount);
    $status = $ok ? "успешно" : "отклонено";
    echo "[{$method->getMethodName()}] Оплата " . number_format($amount, 2)
        . " руб. — {$status}" . PHP_EOL;
}

// Демонстрация: успешная и неуспешная оплата для каждого класса
echo "--- Кредитная карта (лимит 5000) ---" . PHP_EOL;
$card = new CreditCard(5000);
checkout($card, 3000);   // успешно
checkout($card, 3000);   // отклонено (3000+3000 > 5000)

echo PHP_EOL . "--- PayPal (баланс 2000) ---" . PHP_EOL;
$pp = new PayPal(2000);
checkout($pp, 1500);     // успешно
checkout($pp, 1000);     // отклонено (остаток 500)

echo PHP_EOL . "--- Наличные ---" . PHP_EOL;
$cash = new Cash();
checkout($cash, 100000); // успешно
checkout($cash, 999999); // успешно