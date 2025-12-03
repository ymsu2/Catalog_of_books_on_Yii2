<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use common\models\Author;
use common\models\Book;

class SeedController extends Controller
{
    public function actionIndex()
    {
        // Создаем администратора
        $admin = new User();
        $admin->username = 'admin';
        $admin->email = 'admin@example.com';
        $admin->setPassword('admin123');
        $admin->generateAuthKey();
        $admin->save();
        
        echo "Создан пользователь admin:admin123\n";

        // Создаем авторов
        $authorsData = [
            'Лев Толстой',
            'Фёдор Достоевский',
            'Антон Чехов',
            'Иван Тургенев',
            'Николай Гоголь',
            'Александр Пушкин',
            'Михаил Лермонтов',
            'Иван Бунин',
            'Александр Блок',
            'Сергей Есенин',
        ];

        $authors = [];
        foreach ($authorsData as $name) {
            $author = new Author();
            $author->name = $name;
            if ($author->save()) {
                $authors[] = $author;
                echo "Создан автор: {$name}\n";
            }
        }

        // Создаем книги
        $booksData = [
            ['Война и мир', 1869, '9785170801182', 'Эпический роман Льва Толстого'],
            ['Анна Каренина', 1877, '9785170825584', 'Роман о трагической любви'],
            ['Преступление и наказание', 1866, '9785170801199', 'Философский роман Достоевского'],
            ['Идиот', 1869, '9785170801205', 'Роман о князе Мышкине'],
            ['Вишнёвый сад', 1904, '9785170801212', 'Пьеса Чехова'],
            ['Дама с собачкой', 1899, '9785170801229', 'Рассказ Чехова'],
            ['Отцы и дети', 1862, '9785170801236', 'Роман Тургенева'],
            ['Мёртвые души', 1842, '9785170801243', 'Поэма Гоголя'],
            ['Евгений Онегин', 1833, '9785170801250', 'Роман в стихах Пушкина'],
            ['Герой нашего времени', 1840, '9785170801267', 'Роман Лермонтова'],
        ];

        foreach ($booksData as $index => $bookData) {
            $book = new Book();
            $book->title = $bookData[0];
            $book->year = $bookData[1];
            $book->isbn = $bookData[2];
            $book->description = $bookData[3];
            
            if ($book->save()) {
                // Назначаем авторов книгам
                $authorIndex = $index % count($authors);
                $book->link('authors', $authors[$authorIndex]);
                
                // Некоторым книгам назначаем несколько авторов
                if ($index < 3) {
                    $secondAuthorIndex = ($index + 1) % count($authors);
                    $book->link('authors', $authors[$secondAuthorIndex]);
                }
                
                echo "Создана книга: {$bookData[0]}\n";
            }
        }

        echo "Тестовые данные успешно созданы!\n";
    }
}