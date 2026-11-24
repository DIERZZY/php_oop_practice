<?php
declare(strict_types=1);

abstract class Employee
{
    protected string $name;
    protected float $baseSalary;

    public function __construct(string $name, float $baseSalary)
    {
        $this->name = $name;
        $this->baseSalary = $baseSalary;
    }

    abstract public function calculateSalary(): float;

    public function getName(): string
    {
        return $this->name;
    }
}

class Manager extends Employee
{
    public function calculateSalary(): float
    {
        return $this->baseSalary * 1.2; // +20% премия
    }
}

class Developer extends Employee
{
    private int $overtimeHours = 0;

    public function setOvertimeHours(int $hours): void
    {
        $this->overtimeHours = $hours;
    }

    public function calculateSalary(): float
    {
        return $this->baseSalary + $this->overtimeHours * 500;
    }
}

class Intern extends Employee
{
    public function calculateSalary(): float
    {
        return $this->baseSalary * 0.5;
    }
}

/**
 * Принимает массив объектов Employee и выводит таблицу «Имя — Зарплата»
 * и общий фонд оплаты труда.
 */
function printPayroll(array $employees): void
{
    echo str_pad("Имя", 20) . "Зарплата" . PHP_EOL;
    echo str_repeat("-", 35) . PHP_EOL;
    $total = 0.0;
    foreach ($employees as $e) {
        $salary = $e->calculateSalary();
        $total += $salary;
        echo str_pad($e->getName(), 20) . number_format($salary, 2, '.', ' ') . PHP_EOL;
    }
    echo str_repeat("-", 35) . PHP_EOL;
    echo "Общий фонд оплаты труда: " . number_format($total, 2, '.', ' ') . PHP_EOL;
}

// Демонстрация
$dev = new Developer("Иван", 80000);
$dev->setOvertimeHours(10);

$employees = [
    new Manager("Анна", 100000),
    $dev,
    new Intern("Пётр", 40000),
];

printPayroll($employees);

/*
 * Почему нельзя создать объект Employee напрямую:
 * Класс Employee объявлен как abstract и содержит абстрактный метод
 * calculateSalary(). Абстрактный класс — это шаблон, у которого нет
 * полной реализации. PHP запрещает инстанцировать abstract-классы
 * (вызов new Employee(...) вызовет Fatal error), потому что для
 * абстрактного метода не существует тела — его обязаны реализовать
 * наследники. Это гарантирует, что каждый конкретный сотрудник умеет
 * считать свою зарплату.
 */