<?php
declare(strict_types=1);

trait LoggerTrait
{
    /** @var string[] */
    protected array $logs = [];

    public function log(string $message): void
    {
        $this->logs[] = "[" . date("Y-m-d H:i:s") . "] {$message}";
    }

    /** @return string[] */
    public function getLogs(): array
    {
        return $this->logs;
    }

    public function clearLogs(): void
    {
        $this->logs = [];
    }
}

class UserService
{
    use LoggerTrait;

    public function register(string $username): void
    {
        // ... логика регистрации ...
        $this->log("Зарегистрирован пользователь: {$username}");
    }
}

class OrderService
{
    use LoggerTrait;

    public function createOrder(int $orderId, float $total): void
    {
        // ... логика создания заказа ...
        $this->log("Создан заказ №{$orderId} на сумму {$total} руб.");
    }
}

// Демонстрация
$users = new UserService();
$orders = new OrderService();

$users->register("alice");
$users->register("bob");
$orders->createOrder(101, 4999.90);
$orders->createOrder(102, 1290.00);

echo "--- Журнал UserService ---" . PHP_EOL;
foreach ($users->getLogs() as $line) echo $line . PHP_EOL;

echo PHP_EOL . "--- Журнал OrderService ---" . PHP_EOL;
foreach ($orders->getLogs() as $line) echo $line . PHP_EOL;

// Очищаем журнал только у UserService
$users->clearLogs();
echo PHP_EOL . "После очистки журнала UserService:" . PHP_EOL;
echo "UserService записей: " . count($users->getLogs()) . PHP_EOL;
echo "OrderService записей: " . count($orders->getLogs()) . PHP_EOL;

/*
 * Почему трейт удобнее наследования:
 * UserService и OrderService — логически несвязанные классы (разные
 * предметные области). Если бы логирование выносили в базовый класс,
 * пришлось бы строить искусственную иерархию, а PHP поддерживает
 * только одиночное наследование — и это заняло бы единственный слот
 * родителя. Трейт же «вмешивает» готовую реализацию в любой класс
 * независимо от его места в иерархии, при этом каждый объект получает
 * СВОЮ копию свойства $logs (у UserService и OrderService журналы
 * независимы, что и показано выше).
 */