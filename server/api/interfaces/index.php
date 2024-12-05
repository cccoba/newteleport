<?php defined('_JEXEC') or die('Restricted access');
function requireAllFiles($dir)
{
    // Создаем итератор для рекурсивного обхода директорий
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    // Перебираем каждый элемент итератора
    foreach ($iterator as $file) {
        // Проверяем, является ли текущий элемент файлом и имеет ли расширение .php
        if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            // Подключаем файл
            require_once $file->getPathname();
        }
    }
}

// Укажите директорию, с которой начнется рекурсивный обход
$rootDir = __DIR__; // Текущая директория

// Вызов функции для подключения всех PHP-файлов
requireAllFiles($rootDir);
?>