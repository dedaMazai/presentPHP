<?php

namespace App\Helpers;

class ExceptionHelper
{
    /**
     * Создаёт новое исключение с указанием первого кадра из вашего приложения,
     * вместо системных файлов из vendor.
     *
     * @param \Throwable $e      Оригинальное исключение
     * @param string     $appPath Часть пути, по которой определяем "наш" код
     *
     * @return \Exception
     */
    public static function rethrowWithLocation(\Throwable $e, string $appPath = '/var/www/project/app/'): \Exception
    {
        $trace = $e->getTrace();
        $userFile = null;
        $userLine = null;

        // Перебираем кадры стектрейса, ищем первый, где путь указывает на папку вашего приложения
        foreach ($trace as $frame) {
            if (!empty($frame['file']) && str_contains($frame['file'], $appPath)) {
                $userFile = $frame['file'];
                $userLine = $frame['line'] ?? null;
                break;
            }
        }

        if ($userFile && $userLine) {
            $message = sprintf(
                'Ошибка в %s (строка: %d): %s',
                $userFile,
                $userLine,
                $e->getMessage()
            );
        } else {
            // Если не нашли ничего относящегося к /var/www/project/app/,
            // указываем файл и строку из оригинального исключения
            $message = sprintf(
                'Ошибка в %s (строка: %d): %s',
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            );
        }

        // Возвращаем новое исключение с уточнённым текстом и сохраняем оригинальное как "предыдущее"
        return new \Exception($message, 0, $e);
    }
}
